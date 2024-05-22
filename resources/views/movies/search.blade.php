@extends('layouts.main')

@section('main')
    <h1>Search Results</h1>

    @if ($movies->count())
        @foreach ($movies as $movie)
            <div class="movie">
                <h2>{{ $movie->title }}</h2>
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }} poster">
                <p>{{ $movie->synopsis }}</p>
                <a href="{{ route('movies.show', $movie->id) }}">View Details</a>
            </div>
        @endforeach
    @else
        <p>No movies found</p>
    @endif
@endsection
