<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserFormRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(): View
    {
        $users = User::whereIn('type', ['A', 'E'])->orderBy('name')->paginate(20);
        return view('users.index')->with('users', $users);
    }
    

    public function create(): View
    {
        $newUser = new User();
        return view('users.create')->with('user', $newUser);
    }

    public function store(UserFormRequest $request): RedirectResponse
    {
        // Handle file upload
        $data = $request->validated();
        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/photos', $filename);
            $data['photo_filename'] = $filename;
        }

        $newUser = User::create($data);
        $url = route('users.show', ['user' => $newUser]);
        $htmlMessage = "User <a href='$url'><u>{$newUser->name}</u></a> ({$newUser->id}) has been created successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function show(User $user): View
    {
        return view('users.show')->with('user', $user);
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        // Handle file upload
        $data = $request->validated();
        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/photos', $filename);
            $data['photo_filename'] = $filename;
        } else {
            // Keep the original photo_filename if no new file is uploaded
            $data['photo_filename'] = $user->photo_filename;
        }

        $user->update($data);
        $url = route('users.show', ['user' => $user]);
        $htmlMessage = "User <a href='$url'><u>{$user->name}</u></a> ({$user->id}) has been updated successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        $alertType = 'success';
        $alertMsg = "User {$user->name} ({$user->id}) has been deleted successfully!";

        return redirect()->route('users.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
    
}
