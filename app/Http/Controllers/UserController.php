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

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::withTrashed(); // Incluir usuários soft deletados

        if ($request->has('type') && $request->type != '') {
            $type = $request->type;
            if ($type === 'C' && !auth()->user()->can('viewCustomers', User::class)) {
                abort(403, 'Access denied');
            }
            $query->where('type', $type);
        }

        // Se o usuário autenticado for um funcionário, exclua clientes da lista
        if (auth()->user()->type === 'E') {
            $query->whereIn('type', ['A', 'E']);
        }

        $users = $query->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
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
        $this->authorize('view', $user);
        return view('users.show')->with('user', $user);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

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
        $this->authorize('delete', $user);

        $user->forceDelete();

        $alertType = 'success';
        $alertMsg = "User {$user->name} ({$user->id}) has been deleted permanently!";

        return redirect()->route('users.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function block($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->delete(); // Soft delete
        return redirect()->route('users.index')->with('success', 'User blocked successfully.');
    }

    public function unblock($id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);

        $user->restore(); // Restore soft deleted user
        return redirect()->route('users.index')->with('success', 'User unblocked successfully.');
    }
}
