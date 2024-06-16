<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Theater;
use App\Models\Screening;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ScreeningFormRequest;
use App\Http\Requests\TicketFormRequest;

class ScreeningController extends \Illuminate\Routing\Controller
{

    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Screening::class);
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Screening::select('screenings.*')
            ->join('theaters', 'screenings.theater_id', '=', 'theaters.id')
            ->join('movies', 'screenings.movie_id', '=', 'movies.id')
            ->orderBy('theaters.name')
            ->orderBy('movies.title')
            ->with(['movie', 'theater']);

        if ($search) {
            $query->where('movies.title', 'like', '%' . $search . '%');
        }

        $screenings = $query->get()->unique(function ($screening) {
            return $screening->theater_id . '-' . $screening->movie_id;
        });

        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $screenings->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedScreenings = new LengthAwarePaginator($currentItems, $screenings->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('screenings.index', ['screenings' => $paginatedScreenings]);
    }


    public function create(): View
    {
        $movies = Movie::orderBy('title')->pluck('title', 'id')->toArray();
        $theaters = Theater::orderBy('name')->pluck('name', 'id')->toArray();
        return view('screenings.create', compact('movies', 'theaters'));
    }


    public function store(ScreeningFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        foreach ($data['screenings'] as $screeningData) {
            Screening::create([
                'movie_id' => $data['movie_id'],
                'theater_id' => $data['theater_id'],
                'date' => $screeningData['date'],
                'start_time' => $screeningData['start_time'],
            ]);
        }


        return redirect()->route('screenings.index')
        ->with('alert-type', 'success')
        ->with('alert-msg', 'Screenings created successfully.');

    }



    public function update(ScreeningFormRequest $request, Screening $screening): RedirectResponse
    {
        $modifiedIds = explode(',', $request->input('modified_ids'));

        $movieChanged = $request->has('movie_id') && $request->input('movie_id') != $screening->movie_id;
        $theaterChanged = $request->has('theater_id') && $request->input('theater_id') != $screening->theater_id;

        if ($movieChanged || $theaterChanged) {
            $relatedScreenings = Screening::where('movie_id', $screening->movie_id)
                                            ->where('theater_id', $screening->theater_id)
                                            ->get();

            foreach ($relatedScreenings as $relatedScreening) {
                if ($relatedScreening->tickets()->exists()) {
                    return redirect()->back()
                        ->with('alert-type', 'danger')
                        ->with('alert-msg', 'Cannot change movie or theater for screenings with tickets.');
                }
            }

            foreach ($relatedScreenings as $relatedScreening) {
                $updateData = [];
                if ($movieChanged) {
                    $updateData['movie_id'] = $request->input('movie_id');
                }
                if ($theaterChanged) {
                    $updateData['theater_id'] = $request->input('theater_id');
                }

                if (!empty($updateData)) {
                    $relatedScreening->update($updateData);
                }
            }


            if ($movieChanged) {
                $screening->movie_id = $request->input('movie_id');
            }
            if ($theaterChanged) {
                $screening->theater_id = $request->input('theater_id');
            }
            $screening->save();
        }

        $validatedData = $request->validated();

        foreach ($modifiedIds as $id) {
            $currentScreening = Screening::find($id);
            if (!$currentScreening || $currentScreening->tickets()->exists()) {
                continue;
            }

            $updateData = [];
            if ($request->has("screenings.$id.date") && $request->input("screenings.$id.date") !== $currentScreening->date) {
                $updateData['date'] = $request->input("screenings.$id.date");
            }
            if ($request->has("screenings.$id.start_time") && $request->input("screenings.$id.start_time") !== $currentScreening->start_time) {
                $updateData['start_time'] = $request->input("screenings.$id.start_time");
            }

            if (!empty($updateData)) {
                $currentScreening->update($updateData);
            }
        }

        return redirect()->route('screenings.edit', ['screening' => $screening->id])
                         ->with('alert-type', 'success')
                         ->with('alert-msg', 'Screenings updated successfully.');
    }

    public function edit(Screening $screening, Request $request): View
    {
        $movies = Movie::orderBy('title')->pluck('title', 'id')->toArray();

        $theaters = Theater::orderBy('name')->pluck('name', 'id')->toArray();

        $request->validate([
            'filter_day' => 'nullable|integer|min:1|max:31',
            'filter_month' => 'nullable|integer|min:1|max:12',
            'filter_year' => 'nullable|integer|min:1900|max:2024',
        ]);

        $query = Screening::where('movie_id', $screening->movie_id)
            ->where('theater_id', $screening->theater_id)
            ->join('movies', 'screenings.movie_id', '=', 'movies.id')
            ->orderBy('movies.title', 'asc')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->select('screenings.*');

        if ($request->filled('filter_day')) {
            $query->whereDay('date', $request->filter_day);
        }

        if ($request->filled('filter_month')) {
            $query->whereMonth('date', $request->filter_month);
        }

        if ($request->filled('filter_year')) {
            $query->whereYear('date', $request->filter_year);
        }

        $relatedScreenings = $query->paginate(10)->appends($request->except('page'));

        return view('screenings.edit', compact('screening', 'movies', 'theaters', 'relatedScreenings'));
    }



    public function show(Screening $screening, Request $request): View
    {
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();


        $request->validate([
            'filter_day' => 'nullable|integer|min:1|max:31',
            'filter_month' => 'nullable|integer|min:1|max:12',
            'filter_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        $query = Screening::where('movie_id', $screening->movie_id)
            ->where('theater_id', $screening->theater_id)
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($request->filled('filter_day')) {
            $query->whereDay('date', $request->filter_day);
        }

        if ($request->filled('filter_month')) {
            $query->whereMonth('date', $request->filter_month);
        }

        if ($request->filled('filter_year')) {
            $query->whereYear('date', $request->filter_year);
        }

        $relatedScreenings = $query->paginate(10)->appends($request->except('page'));

        return view('screenings.show', compact('screening', 'movies', 'theaters', 'relatedScreenings'));
    }



    public function destroy(Screening $screening): RedirectResponse
    {
        $relatedScreenings = Screening::where('movie_id', $screening->movie_id)
                                        ->where('theater_id', $screening->theater_id)
                                        ->get();

        foreach ($relatedScreenings as $relatedScreening) {
            if ($relatedScreening->tickets()->exists()) {
                return redirect()->route('screenings.index')
                    ->with('alert-type', 'danger')
                    ->with('alert-msg', 'Cannot delete screenings because there are tickets associated.');
            }
        }

        foreach ($relatedScreenings as $relatedScreening) {
            $relatedScreening->delete();
        }

        return redirect()->route('screenings.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'All related screenings deleted successfully!');
    }

    public function destroySingle(Screening $screening): RedirectResponse
    {
        if ($screening->tickets()->exists()) {
            return redirect()->route('screenings.edit', ['screening' => $screening->id])
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Cannot delete screening because there are tickets associated.');
        }

        $movieId = $screening->movie_id;
        $theaterId = $screening->theater_id;

        $screening->delete();

        $originalScreening = Screening::where('movie_id', $movieId)
                                        ->where('theater_id', $theaterId)
                                        ->first();

        if (!$originalScreening) {
            return redirect()->route('screenings.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', 'Screening deleted successfully!');
        }

        return redirect()->route('screenings.edit', ['screening' => $originalScreening->id])
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Screening deleted successfully!');
    }




    public function selectSession(Request $request)
    {
        $this->authorize('selectSession', Screening::class);

        $search = $request->input('search');
        $today = Carbon::today();
        $fourDaysAgo = Carbon::today()->subDays(2);

        $query = Screening::with(['movie:id,title', 'theater:id,name'])
            ->select('id', 'movie_id', 'theater_id', 'date', 'start_time')
            ->where('date', '>=', $fourDaysAgo)
            ->orderBy('date', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search) || preg_match('/^\d{2}-\d{2}$/', $search) || preg_match('/^\d{4}$/', $search)) {
                    $q->where('date', 'like', "%{$search}%");
                } else {
                    $q->whereHas('movie', function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    });
                    $q->orWhereHas('theater', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        $screenings = $query->get();

        return view('screenings.session_control', compact('screenings', 'search'));
    }

    public function validateTicket(TicketFormRequest $request)
    {
        $ticket = $request->getTicket();

        if (!$ticket) {
            return back()->with('alert-type', 'danger')->with('alert-msg', 'Invalid ticket.');
        }

        $ticket->update(['status' => 'invalid']);

        return back()->with('alert-type', 'success')->with('alert-msg', 'Ticket validated successfully.');
    }


    public function getTheatersForMovie(Movie $movie)
    {
        $theaters = Theater::whereHas('screenings', function ($query) use ($movie) {
            $query->where('movie_id', $movie->id);
        })->pluck('name', 'id');

        return response()->json($theaters);
    }
}
