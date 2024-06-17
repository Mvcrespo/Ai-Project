@extends('layouts.admin')

@section('header-title', 'New Screening')

@section('main')
<div class="flex flex-col space-y-6">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
        <div class="max-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        New Screening
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 mb-6">
                        Click on "Save" button to store the information.
                    </p>
                </header>

                <form method="POST" action="{{ route('screenings.store') }}" novalidate>
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <x-field.select name="movie_id" label="Movie" :options="$movies" value="{{ old('movie_id') }}" id="movie-select"/>
                            <x-field.select name="theater_id" label="Theater" :options="$theaters" value="{{ old('theater_id') }}" id="theater-select"/>
                        </div>
                    </div>

                    <div class="mt-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Start Time
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="screenings-container" class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                <tr class="screening-entry">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="date" name="screenings[0][date]" class="form-input mt-1 block w-full dark:bg-gray-700 dark:text-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="time" name="screenings[0][start_time]" class="form-input mt-1 block w-full dark:bg-gray-700 dark:text-gray-300">
                                    </td>
                                    <td class="px-6 py-7 whitespace-nowrap flex items-center space-x-6">
                                        <button type="button" class="btn btn-success add-screening">
                                            <x-table.icon-add class="px-0.5"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex mt-6">
                        <x-button element="submit" type="dark" text="Save" class="uppercase ms-4"/>
                        <x-button element="a" type="light" text="Cancel" class="uppercase ms-4" href="{{ route('screenings.index') }}"/>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.add-screening')) {
            const container = document.getElementById('screenings-container');
            const index = container.children.length;
            const newRow = document.createElement('tr');
            newRow.classList.add('screening-entry');

            newRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="date" name="screenings[${index}][date]" class="form-input mt-1 block w-full dark:bg-gray-700 dark:text-gray-300">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="time" name="screenings[${index}][start_time]" class="form-input mt-1 block w-full dark:bg-gray-700 dark:text-gray-300">
                </td>
                <td class="px-6 py-7 whitespace-nowrap flex items-center space-x-8">
                    <button type="button" class="remove-screening btn btn-danger">
                        <x-table.icon-trash class="px-0.5"/>
                    </button>
                    <button type="button" class="btn btn-success add-screening">
                        <x-table.icon-add class="px-0.5"/>
                    </button>
                </td>
            `;

            container.appendChild(newRow);
            updateButtons();
        }

        if (e.target && e.target.closest('.remove-screening')) {
            const container = document.getElementById('screenings-container');
            if (container.children.length > 1) {
                e.target.closest('.screening-entry').remove();
                updateButtons();
            }
        }
    });

    function updateButtons() {
        const container = document.getElementById('screenings-container');
        const rows = container.querySelectorAll('.screening-entry');
        rows.forEach((row, index) => {
            const addButton = row.querySelector('.add-screening');
            const removeButton = row.querySelector('.remove-screening');
            if (addButton) {
                addButton.style.display = (index === rows.length - 1) ? 'inline-block' : 'none';
            }
            if (removeButton) {
                removeButton.style.display = (index === 0) ? 'none' : 'inline-block';
            }
        });
    }

    updateButtons();
</script>
@endsection
