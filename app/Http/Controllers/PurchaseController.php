<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Payment;

class PurchaseController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nif' => 'required|string|max:9',
            'payment_type' => 'required|string|in:PAYPAL,VISA,MBWAY',
            'payment_reference' => 'required|string|max:255',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'The cart is empty.']);
        }

        // Process the payment
        $paymentSuccessful = Payment::process($request->payment_type, $request->payment_reference);

        if (!$paymentSuccessful) {
            return back()->withErrors(['payment' => 'Payment could not be processed. Please try again.']);
        }

        // If payment is successful, finalize the purchase
        DB::transaction(function () use ($request, $cart) {
            $totalPrice = array_sum(array_column($cart, 'price'));
            $purchase = Purchase::create([
                'customer_id' => Auth::id(),
                'date' => now(),
                'total_price' => $totalPrice,
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'nif' => $request->nif,
                'payment_type' => $request->payment_type,
                'payment_ref' => $request->payment_reference,
            ]);

            foreach ($cart as $cartItem) {
                Ticket::create([
                    'screening_id' => $cartItem['screening_id'],
                    'seat_id' => $cartItem['seat_id'],
                    'purchase_id' => $purchase->id,
                    'price' => $cartItem['price'],
                    'status' => 'valid',
                ]);
            }

            // Clear the cart
            session()->forget('cart');
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase completed successfully!');
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
