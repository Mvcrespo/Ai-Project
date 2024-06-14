@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
    $screening = $screening ?? new \App\Models\Screening;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-4">
        <x-field.select name="movie_id" label="Movie" :options="$movies" :readonly="$readonly" value="{{ old('movie_id', $screening->movie_id) }}"/>
        <x-field.select name="theater_id" label="Theater" :options="$theaters" :readonly="$readonly" value="{{ old('theater_id', $screening->theater_id) }}"/>
        <x-field.input name="date" label="Date" type="date" :readonly="$readonly" value="{{ old('date', $screening->date) }}"/>
        <x-field.input name="start_time" label="Start Time" type="time" :readonly="$readonly" value="{{ old('start_time', $screening->start_time) }}"/>
    </div>
</div>

@if(isset($relatedScreenings) && $relatedScreenings->isNotEmpty())
    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Related Screenings</h3>
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
    </div>
@endif
