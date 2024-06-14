@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-4">
        <x-field.input name="title" label="Title" width="full" :readonly="$readonly" value="{{ old('title', $movie->title) }}"/>

        <!-- Dropdown para Genre Code -->
        <div>
            <label for="genre_code" class="block text-gray-700 font-bold mb-2">Genre</label>
            <select name="genre_code" id="genre_code" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" {{ $readonly ? 'disabled' : '' }}>
                @foreach($genres as $genre)
                    <option value="{{ $genre->code }}" {{ old('genre_code', $movie->genre_code) == $genre->code ? 'selected' : '' }}>
                        {{ $genre->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <x-field.input name="year" label="Year" width="full" type="number" :readonly="$readonly" value="{{ old('year', $movie->year) }}"/>
        <x-field.text-area name="synopsis" label="Synopsis" width="full" height="lg" :readonly="$readonly" value="{{ old('synopsis', $movie->synopsis) }}"/>
        <x-field.input name="trailer_url" label="Trailer URL" width="full" :readonly="$readonly" value="{{ old('trailer_url', $movie->trailer_url) }}"/>
    </div>
    <div class="flex flex-col items-center space-y-4">
        <div class="mb-4">
            <label for="poster_filename" class="block text-gray-700 font-bold mb-2 text-center">Poster</label>
            @php
                $defaultPosterUrl = asset('img/default_poster.png');
                $posterUrl = $movie->poster_filename ? asset('storage/posters/' . $movie->poster_filename) : $defaultPosterUrl;
            @endphp
            <img src="{{ $posterUrl }}" alt="Poster" class="mb-2 w-64 h-96 object-cover mx-auto">
            @if (!$readonly)
                <input type="file" name="poster_filename" id="poster_filename" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300"/>
                @if($movie->poster_filename)
                    <button type="button" class="px-4 py-2 mt-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:bg-red-800" onclick="document.getElementById('form_to_delete_poster').submit();">Delete Poster</button>
                @endif
            @endif
        </div>
    </div>
</div>
