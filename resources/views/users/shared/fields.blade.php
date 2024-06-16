@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
    $adminReadonly = $readonly;
    if (!$adminReadonly) {
        if ($mode == 'create') {
            $adminReadonly = Auth::user()?->cannot('createAdmin', App\Models\User::class);
        } elseif ($mode == 'edit') {
            $adminReadonly = Auth::user()?->cannot('updateAdmin', $user);
        } else {
            $adminReadonly = true;
        }
    }

    $typeOptions = [
        'A' => 'Admin',
        'E' => 'Employee',
        'C' => 'Customer'
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-4">
        <x-field.input name="name" label="Name" width="full" :readonly="$readonly" value="{{ old('name', $user->name) }}"/>
        <x-field.input name="email" label="Email" width="full" type="email" :readonly="$readonly" value="{{ old('email', $user->email) }}"/>
        <x-field.radiogroup name="type" label="Type" width="full" :readonly="$readonly" value="{{ old('type', $user->type) }}" :options="$typeOptions"/>

        @if ($mode == 'create')
            <x-field.input name="password" label="Password" width="full" type="password" :readonly="$readonly" value=""/>
            <x-field.input name="password_confirmation" label="Confirm Password" width="full" type="password" :readonly="$readonly" value=""/>
        @endif
    </div>
    <div class="flex flex-col items-center space-y-4">
        <div class="mb-4">
            <label for="photo_file" class="block text-gray-700 font-bold mb-2 text-center">Profile Photo</label>
            @php
                $photoUrl = $user->photo_filename ? asset('storage/photos/' . $user->photo_filename) : asset('/img/default_user.png');
            @endphp
            <img src="{{ $photoUrl }}" alt="Profile Photo" class="mb-2 w-52 h-65 object-cover mx-auto">
            @if (!$readonly)
                <input type="file" name="photo_file" id="photo_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300"/>
            @endif
        </div>
    </div>
</div>
