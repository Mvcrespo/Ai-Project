<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Purchase;

class ProfileController extends Controller
{

    public function edit(Request $request): View
    {
        $user = User::findOrFail(Auth::id());


        if ($user->type !== 'A' && $user->type !== 'C') {
            abort(403, 'Unauthorized action.');
        }

        $customer = $user->customer;

        $purchases = Purchase::with([
            'tickets.screening.movie',
            'tickets.seat',
            'tickets.screening.theater'
        ])->where('customer_id', $user->id)
          ->orderBy('created_at', 'desc')
          ->paginate(5);

        return view('profile.edit-profile', [
            'user' => $user,
            'customer' => $customer,
            'purchases' => $purchases,
        ]);
    }



    public function editPassword(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }


    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/photos', $filename);
            $data['photo_filename'] = $filename;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();


        if ($user->customer) {
            $customer = $user->customer;
            $customer->nif = $data['nif'] ?? null;
            $customer->payment_type = $data['payment_type'] !== '' ? $data['payment_type'] : null;
            $customer->payment_ref = $data['payment_ref'] ?? null;
            $customer->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
