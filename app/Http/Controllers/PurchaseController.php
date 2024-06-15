<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Ticket;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PaymentSimulation;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Payment;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseReceiptMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PurchaseController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Purchase::class, 'purchase', ['except' => ['store']]);
    }

    public function index(Request $request)
    {
        $query = Purchase::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cart = collect(session()->get('cart', []));
        if ($cart->isEmpty()) {
            return back()->withErrors(['cart' => 'The cart is empty.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nif' => 'required|string|max:9',
            'payment_type' => 'required|string|in:PAYPAL,VISA,MBWAY',
            'payment_reference' => 'required|string|max:255',
        ]);

        $paymentSuccess = $this->validatePayment($request);

        if (!$paymentSuccess) {
            return back()->with('alert-type', 'payment')->with('alert-msg', 'Payment validation failed. Please check your payment details and try again.');
        }

        $now = Carbon::now();
        foreach ($cart as $cartItem) {
            $screening = \App\Models\Screening::find($cartItem['screening_id']);
            if (!$screening) {
                return back()->with('alert-type', 'time')->with('alert-msg', 'Screening not found.');
            }

            $screeningTime = Carbon::createFromFormat('Y-m-d H:i:s', $screening->date . ' ' . $screening->start_time);
            if ($now->greaterThanOrEqualTo($screeningTime->copy()->subMinutes(5))) {
                return back()->with('alert-type', 'time')->with('alert-msg', 'Tickets can only be purchased up to 5 minutes before the movie starts.');
            }
        }

        $discount = 0;
        if (Auth::check()) {
            $configuration = \App\Models\Configuration::first();
            if ($configuration) {
                $discount = $configuration->registered_customer_ticket_discount;
            }
        }

        DB::transaction(function () use ($request, $cart, $discount) {
            $totalFinalPrice = $cart->sum(function ($item) use ($discount) {
                return $item['price'] - $discount;
            });

            $purchase = Purchase::create([
                'customer_id' => Auth::id(),
                'date' => now(),
                'total_price' => $totalFinalPrice,
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'nif' => $request->nif,
                'payment_type' => $request->payment_type,
                'payment_ref' => $request->payment_reference,
            ]);

            $tickets = [];
            foreach ($cart as $cartItem) {
                $finalPrice = $cartItem['price'] - $discount;
                $qrcodeUrl = url('/tickets/validate/' . Str::random(40)); // Generating a unique URL for the QR code

                $ticket = Ticket::create([
                    'screening_id' => $cartItem['screening_id'],
                    'seat_id' => $cartItem['seat_id'],
                    'purchase_id' => $purchase->id,
                    'price' => $finalPrice,
                    'status' => 'valid',
                    'qrcode_url' => $qrcodeUrl,
                ]);

                // Save QR code using Endroid QR Code
                $qrCode = QrCode::create($qrcodeUrl);
                $writer = new PngWriter();
                $qrCodeImage = $writer->write($qrCode)->getString();
                $qrCodePath = 'ticket_qrcodes/qrcode_' . $ticket->id . '.png';
                Storage::put($qrCodePath, $qrCodeImage);

                $tickets[] = $ticket;
            }

            // Generate the receipt PDF and save it
            $pdfReceipt = PDF::loadView('purchases.receipt', compact('purchase'));
            $pdfFilename = 'receipt_' . $purchase->id . '.pdf';
            $pdfPath = storage_path('app/pdf_purchases/' . $pdfFilename);
            $pdfReceipt->save($pdfPath);

            // Update the purchase with the receipt PDF filename
            $purchase->update(['receipt_pdf_filename' => $pdfFilename]);

            // Generate the tickets PDF without saving
            $pdfTickets = PDF::loadView('tickets.pdf', compact('tickets', 'purchase'))->output();

            // Send the email with the receipt and tickets PDFs as attachments
            Mail::to($purchase->customer_email)->send(new PurchaseReceiptMail($purchase, $pdfPath, $pdfTickets));

            session()->forget('cart');
        });

        return redirect()->route('movies.high')->with('alert-type', 'success')->with('alert-msg', 'Purchase completed successfully!');
    }


    /**
     * Validate payment details based on payment type.
     */
    protected function validatePayment(Request $request): bool
    {
        switch ($request->input('payment_type')) {
            case 'VISA':
                return Payment::payWithVisa($request->input('payment_reference'), $request->input('cvv'));

            case 'PAYPAL':
                return Payment::payWithPaypal($request->input('payment_reference'));

            case 'MBWAY':
                return Payment::payWithMBway($request->input('payment_reference'));

            default:
                return false;
        }
    }

    public function download(Purchase $purchase)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->back()->with('alert-type', 'error')->with('alert-msg', 'You must be logged in to download the PDF.');
        }

        // Verifica se o arquivo PDF está presente
        if (is_null($purchase->receipt_pdf_filename)) {
            return redirect()->back()->with('alert-type', 'error')->with('alert-msg', 'No PDF available for download.');
        }

        // Verifica se o usuário é admin
        if (Auth::user()->type === 'A') {
            $canDownload = true;
        } else {
            // Verifica se a compra pertence ao usuário logado
            $canDownload = $purchase->customer_id === Auth::id();
        }

        if (!$canDownload) {
            return redirect()->back()->with('alert-type', 'error')->with('alert-msg', 'Unauthorized action.');
        }

        // Gera o caminho correto do arquivo PDF
        $pdfPath = storage_path('app/' . $purchase->receipt_pdf_filename);

        // Normaliza o caminho do arquivo
        $normalizedPdfPath = realpath($pdfPath);

        // Verifica se o arquivo existe no caminho normalizado
        if ($normalizedPdfPath && file_exists($normalizedPdfPath)) {
            return response()->download($normalizedPdfPath);
        } else {
            return redirect()->back()->with('alert-type', 'error')->with('alert-msg', 'PDF not found!');
        }
    }



}
