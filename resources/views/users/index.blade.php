@extends('layouts.admin')

@section('header-title', 'List of Users')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <div class="flex items-center justify-between gap-4 mb-4">
                @can('create', App\Models\User::class)
                    <x-button
                        href="{{ route('users.create') }}"
                        text="Create a new user"
                        type="success"/>
                @endcan
                <div class="flex items-center gap-4">
                    <x-button
                        href="{{ route('users.index', ['type' => 'A']) }}"
                        text="Admin"
                        type="primary"/>
                    <x-button
                        href="{{ route('users.index', ['type' => 'E']) }}"
                        text="Employee"
                        type="primary"/>
                    <x-button
                        href="{{ route('users.index', ['type' => 'C']) }}"
                        text="Customer"
                        type="primary"/>
                    <x-button
                        href="{{ route('users.index') }}"
                        text="All Types"
                        type="primary"/>
                </div>
            </div>

            <!-- Search Form -->
            <form action="{{ route('users.index') }}" method="GET" class="mb-4 flex items-center gap-4">
                <input type="text" name="search" class="form-control block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search for a user..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Search</button>
            </form>

            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <x-users.table :users="$users"
                    :showView="true"
                    :showEdit="true"
                    :showDelete="true"
                    />
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
