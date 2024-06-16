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

<!-- Formulário de Exclusão Fora do Formulário Principal -->
<form id="screeningsDeleteForm" action="" method="POST">
    @csrf
    @method('DELETE')
</form>

<!-- Formulário Principal -->
<form method="POST" action="{{ route('screenings.update', ['screening' => $screening]) }}">
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
    @if($mode == 'edit')
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Save</button>
    @endif
</form>
<!-- Fim do Formulário Principal -->

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
</script>
