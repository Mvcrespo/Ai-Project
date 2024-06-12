<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Customer;
use App\Services\PaymentSimulation;

class CartController extends Controller
{
    public function show(): View
    {
        $cart = session('cart', collect());
        return view('cart.show', compact('cart'));
    }

    public function addToCart(Request $request, Ticket $ticket)
    {
        try {
            $cart = session('cart', collect());
            if ($cart->firstWhere('id', $ticket->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket is already in the cart.'
                ]);
            } else {
                $cart->push($ticket);
                session(['cart' => $cart]);
            }

            return response()->json([
                'success' => true,
                'total' => $cart->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to add ticket to cart', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add ticket to cart.'
            ], 500);
        }
    }

    public function removeFromCart(Request $request, Ticket $ticket): RedirectResponse
    {
        $url = route('cart.show', ['ticket' => $ticket]);
        $cart = session('cart', collect());
        if (!$cart->contains('id', $ticket->id)) {
            $alertType = 'warning';
            $htmlMessage = "Ticket <a href='$url'>#{$ticket->id}</a>
                <strong>\"Seat {$ticket->seat->seat_number}\"</strong> was not removed from the cart because it is not in the cart!";
            return back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        } else {
            $cart = $cart->filter(function ($item) use ($ticket) {
                return $item->id !== $ticket->id;
            });
            session(['cart' => $cart]);
            $alertType = 'success';
            $htmlMessage = "Ticket <a href='$url'>#{$ticket->id}</a>
                <strong>\"Seat {$ticket->seat->seat_number}\"</strong> was removed from the cart.";
            return back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');
        return back()
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Shopping Cart has been cleared');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $cart = session('cart', collect());
        if ($cart->isEmpty()) {
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Cart was not confirmed, because cart is empty!");
        } else {
            $customer = Customer::firstOrCreate(
                ['email' => $request->input('email')],
                ['name' => $request->input('name'), 'nif' => $request->input('nif')]
            );

            $paymentDetails = [
                'payment_type' => $request->input('payment_type'),
                'payment_reference' => $request->input('payment_reference')
            ];

            $paymentSimulation = new PaymentSimulation();
            $paymentSuccess = $paymentSimulation->processPayment($paymentDetails);

            if (!$paymentSuccess) {
                return back()
                    ->with('alert-type', 'danger')
                    ->with('alert-msg', "Payment failed. Please try again.");
            }

            $insertTickets = [];
            foreach ($cart as $ticket) {
                $insertTickets[$ticket->id] = [
                    "ticket_id" => $ticket->id,
                    "status" => 'confirmed',
                ];
            }

            DB::transaction(function () use ($customer, $insertTickets) {
                $customer->tickets()->attach($insertTickets);
            });

            $request->session()->forget('cart');
            return redirect()->route('customers.show', ['customer' => $customer])
                ->with('alert-type', 'success')
                ->with('alert-msg', "Tickets have been purchased successfully.");
        }
    }

    public function getCartTotal(Request $request)
    {
        $cart = session('cart', collect());
        return response()->json(['total' => $cart->count()]);
    }
}

