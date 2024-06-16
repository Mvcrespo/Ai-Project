@php
$mode = $mode ?? 'edit';
$readonly = $mode == 'show';
$screening = $screening ?? new \App\Models\Screening;
@endphp

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif


<form id="screeningsDeleteForm" action="" method="POST">
    @csrf
    @method('DELETE')
</form>


<form id="screeningsFilterForm" action="{{ $mode == 'edit' ? route('screenings.edit', ['screening' => $screening]) : route('screenings.show', ['screening' => $screening]) }}" method="GET">
    <input type="hidden" name="filter_day" id="filter_day_input">
    <input type="hidden" name="filter_month" id="filter_month_input">
    <input type="hidden" name="filter_year" id="filter_year_input">
</form>

<form method="POST" action="{{ route('screenings.update', ['screening' => $screening]) }}" class="mt-6">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-4">
            <x-field.select name="movie_id" label="Movie" :options="$movies" :readonly="$readonly" value="{{ old('movie_id', $screening->movie_id) }}" id="movie-select"/>
            @error('movie_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <x-field.select name="theater_id" label="Theater" :options="$theaters" :readonly="$readonly" value="{{ old('theater_id', $screening->theater_id) }}" id="theater-select"/>
            @error('theater_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="flex my-2 space-x-2 mt-4">
        <div>
            <label for="filter_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day</label>
            <input type="number" name="filter_day" id="filter_day" value="{{ request('filter_day') }}" class="mt-1 block w-20 border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
            @error('filter_day')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="filter_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
            <input type="number" name="filter_month" id="filter_month" value="{{ request('filter_month') }}" class="mt-1 block w-20 border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
            @error('filter_month')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="filter_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
            <input type="number" name="filter_year" id="filter_year" value="{{ request('filter_year') }}" class="mt-1 block w-20 border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
            @error('filter_year')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex items-end">
            <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-md" onclick="submitFilterForm()">Filter</button>
        </div>
    </div>


    @if(isset($relatedScreenings) && $relatedScreenings->isNotEmpty())
        <div class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Related Screenings</h3>

            <input type="hidden" name="modified_ids" id="modified_ids">
            <table class="table-auto border-collapse w-full mt-4">
                <thead>
                    <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                        <th class="px-2 py-2 text-left">Date</th>
                        <th class="px-2 py-2 text-left">Start Time</th>
                        @if($mode == 'edit')
                            <th class="w-10 px-2 py-2 text-left">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relatedScreenings as $relatedScreening)
                        @php $disabled = $relatedScreening->tickets()->exists(); @endphp
                        <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                            <td class="px-2 py-2 text-left">
                                <input type="hidden" name="screenings[{{ $relatedScreening->id }}][id]" value="{{ $relatedScreening->id }}">
                                <input type="date" name="screenings[{{ $relatedScreening->id }}][date]" value="{{ $relatedScreening->date }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600" onchange="markAsModified({{ $relatedScreening->id }})" @if($disabled || $readonly) disabled @endif>
                                @error("screenings.{$relatedScreening->id}.date")
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="px-2 py-2 text-left">
                                <input type="time" name="screenings[{{ $relatedScreening->id }}][start_time]" value="{{ $relatedScreening->start_time }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600" onchange="markAsModified({{ $relatedScreening->id }})" @if($disabled || $readonly) disabled @endif>
                                @error("screenings.{$relatedScreening->id}.start_time")
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </td>
                            @if($mode == 'edit')
                                <td class="px-2 py-2 text-center align-middle">
                                    @if(!$disabled)
                                        <div class="flex justify-center">
                                            <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteScreening({{ $relatedScreening->id }})">
                                                <x-table.icon-trash class="px-0.5"/>
                                            </button>
                                        </div>
                                    @endif
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

    @if ($errors->any())
        <div class="alert alert-danger" style="color: red; padding: 10px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($mode == 'edit')
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Save</button>
    @endif
</form>

<script>
    let modifiedIds = [];

    function markAsModified(id) {
        if (!modifiedIds.includes(id)) {
            modifiedIds.push(id);
        }
        document.getElementById('modified_ids').value = modifiedIds.join(',');
    }

    function deleteScreening(screeningId) {
        const form = document.getElementById('screeningsDeleteForm');
        form.action = `/screenings/${screeningId}/destroy-single`;
        form.submit();
    }

    function submitFilterForm() {
        document.getElementById('filter_day_input').value = document.getElementById('filter_day').value;
        document.getElementById('filter_month_input').value = document.getElementById('filter_month').value;
        document.getElementById('filter_year_input').value = document.getElementById('filter_year').value;
        document.getElementById('screeningsFilterForm').submit();
    }
</script>
