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

class PurchaseController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cart = collect(session()->get('cart', [])); // Certifique-se de que o carrinho é uma coleção
        if ($cart->isEmpty()) {
            return back()->withErrors(['cart' => 'The cart is empty.']);
        }

        // Validar os campos gerais
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nif' => 'required|string|max:9',
            'payment_type' => 'required|string|in:PAYPAL,VISA,MBWAY',
            'payment_reference' => 'required|string|max:255',
        ]);

        // Validar o pagamento
        $paymentSuccess = $this->validatePayment($request);

        if (!$paymentSuccess) {
            return back()->with('alert-type', 'payment')->with('alert-msg', 'Payment validation failed. Please check your payment details and try again.');
        }

        // Verificar se o horário do filme permite a compra
        $now = Carbon::now();
        foreach ($cart as $cartItem) {
            $screening = \App\Models\Screening::find($cartItem['screening_id']);
            if (!$screening) {
                return back()->with('alert-type', 'time')->with('alert-msg', 'Screening not found.');
            }

            // Combine a data e a hora corretamente
            $screeningTime = Carbon::createFromFormat('Y-m-d H:i:s', $screening->date . ' ' . $screening->start_time);

            if ($now->greaterThanOrEqualTo($screeningTime->copy()->subMinutes(5))) {
                return back()->with('alert-type', 'time')->with('alert-msg', 'Tickets can only be purchased up to 5 minutes before the movie starts.');
            }
        }

        // Inicializar o desconto
        $discount = 0;

        // Aplicar desconto se o usuário estiver autenticado
        if (Auth::check()) {
            $configuration = \App\Models\Configuration::first();
            if ($configuration) {
                $discount = $configuration->registered_customer_ticket_discount;
            }
        }

        // Se o pagamento for bem-sucedido, finalize a compra
        DB::transaction(function () use ($request, $cart, $discount) {
            // Calcular o preço total com desconto
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

            foreach ($cart as $cartItem) {
                $finalPrice = $cartItem['price'] - $discount;
                Ticket::create([
                    'screening_id' => $cartItem['screening_id'],
                    'seat_id' => $cartItem['seat_id'],
                    'purchase_id' => $purchase->id,
                    'price' => $finalPrice,
                    'status' => 'valid',
                ]);
            }

            // Clear the cart
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

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
