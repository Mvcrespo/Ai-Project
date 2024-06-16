@extends('layouts.admin')

@section('header-title', 'List of Screenings')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            @can('create', App\Models\Screening::class)
                <div class="flex items-center gap-4 mb-4">
                    <x-button href="{{ route('screenings.create') }}" text="Create a new screening" type="success"/>
                </div>
            @endcan

            <form method="GET" action="{{ route('screenings.index') }}" class="mb-4">
                <div class="flex items-center gap-4">
                    <input type="text" name="search" class="form-control block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search by movie title..." value="{{ request('search') }}">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Search</button>
                </div>
            </form>

            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <x-screenings.table :screenings="$screenings" :showView="true" :showEdit="true" :showDelete="true"/>
            </div>
            <div class="mt-4">
                {{ $screenings->links() }}
            </div>
        </div>
    </div>
@endsection
