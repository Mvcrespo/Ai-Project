<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theater;
use App\Models\Seat;
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


        $seatLayout = json_decode($request->input('seat_layout'), true);
        if ($seatLayout) {
            foreach ($seatLayout as $row => $seats) {
                foreach ($seats as $seatNumber) {
                    Seat::create([
                        'theater_id' => $newTheater->id,
                        'row' => $row,
                        'seat_number' => $seatNumber,
                        'custom' => json_encode([]),
                    ]);
                }
            }
        }

        $url = route('theaters.show', ['theater' => $newTheater]);
        $htmlMessage = "Theater <a href='$url'><u>{$newTheater->name}</u></a> ({$newTheater->id}) has been created successfully!";
        return redirect()->route('theaters.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    public function edit(Theater $theater): View
    {
        $mode = 'edit';
        $readonly = false;


        $seats = Seat::where('theater_id', $theater->id)->get();
        $seatLayout = [];

        foreach ($seats as $seat) {
            if (!isset($seatLayout[$seat->row])) {
                $seatLayout[$seat->row] = [];
            }
            $seatLayout[$seat->row][] = $seat->seat_number;
        }

        return view('theaters.edit', compact('theater', 'mode', 'readonly', 'seatLayout'));
    }

    public function show(Theater $theater): View
    {
        $mode = 'show';
        $readonly = true;


        $seats = Seat::where('theater_id', $theater->id)->get();
        $seatLayout = [];

        foreach ($seats as $seat) {
            if (!isset($seatLayout[$seat->row])) {
                $seatLayout[$seat->row] = [];
            }
            $seatLayout[$seat->row][] = $seat->seat_number;
        }

        return view('theaters.show', compact('theater', 'mode', 'readonly', 'seatLayout'));
    }


    public function update(TheaterFormRequest $request, Theater $theater): RedirectResponse
    {
        $theater->update($request->validated());

        if ($request->hasFile('photo_file')) {

            if ($theater->photo_filename && Storage::exists('public/theaters/' . $theater->photo_filename)) {
                Storage::delete('public/theaters/' . $theater->photo_filename);
            }
            $path = $request->file('photo_file')->store('public/theaters');
            $theater->photo_filename = basename($path);
            $theater->save();
        }


        $seatLayout = json_decode($request->input('seat_layout'), true);


        $existingSeats = Seat::withTrashed()->where('theater_id', $theater->id)->get();
        $existingSeatMap = [];
        foreach ($existingSeats as $seat) {
            if (!isset($existingSeatMap[$seat->row])) {
                $existingSeatMap[$seat->row] = [];
            }
            $existingSeatMap[$seat->row][$seat->seat_number] = $seat;
        }


        $newSeatMap = [];
        foreach ($seatLayout as $row => $seats) {
            foreach ($seats as $seatNumber) {
                if (!isset($newSeatMap[$row])) {
                    $newSeatMap[$row] = [];
                }
                $newSeatMap[$row][] = $seatNumber;
            }
        }


        foreach ($newSeatMap as $row => $seats) {
            foreach ($seats as $seatNumber) {
                if (isset($existingSeatMap[$row][$seatNumber])) {
                    $seat = $existingSeatMap[$row][$seatNumber];
                    if ($seat->trashed()) {

                        $seat->restore();
                        $seat->deleted_at = null;
                        $seat->save();
                    }
                } else {

                    Seat::create([
                        'theater_id' => $theater->id,
                        'row' => $row,
                        'seat_number' => $seatNumber,
                    ]);
                }
            }
        }


        foreach ($existingSeatMap as $row => $seats) {
            foreach ($seats as $seatNumber => $seat) {
                if (!isset($newSeatMap[$row]) || !in_array($seatNumber, $newSeatMap[$row])) {

                    $seat->delete();
                }
            }
        }

        $url = route('theaters.show', ['theater' => $theater]);
        $htmlMessage = "Theater <a href='$url'><u>{$theater->name}</u></a> ({$theater->id}) has been updated successfully!";
        return redirect()->route('theaters.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }




    public function destroy(Theater $theater): RedirectResponse
    {
        $today = now()->startOfDay();
        $hasFutureScreenings = $theater->screenings()
            ->where('date', '>=', $today)
            ->exists();

        if ($hasFutureScreenings) {
            $alertType = 'danger';
            $alertMsg = "It is not possible to delete the theater {$theater->name} ({$theater->id}) because it has future screenings!";
        } else {
            try {
                $theater->delete();
                $alertType = 'success';
                $alertMsg = "Theater {$theater->name} ({$theater->id}) has been deleted successfully!";
            } catch (\Exception $error) {
                $url = route('theaters.show', ['theater' => $theater]);
                $alertType = 'danger';
                $alertMsg = "It was not possible to delete the theater <a href='$url'><u>{$theater->name}</u></a> ({$theater->id}) because there was an error with the operation!";
            }
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
