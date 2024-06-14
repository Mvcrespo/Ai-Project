<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
        $seatIds = $request->input('seat_id');
        $screeningIds = $request->input('screening_id');
        $movieTitles = $request->input('movie_title');
        $seats = $request->input('seat');
        $prices = $request->input('price');

        $cart = session()->get('cart', collect());

        for ($i = 0; $i < count($seatIds); $i++) {
            $itemExists = $cart->contains(function ($item) use ($seatIds, $screeningIds, $i) {
                return $item['seat_id'] == $seatIds[$i] && $item['screening_id'] == $screeningIds[$i];
            });

            if (!$itemExists) {
                $cart->push([
                    'seat_id' => $seatIds[$i],
                    'screening_id' => $screeningIds[$i],
                    'movie_title' => $movieTitles[$i],
                    'seat' => $seats[$i],
                    'price' => $prices[$i],
                ]);
            }
        }

        session()->put('cart', $cart);

        return back()->with('alert-type', 'success')->with('alert-msg', 'Items added to Shopping Cart');
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

    public function getCartTotal(Request $request)
    {
        $cart = session('cart', collect());
        return response()->json(['total' => $cart->count()]);
    }
}
