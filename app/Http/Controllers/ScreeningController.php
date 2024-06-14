<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Theater;
use App\Models\Screening;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ScreeningController extends Controller
{
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

    public function edit(Screening $screening): View
    {
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();

        // Carregar todas as sessões relacionadas ao mesmo filme e teatro e ordenar por data
        $relatedScreenings = Screening::where('movie_id', $screening->movie_id)
            ->where('theater_id', $screening->theater_id)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('screenings.edit', compact('screening', 'movies', 'theaters', 'relatedScreenings'));
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


    public function show(Screening $screening): View
    {
        $movies = Movie::all()->pluck('title', 'id')->toArray();
        $theaters = Theater::all()->pluck('name', 'id')->toArray();

        // Carregar todas as sessões relacionadas ao mesmo filme e teatro e ordenar por data
        $relatedScreenings = Screening::where('movie_id', $screening->movie_id)
            ->where('theater_id', $screening->theater_id)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('screenings.show', compact('screening', 'movies', 'theaters', 'relatedScreenings'));
    }

    public function destroy(Screening $screening): RedirectResponse
    {
        $screening->delete();

        return redirect()->route('screenings.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Screening deleted successfully!');
    }

    
}
