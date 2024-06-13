@extends('layouts.admin')

@section('header-title', 'Theater "' . $theater->name . '"')

@section('main')
<div class="flex flex-col space-y-6">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
        <div class="max-full">
            <section>
                <div class="flex flex-wrap justify-end items-center gap-4 mb-4">
                    @can('create', App\Models\User::class)
                    <x-button
                        href="{{ route('theaters.create') }}"
                        text="New"
                        type="success"/>
                    @endcan
                    @can('update', $theater)
                    <x-button
                        href="{{ route('theaters.edit', ['theater' => $theater]) }}"
                        text="Edit"
                        type="primary"/>
                    @endcan
                    @can('delete', $theater)
                    <form method="POST" action="{{ route('theaters.destroy', ['theater' => $theater]) }}">
                        @csrf
                        @method('DELETE')
                        <x-button
                            element="submit"
                            text="Delete"
                            type="danger"/>
                    </form>
                    @endcan
                </div>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Theater "{{ $theater->name }}"
                    </h2>
                </header>
                @include('theaters.shared.fields', ['mode' => 'show'])
            </section>
        </div>
    </div>
</div>
@endsection
