{{-- resources/views/movies/shared/movies-list.blade.php --}}

@props(['movies'])
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @if($movies->isEmpty())
        <p class="text-gray-800 dark:text-gray-200">Nenhum filme encontrado.</p>
    @else
        @foreach($movies as $movie)
            <a href="{{ route('movies.show', $movie->id) }}" class="relative group bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-transform transform hover:scale-105" style="height: 100%;">
                <div class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow-md" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <img src="{{ $movie->poster_full_url }}" alt="{{ $movie->title }}" style="width: 100%; height: 340px; object-fit: contain;" class="rounded-lg mb-4">
                    <div class="text-center">
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $movie->title }}</h4>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-white dark:bg-gray-800 bg-opacity-90 dark:bg-opacity-90 p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <p class="text-gray-600 dark:text-gray-400">{{ $movie->year }}</p>
                    <p class="text-gray-600 dark:text-gray-400">{{ $movie->genre_code }}</p>
                    <span class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">View Details</span>
                </div>
            </a>
        @endforeach
    @endif
</div>
