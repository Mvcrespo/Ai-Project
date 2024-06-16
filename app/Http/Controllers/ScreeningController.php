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

        // Obter todos os screenings e agrupar por theater_id e movie_id
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
        $movies = Movie::orderBy('title')->pluck('title', 'id')->toArray();
        $theaters = Theater::orderBy('name')->pluck('name', 'id')->toArray();
        return view('screenings.create', compact('movies', 'theaters'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'theater_id' => 'required|exists:theaters,id',
            'screenings.*.date' => 'required|date|before_or_equal:2024-12-31',
            'screenings.*.start_time' => 'required|date_format:H:i',
        ]);

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



    public function update(Request $request, Screening $screening)
    {
        $modifiedIds = explode(',', $request->input('modified_ids'));

        // Verifica se o filme ou o teatro foi alterado
        $movieChanged = $request->has('movie_id') && $request->input('movie_id') != $screening->movie_id;
        $theaterChanged = $request->has('theater_id') && $request->input('theater_id') != $screening->theater_id;

        // Atualiza o filme e/ou o teatro em todas as sessões se nenhum ticket existir
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

            // Atualiza o filme e/ou o teatro da sessão atual
            if ($movieChanged) {
                $screening->movie_id = $request->input('movie_id');
            }
            if ($theaterChanged) {
                $screening->theater_id = $request->input('theater_id');
            }
            $screening->save();
        }

        foreach ($modifiedIds as $id) {
            $rules = [];
            $messages = [];

            // Recupera a exibição pelo ID
            $currentScreening = Screening::find($id);
            if (!$currentScreening || $currentScreening->tickets()->exists()) {
                continue; // Skip updating if the screening has tickets
            }

            // Adiciona regra de validação para a data apenas se ela foi preenchida e diferente do valor original
            if ($request->has("screenings.$id.date") && $request->input("screenings.$id.date") !== $currentScreening->date) {
                $rules["screenings.$id.date"] = 'date';
                $messages["screenings.$id.date.date"] = "The date for screening $id must be a valid date.";
            }

            // Adiciona regra de validação para o horário de início apenas se ele foi preenchido e diferente do valor original
            if ($request->has("screenings.$id.start_time") && $request->input("screenings.$id.start_time") !== $currentScreening->start_time) {
                $rules["screenings.$id.start_time"] = 'date_format:H:i';
                $messages["screenings.$id.start_time.date_format"] = "The start time for screening $id must be in the format H:i.";
            }

            // Valida apenas os campos que foram modificados
            if (!empty($rules)) {
                $request->validate($rules, $messages);
            }

            // Atualiza os campos modificados
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
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();

        // Validação dos campos de filtro
        $request->validate([
            'filter_day' => 'nullable|integer|min:1|max:31',
            'filter_month' => 'nullable|integer|min:1|max:12',
            'filter_year' => 'nullable|integer|min:1900|max:2024' . date('Y'),
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

    public function getTheatersForMovie(Movie $movie)
{
    $theaters = Theater::whereHas('screenings', function ($query) use ($movie) {
        $query->where('movie_id', $movie->id);
    })->pluck('name', 'id');

    return response()->json($theaters);
}
}
