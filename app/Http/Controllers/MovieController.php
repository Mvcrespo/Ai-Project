<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $movies = Movie::with('screenings')->get();
        $genres = Genre::all();
        return view('movies.index', compact('movies', 'genres'));
    }

    public function allmovies(Request $request)
    {
        $query = Movie::query();

        if ($request->has('query') && $request->query('query') != null) {
            $searchQuery = $request->query('query');
            $query->where(function($q) use ($searchQuery) {
                $q->where('title', 'like', '%' . $searchQuery . '%')
                  ->orWhere('synopsis', 'like', '%' . $searchQuery . '%');
            });
        }

        if ($request->has('genre') && $request->query('genre') != null) {
            $query->where('genre_code', $request->query('genre'));
        }

        $movies = $query->get();
        $genres = Genre::all();

        return view('movies.allmovies', compact('movies', 'genres'));
    }

    public function highlighted(Request $request)
    {
        $movies = Movie::with('screenings')->get();
        $genres = Genre::all();
        return view('movies.index', compact('movies', 'genres'));
    }

    public function highlightedSearch(Request $request)
    {
        $movies = Movie::with('screenings')->get();
        $genres = Genre::all();
        return view('movies.index', compact('movies', 'genres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $movie = Movie::with(['screenings.theater', 'screenings.tickets'])->findOrFail($id);
        return view('movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }

    /**
     * Search for movies based on query and genre.
     */
    public function search(Request $request): View
    {
        return $this->allmovies($request);
    }
}
