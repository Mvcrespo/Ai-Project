@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
    $screening = $screening ?? new \App\Models\Screening;
@endphp

<!-- Formulário de Edição -->
@if($mode == 'edit')
    <form method="POST" action="{{ route('screenings.update', ['screening' => $screening]) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-4">
                <x-field.select name="movie_id" label="Movie" :options="$movies" :readonly="$readonly" value="{{ old('movie_id', $screening->movie_id) }}"/>
                <x-field.select name="theater_id" label="Theater" :options="$theaters" :readonly="$readonly" value="{{ old('theater_id', $screening->theater_id) }}"/>
                <x-field.input name="date" label="Date" type="date" :readonly="$readonly" value="{{ old('date', $screening->date) }}"/>
                <x-field.input name="start_time" label="Start Time" type="time" :readonly="$readonly" value="{{ old('start_time', $screening->start_time) }}"/>
            </div>
        </div>

        <div class="flex mt-6">
            <x-button element="submit" type="dark" text="Save" class="uppercase"/>
            <x-button element="a" type="light" text="Cancel" class="uppercase ms-4" href="{{ url()->full() }}"/>
        </div>
    </form>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-4">
            <x-field.select name="movie_id" label="Movie" :options="$movies" :readonly="$readonly" value="{{ old('movie_id', $screening->movie_id) }}"/>
            <x-field.select name="theater_id" label="Theater" :options="$theaters" :readonly="$readonly" value="{{ old('theater_id', $screening->theater_id) }}"/>
            <x-field.input name="date" label="Date" type="date" :readonly="$readonly" value="{{ old('date', $screening->date) }}"/>
            <x-field.input name="start_time" label="Start Time" type="time" :readonly="$readonly" value="{{ old('start_time', $screening->start_time) }}"/>
        </div>
    </div>
@endif

<!-- Formulário de Filtro -->
@if(isset($relatedScreenings) && $relatedScreenings->isNotEmpty())
    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Related Screenings</h3>

        <form method="GET" action="{{ $mode == 'edit' ? route('screenings.edit', ['screening' => $screening]) : route('screenings.show', ['screening' => $screening]) }}" class="flex space-x-4 mt-4">
            <div>
                <label for="filter_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day</label>
                <input type="number" name="filter_day" id="filter_day" value="{{ request('filter_day') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                @error('filter_day')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="filter_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
                <input type="number" name="filter_month" id="filter_month" value="{{ request('filter_month') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                @error('filter_month')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="filter_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                <input type="number" name="filter_year" id="filter_year" value="{{ request('filter_year') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                @error('filter_year')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Filter</button>
            </div>
        </form>

        <table class="table-auto border-collapse w-full mt-4">
            <thead>
                <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                    <th class="px-2 py-2 text-left">Date</th>
                    <th class="px-2 py-2 text-left">Start Time</th>
                    @if($mode == 'edit')
                        <th class="px-2 py-2 text-left">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($relatedScreenings as $relatedScreening)
                    <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                        <td class="px-2 py-2 text-left">{{ $relatedScreening->date }}</td>
                        <td class="px-2 py-2 text-left">{{ $relatedScreening->start_time }}</td>
                        @if($mode == 'edit')
                            <td class="px-2 py-2 text-left">
                                <a href="{{ route('screenings.edit', ['screening' => $relatedScreening]) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $relatedScreenings->links() }}
        </div>
    </div>
@endif
