@extends('layouts.admin')

@section('header-title', 'List of Movies')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden
                    shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            @can('create', App\Models\Movie::class)
                <div class="flex items-center gap-4 mb-4">
                    <x-button
                        href="{{ route('movies.create') }}"
                        text="Create a new movie"
                        type="success"/>
                </div>
            @endcan

            <form action="{{ route('movies.index') }}" method="GET" class="mb-4 flex items-center gap-4">
                <input type="text" name="search" class="form-control block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search for a movie..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Search</button>
            </form>

            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <x-movies.table :movies="$movies"
                    :showView="true"
                    :showEdit="true"
                    :showDelete="true"
                    />
            </div>
            <div class="mt-4 flex justify-center">
                {{ $movies->links() }}
            </div>
        </div>
    </div>
@endsection
