<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Http\Requests\MovieFormRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class MovieController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Movie::class);
    }

    public function index(): View
    {
        $movies = Movie::orderBy('title')->paginate(20);
        return view('movies.index')->with('movies', $movies);
    }

    public function create(): View
    {
        $newmovie = new Movie();
        return view('movies.create')->with('movie', $newmovie);
    }

    public function store(MovieFormRequest $request): RedirectResponse
    {
        // Validate and handle file upload
        $data = $request->validated();
        if ($request->hasFile('poster_filename')) {
            $file = $request->file('poster_filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/posters', $filename);
            $data['poster_filename'] = $filename;
        }

        // Create new movie
        $newMovie = Movie::create($data);
        $url = route('movies.show', ['movie' => $newMovie]);
        $htmlMessage = "Movie <a href='$url'><u>{$newMovie->title}</u></a> ({$newMovie->id}) has been created successfully!";

        return redirect()->route('movies.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    public function edit(Movie $movie): View
    {
        return view('movies.edit')->with('movie', $movie);
    }

    public function update(MovieFormRequest $request, Movie $movie): RedirectResponse
    {
        // Handle file upload
        $data = $request->validated();
        if ($request->hasFile('poster_filename')) {
            $file = $request->file('poster_filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/posters', $filename);
            $data['poster_filename'] = $filename;
        } else {
            // Keep the original poster_filename if no new file is uploaded
            $data['poster_filename'] = $movie->poster_filename;
        }

        $movie->update($data);
        $url = route('movies.show', ['movie' => $movie]);
        $htmlMessage = "Movie <a href='$url'><u>{$movie->title}</u></a> ({$movie->id}) has been updated successfully!";
        return redirect()->route('movies.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        $movie->delete();

        $alertType = 'success';
        $alertMsg = "Movie {$movie->title} ({$movie->id}) has been deleted successfully!";

        return redirect()->route('movies.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function destroyPoster(Movie $movie): RedirectResponse
    {
        // Check if the movie has a poster filename
        if ($movie->poster_filename) {
            $filePath = 'public/posters/' . $movie->poster_filename;

            // Check if the file exists in the storage
            if (Storage::exists($filePath)) {
                // Delete the file from the storage
                Storage::delete($filePath);
            }

            // Update the movie's poster filename to null
            $movie->poster_filename = null;
            $movie->save();

            return redirect()->back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "Poster of movie {$movie->title} has been deleted.");
        }

        return redirect()->back()
            ->with('alert-type', 'warning')
            ->with('alert-msg', "No poster found to delete for movie {$movie->title}.");
    }


    public function show(Movie $movie): View
    {
        return view('movies.show')->with('movie', $movie);
    }

    public function high(Movie $movie): View
    {
        $movies = Movie::with('screenings')->get();
        $genres = Genre::all();
        return view('movies.high', compact('movies', 'genres'));
    }

    public function highlighted(Request $request)
    {
        $movies = Movie::with('screenings')->get();
        $genres = Genre::all();
        return view('movies.high', compact('movies', 'genres'));
    }

    public function highlightedSearch(Request $request)
    {
        $movies = Movie::with('screenings')->get();
        $genres = Genre::all();
        return view('movies.high', compact('movies', 'genres'));
    }

    public function high_show($id): View
    {
        $movie = Movie::with(['screenings.theater', 'screenings.tickets'])->findOrFail($id);
        return view('movies.high_show', compact('movie'));
    }
}
