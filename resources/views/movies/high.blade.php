@extends('layouts.main')

@section('header-title', 'Movies On Show')

@section('main')
    <main class="container mx-auto p-4">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="my-4 p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
                <form action="{{ route('movies.highlighted_search') }}" method="GET" class="pb-6 flex space-x-4">
                    <input type="text" name="query" placeholder="Search highlighted movies..." class="px-4 py-2 border rounded-md w-full bg-gray-100 dark:bg-gray-700 dark:text-white" value="{{ request('query') }}">
                    <select name="genre" class="px-4 py-2 border rounded-md bg-gray-100 dark:bg-gray-700 dark:text-white">
                        <option value="">All Genres</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->code }}" {{ request('genre') == $genre->code ? 'selected' : '' }}>{{ $genre->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Search</button>
                </form>

                @include('movies.shared.movies-list', ['movies' => $movies])
            </div>
        </div>
        <div class="mt-4 flex justify-center">
            {{ $movies->links() }}
        </div>
    </main>
@endsection
