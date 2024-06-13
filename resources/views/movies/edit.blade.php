@extends('layouts.admin')

@section('header-title', 'Edit Movie "' . $movie->title . '"')

@section('main')
<div class="flex flex-col space-y-6">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
        <div class="max-full">
            <section>
                <div class="flex flex-wrap justify-end items-center gap-4 mb-4">
                    @can('create', App\Models\movie::class)
                    <x-button
                        href="{{ route('movies.create') }}"
                        text="New"
                        type="success"/>
                    @endcan
                    @can('update', $movie)
                    <x-button
                        href="{{ route('movies.show', ['movie' => $movie]) }}"
                        text="View"
                        type="primary"/>
                    @endcan
                    @can('delete', $movie)
                    <form method="POST" action="{{ route('movies.destroy', ['movie' => $movie]) }}">
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
                        Edit movie "{{ $movie->title }}"
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 mb-6">
                        Click on "Save" button to store the information.
                    </p>
                </header>

                <form method="POST" action="{{ route('movies.update', ['movie' => $movie]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('movies.shared.fields', ['mode' => 'edit'])
                    <div class="flex mt-6">
                        <x-button element="submit" type="dark" text="Save" class="uppercase"/>
                        <x-button element="a" type="light" text="Cancel" class="uppercase ms-4" href="{{ url()->previous() }}"/>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
<form id="form_to_delete_poster" method="POST" action="{{ route('movies.poster.destroy', ['movie' => $movie]) }}">
    @csrf
    @method('DELETE')
</form>
@endsection
