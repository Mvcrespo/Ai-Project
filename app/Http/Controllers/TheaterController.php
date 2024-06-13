<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theater;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TheaterFormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TheaterController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Theater::class);
    }

    public function index(Request $request): View
    {
        $theaters = Theater::paginate(10);
        return view('theaters.index', compact('theaters'));
    }

    public function create(): View
    {
        $theater = new Theater();
        $mode = 'create';
        $readonly = false;
        return view('theaters.create', compact('theater', 'mode', 'readonly'));
    }

    public function store(TheaterFormRequest $request): RedirectResponse
    {
        $newTheater = Theater::create($request->validated());
        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('public/theaters');
            $newTheater->photo_filename = basename($path);
            $newTheater->save();
        }

        $url = route('theaters.show', ['theater' => $newTheater]);
        $htmlMessage = "Theater <a href='$url'><u>{$newTheater->name}</u></a> ({$newTheater->id}) has been created successfully!";
        return redirect()->route('theaters.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function show(Theater $theater): View
    {
        $mode = 'show';
        $readonly = true;
        return view('theaters.show', compact('theater', 'mode', 'readonly'));
    }

    public function edit(Theater $theater): View
    {
        $mode = 'edit';
        $readonly = false;
        return view('theaters.edit', compact('theater', 'mode', 'readonly'));
    }

    public function update(TheaterFormRequest $request, Theater $theater): RedirectResponse
    {
        $theater->update($request->validated());
        if ($request->hasFile('photo_file')) {
            // Delete previous file (if any)
            if ($theater->photo_filename && Storage::exists('public/theaters/' . $theater->photo_filename)) {
                Storage::delete('public/theaters/' . $theater->photo_filename);
            }
            $path = $request->file('photo_file')->store('public/theaters');
            $theater->photo_filename = basename($path);
            $theater->save();
        }

        $url = route('theaters.show', ['theater' => $theater]);
        $htmlMessage = "Theater <a href='$url'><u>{$theater->name}</u></a> ({$theater->id}) has been updated successfully!";
        return redirect()->route('theaters.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Theater $theater): RedirectResponse
    {
        try {
            $url = route('theaters.show', ['theater' => $theater]);
            $theater->delete();
            $alertType = 'success';
            $alertMsg = "Theater {$theater->name} ({$theater->id}) has been deleted successfully!";
        } catch (\Exception $error) {
            $alertType = 'danger';
            $alertMsg = "It was not possible to delete the theater <a href='$url'><u>{$theater->name}</u></a> ({$theater->id}) because there was an error with the operation!";
        }
        return redirect()->route('theaters.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function destroyPhoto(Theater $theater): RedirectResponse
    {
        if ($theater->photo_filename) {
            if (Storage::exists('public/theaters/' . $theater->photo_filename)) {
                Storage::delete('public/theaters/' . $theater->photo_filename);
            }
            $theater->photo_filename = null;
            $theater->save();
            return redirect()->back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "Photo of theater {$theater->name} has been deleted.");
        }
        return redirect()->back();
    }
}
