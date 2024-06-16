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

    public function index(Request $request): View
    {
        $query = Movie::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        $movies = $query->orderBy('title')->paginate(20);
        return view('movies.index')->with('movies', $movies);
    }

    public function create(): View
    {
        $newmovie = new Movie();
        $genres = Genre::all();
        return view('movies.create')->with(['movie' => $newmovie, 'genres' => $genres]);
    }

    public function store(MovieFormRequest $request): RedirectResponse
    {

        $data = $request->validated();
        if ($request->hasFile('poster_filename')) {
            $file = $request->file('poster_filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/posters', $filename);
            $data['poster_filename'] = $filename;
        }


        $newMovie = Movie::create($data);
        $url = route('movies.show', ['movie' => $newMovie]);
        $htmlMessage = "Movie <a href='$url'><u>{$newMovie->title}</u></a> ({$newMovie->id}) has been created successfully!";

        return redirect()->route('movies.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function edit(Movie $movie): View
    {
        $genres = Genre::all();
        return view('movies.edit')->with(['movie' => $movie, 'genres' => $genres]);
    }

    public function update(MovieFormRequest $request, Movie $movie): RedirectResponse
    {

        $data = $request->validated();
        if ($request->hasFile('poster_filename')) {
            $file = $request->file('poster_filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/posters', $filename);
            $data['poster_filename'] = $filename;
        } else {

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
        $today = now()->startOfDay();
        $hasFutureScreenings = $movie->screenings()
            ->where('date', '>=', $today)
            ->exists();

        if ($hasFutureScreenings) {
            $alertType = 'danger';
            $alertMsg = "It is not possible to delete the movie {$movie->title} ({$movie->id}) because it has future screenings!";
        } else {
            try {
                $movie->delete();
                $alertType = 'success';
                $alertMsg = "Movie {$movie->title} ({$movie->id}) has been deleted successfully!";
            } catch (\Exception $error) {
                $url = route('movies.show', ['movie' => $movie]);
                $alertType = 'danger';
                $alertMsg = "It was not possible to delete the movie <a href='$url'><u>{$movie->title}</u></a> ({$movie->id}) because there was an error with the operation!";
            }
        }

        return redirect()->route('movies.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }


    public function destroyPoster(Movie $movie): RedirectResponse
    {

        if ($movie->poster_filename) {
            $filePath = 'public/posters/' . $movie->poster_filename;


            if (Storage::exists($filePath)) {

                Storage::delete($filePath);
            }


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
        $genres = Genre::all();
        return view('movies.show')->with(['movie' => $movie, 'genres' => $genres]);
    }

    public function high(Request $request): View
    {
        $query = $request->input('query');
        $genre = $request->input('genre');

        $movies = Movie::with('screenings')
            ->whereHas('screenings', function($query) {
                $query->whereBetween('date', [now()->startOfDay(), now()->addWeeks(2)->endOfDay()]);
            })
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($query) use ($queryBuilder) {
                    $queryBuilder->where('title', 'like', '%' . strtolower($query) . '%')
                        ->orWhere('synopsis', 'like', '%' . strtolower($query) . '%');
                });
            })
            ->when($genre, function ($queryBuilder) use ($genre) {
                $queryBuilder->where('genre_code', $genre);
            })
            ->paginate(12);

        $genres = Genre::all();

        return view('movies.high', compact('movies', 'genres'));
    }

    public function highlighted(Request $request)
    {
        $query = $request->input('query');
        $genre = $request->input('genre');

        $movies = Movie::with('screenings')
            ->whereHas('screenings', function($query) {
                $query->whereBetween('date', [now()->startOfDay(), now()->addWeeks(2)->endOfDay()]);
            })
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($query) use ($queryBuilder) {
                    $queryBuilder->where('title', 'like', '%' . strtolower($query) . '%')
                        ->orWhere('synopsis', 'like', '%' . strtolower($query) . '%');
                });
            })
            ->when($genre, function ($queryBuilder) use ($genre) {
                $queryBuilder->where('genre_code', $genre);
            })
            ->paginate(10);

        $genres = Genre::all();

        return view('movies.high', compact('movies', 'genres'));
    }

    public function highlightedSearch(Request $request)
    {
        $query = $request->input('query');
        $genre = $request->input('genre');


        $movies = Movie::with('screenings.theater', 'screenings.tickets')
            ->whereHas('screenings', function($query) {
                $query->whereBetween('date', [now()->startOfDay(), now()->addWeeks(2)->endOfDay()]);
            })
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('title', 'like', '%' . strtolower($query) . '%')
                        ->orWhere('synopsis', 'like', '%' . strtolower($query) . '%');
                });
            })
            ->when($genre, function ($queryBuilder) use ($genre) {
                $queryBuilder->where('genre_code', $genre);
            })
            ->paginate(10);

        $genres = Genre::all();

        return view('movies.high', compact('movies', 'genres'));
    }

    public function high_show($id): View
    {
        $movie = Movie::with(['screenings.theater', 'screenings.tickets'])->findOrFail($id);
        foreach ($movie->screenings as $screening) {
            $screening->isSoldOut = $screening->isSoldOut();
        }
        return view('movies.high_show', compact('movie'));
    }
}
