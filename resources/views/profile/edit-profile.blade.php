@extends('layouts.main')

@section('header-title', $user->name)

@section('main')
<div class="flex flex-col space-y-6">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
        <div class="max-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Edit Profile "{{ $user->name }}"
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 mb-6">
                        Click on "Save" button to store the information.
                    </p>
                </header>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <x-field.input name="name" label="Name" width="full" value="{{ old('name', $user->name) }}"/>
                            <x-field.input name="email" label="Email" width="full" type="email" value="{{ old('email', $user->email) }}"/>
                        </div>
                        <div class="flex flex-col items-center space-y-4">
                            <div class="mb-4">
                                <label for="photo_file" class="block text-gray-700 font-bold mb-2 text-center">Profile Photo</label>
                                @php
                                    $photoUrl = $user->photo_filename ? asset('storage/photos/' . $user->photo_filename) : asset('/img/default_user.png');
                                @endphp
                                <img src="{{ $photoUrl }}" alt="Profile Photo" class="mb-2 w-52 h-65 object-cover mx-auto">
                                <input type="file" name="photo_file" id="photo_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300"/>
                            </div>
                        </div>
                    </div>
                    <div class="flex mt-6">
                        <x-button element="submit" type="dark" text="Save" class="uppercase"/>
                        <x-button element="a" type="light" text="Cancel" class="uppercase ms-4"
                                    href="{{ url()->previous() }}"/>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
