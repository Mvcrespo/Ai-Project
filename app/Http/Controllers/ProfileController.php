<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = User::with([
            'purchases' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'purchases.tickets.screening.movie',
            'purchases.tickets.seat',
            'purchases.tickets.screening.theater'
        ])->findOrFail(Auth::id());

        // Verificar se o usuário é do tipo 'A' (admin) ou 'C' (customer)
        if ($user->type !== 'A' && $user->type !== 'C') {
            abort(403, 'Unauthorized action.');
        }

        $customer = $user->customer; // Buscar informações do cliente

        return view('profile.edit-profile', [
            'user' => $user,
            'customer' => $customer,
        ]);
    }

    public function editPassword(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle file upload
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

        // Update customer details if the user is a customer
        if ($user->customer) {
            $customer = $user->customer;
            $customer->nif = $data['nif'] ?? null;
            $customer->payment_type = $data['payment_type'] !== '' ? $data['payment_type'] : null;
            $customer->payment_ref = $data['payment_ref'] ?? null;
            $customer->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
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
