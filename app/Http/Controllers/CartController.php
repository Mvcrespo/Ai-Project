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

    public function addToCart(Request $request)
    {
        $seatId = $request->input('seat_id');
        $screeningId = $request->input('screening_id');
        $movieTitle = $request->input('movie_title');
        $seat = $request->input('seat');
        $price = $request->input('price');

        $cart = session()->get('cart', collect());

        $cart->push([
            'seat_id' => $seatId,
            'screening_id' => $screeningId,
            'movie_title' => $movieTitle,
            'seat' => $seat,
            'price' => $price,
        ]);

        session()->put('cart', $cart);

        return response()->json(['success' => true]);
    }


    public function removeFromCart(Request $request, $seat_id, $screening_id): RedirectResponse
    {
        $cart = session('cart', collect());
        $cart = $cart->filter(function ($item) use ($seat_id, $screening_id) {
            return !($item['seat_id'] == $seat_id && $item['screening_id'] == $screening_id);
        });

        session(['cart' => $cart]);

        return back()
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Item removed from cart');
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

            DB::transaction(function () use ($customer, $cart) {
                foreach ($cart as $item) {
                    Ticket::create([
                        'screening_id' => $item['screening_id'],
                        'seat_id' => $item['seat_id'],
                        'purchase_id' => null, // Será atualizado quando a compra for finalizada
                        'price' => $item['price'],
                        'status' => 'valid'
                    ]);
                }
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
