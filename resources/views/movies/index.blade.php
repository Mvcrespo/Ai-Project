@extends('layouts.main')

@section('header-title', 'Introduction')

@section('main')
<main>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <h3 class="pb-3 font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                Cinema Parte inicial
            </h3>
            <p class="py-3 font-medium text-gray-700 dark:text-gray-300">
                Pagina de teste Inicial
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @if($movies->isEmpty())
                    <p>Nenhum filme encontrado.</p>
                @else
                    @foreach($movies as $movie)
                        <div class="relative group bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <img src="{{ $movie->poster_full_url }}" alt="{{ $movie->title }}" class="w-full h-auto">
                            <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-90 p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <h4 class="font-semibold text-gray-800">{{ $movie->title }}</h4>
                                <p class="text-gray-600">{{ $movie->year }}</p>
                                <p class="text-gray-600">{{ $movie->genre->name }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</main>
@endsection