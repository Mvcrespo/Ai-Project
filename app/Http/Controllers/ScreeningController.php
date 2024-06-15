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

class ScreeningController extends \Illuminate\Routing\Controller
{

    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Screening::class);
    }

    public function index(): View
    {
        // Obter todos os screenings e agrupar por theater_id e movie_id
        $screenings = Screening::with(['movie', 'theater'])
            ->get()
            ->unique(function ($screening) {
                return $screening->theater_id . '-' . $screening->movie_id;
            });

        // Paginar os resultados agrupados
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
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();
        return view('screenings.create', compact('movies', 'theaters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'theater_id' => 'required|exists:theaters,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'custom' => 'nullable|string',
        ]);

        Screening::create($validated);

        return redirect()->route('screenings.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Screening created successfully!');
    }


    public function update(Request $request, Screening $screening): RedirectResponse
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'theater_id' => 'required|exists:theaters,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'custom' => 'nullable|string',
        ]);

        $screening->update($validated);

        return redirect()->route('screenings.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Screening updated successfully!');
    }


    public function edit(Screening $screening, Request $request): View
    {
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();

        // Validação dos campos de filtro
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

        return view('screenings.edit', compact('screening', 'movies', 'theaters', 'relatedScreenings'));
    }

    public function show(Screening $screening, Request $request): View
    {
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();

        // Validação dos campos de filtro
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
        $screening->delete();

        return redirect()->route('screenings.index')
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

    public function validateTicket(Request $request)
    {
        $this->authorize('validateTicket', Screening::class);

        $request->validate([
            'screening_id' => 'required|exists:screenings,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'qrcode_url' => 'nullable|url',
        ]);

        $screening = Screening::find($request->screening_id);

        $ticket = null;
        if ($request->ticket_id) {
            $ticket = Ticket::find($request->ticket_id);
        } elseif ($request->qrcode_url) {
            $ticket = Ticket::where('qrcode_url', $request->qrcode_url)->first();
        }

        if (!$ticket || $ticket->screening_id != $screening->id || $ticket->status != 'valid') {
            return back()->with('alert-type', 'error')->with('alert-msg', 'Invalid ticket.');
        }

        $ticket->update(['status' => 'invalid']);

        return back()->with('alert-type', 'success')->with('alert-msg', 'Ticket validated successfully.');
    }
}
