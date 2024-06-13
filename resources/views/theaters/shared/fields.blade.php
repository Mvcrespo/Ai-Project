@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
    $adminReadonly = $readonly;
    if (!$adminReadonly) {
        if ($mode == 'create') {
            $adminReadonly = Auth::user()?->cannot('createAdmin', App\Models\User::class);
        } elseif ($mode == 'edit') {
            $adminReadonly = Auth::user()?->cannot('updateAdmin', $theater);
        } else {
            $adminReadonly = true;
        }
    }
@endphp

<div class="flex flex-wrap space-x-8">
    <div class="grow mt-6 space-y-4">
        <x-field.input name="name" label="Name" :readonly="$readonly" value="{{ old('name', $theater->name) }}"/>
    </div>
    <div class="flex flex-col items-center space-y-4">
        <div class="mb-4">
            <label for="photo_file" class="block text-gray-700 font-bold mb-2 text-center">Theater Photo</label>
            @php
                $defaultPhotoUrl = asset('photos_theaters/default_theater.png');
                $photoUrl = $theater->photo_filename ? asset('storage/theaters/' . $theater->photo_filename) : $defaultPhotoUrl;
            @endphp
            <img src="{{ $photoUrl }}" alt="Theater Photo" class="mb-2 w-52 h-65 object-cover mx-auto">
            @if (!$readonly)
                <input type="file" name="photo_file" id="photo_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300"/>
                @if($theater->photo_filename)
                <button type="button" class="px-4 py-2 mt-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:bg-red-800" onclick="document.getElementById('form_to_delete_photo').submit();">Delete Photo</button>
                @endif
            @endif
        </div>
    </div>
</div>
