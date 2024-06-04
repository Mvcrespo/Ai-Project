<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theater;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TheaterFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
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
        return view('theaters.index',compact('theaters'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $theater = new Theater();
        return view('theaters.create'
            ,compact('theater'));
            //->with('courses', $courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function  store(TheaterFormRequest $request): RedirectResponse
    {
        $NewTheater = Theater::create($request->validated());
        if ($request->hasFile('photo_file')) {
            $path = $request->photo_filename->store('public/theaters');
            $NewTheater->photo_filename = basename($path);
            $NewTheater->save();
        }

        $url = route('theaters.show', ['theater' => $NewTheater]);
        $htmlMessage = "Theater <a href='$url'><u>{$NewTheater->name}</u></a> ({$NewTheater->id}) has been created successfully!";
        return redirect()->route('theaters.index')
        ->with('alert-type', 'success')
        ->with('alert-msg', $htmlMessage);

    }


    /**
     * Display the specified resource.
     */
    public function show(Theater $theater): View
    {
        return view('theaters.show', compact('theater'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Theater $theater): View
    {
        return view('theaters.show', compact('theater'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TheaterFormRequest $request, Theater $theater): RedirectResponse
    {
        $theater->update($request->validated());
        if ($request->hasFile('photo_file')) {
            // Delete previous file (if any)
            if ($theater->photo_filename &&
                Storage::fileExists('public/teathers/' . $theater->photo_filename)) {
                    Storage::delete('public/teathers/' . $theater->photo_filename);
            }
            $path = $request->photo_filename->store('public/theaters');
            $theater->user->photo_filename = basename($path);
            $theater->user->save();
        }
        $url = route('theaters.show', ['theater' => $theater]);
        $htmlMessage = "Theater <a href='$url'><u>{$theater->name}</u></a> ({$theater->id}) has been updated successfully!";
        return redirect()->route('theaters.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theater $theater): RedirectResponse
    {
        try {
            $url = route('theaters.show', ['theater' => $theater]);
            $totalScreenings = $theater->screenings()->count();
            $totalSeats = $theater->seats()->count();
            if ($totalScreenings == 0 && $totalSeats == 0) {
                $theater->delete();
                $alertType = 'success';
                $alertMsg = "Theater {$theater->name} ({$theater->id}) has been deleted successfully!";
            } else {
                $alertType = 'warning';
                $screeningsStr = match (true) {
                    $totalScreenings <= 0 => "",
                    $totalScreenings == 1 => "there is 1 screening enrolled in it",
                    $totalScreenings > 1 => "there are $totalScreenings screening enrolled in it",
                };
                $seatsStr = match (true) {
                    $totalSeats <= 0 => "",
                    $totalSeats == 1 => "it already has 1 seat",
                    $totalSeats > 1 => "it already has $totalSeats seats",
                };
                $justification = $screeningsStr && $seatsStr
                    ? "$screeningsStr and $seatsStr"
                    : "$screeningsStr$seatsStr";
                $alertMsg = "Theater <a href='$url'><u>{$theater->name}</u></a> ({$theater->id}) cannot be deleted because $justification.";
            }
        } catch (\Exception $error) {
            $alertType = 'danger';
            $alertMsg = "It was not possible to delete the theater
                            <a href='$url'><u>{$theater->name}</u></a> ({$theater->id})
                            because there was an error with the operation!";
        }
        return redirect()->route('theaters.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
    public function destroyPhoto(Teather $teather): RedirectResponse
    {
        if ($teather->photo_filename) {
            if (Storage::fileExists('public/teathers/' . $teather->photo_filename)) {
                Storage::delete('public/teathers/' . $teather->photo_filename);
            }
            $teather->photo_filename = null;
            $teather->save();
        return redirect()->back()
            ->with('alert-type', 'success')
            ->with('alert-msg', "Photo of teather {$teather->name} has been deleted.");
        }
        return redirect()->back();
    }
}
