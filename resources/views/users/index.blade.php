@extends('layouts.main')

@section('header-title', 'List of Users')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden
                    shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
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
