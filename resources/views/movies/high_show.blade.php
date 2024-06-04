@extends('layouts.main')

@section('header-title')
    {{ $movie->title }}
@endsection

@section('main')
    <div class="container mx-auto px-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg overflow-hidden mb-8 p-6">
            <div class="flex flex-col md:flex-row">
                <!-- Poster Image -->
                <div class="md:flex-shrink-0 md:w-1/3">
                    <div class="p-4 bg-white rounded-lg shadow-md">
                        <img src="{{ $movie->poster_full_url }}" alt="{{ $movie->title }} poster" class="w-full h-auto rounded-lg mb-4 md:mb-0">
                    </div>
                </div>
                <!-- Movie Details -->
                <div class="md:ml-6 md:flex-1">
                    <h1 class="text-3xl font-bold mb-4 text-gray-800 dark:text-gray-200">{{ $movie->title }}</h1>
                    <p class="text-gray-700 dark:text-gray-300 mb-2 font-bold">Genre: {{ $movie->genre_code }}</p>
                    <p class="text-gray-700 dark:text-gray-300 mb-1 font-bold">Synopsis:</p>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $movie->synopsis }}</p>
                    @if ($movie->trailer_url)
                        <div class="mb-4">
                            <iframe width="100%" height="415" src="{{ $movie->trailer_embed_url }}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @endif
                </div>
            </div>

            @if($movie->screenings->isNotEmpty())
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200 mt-6">Sessões</h2>
                <div>
                    <label for="date-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Selecione a data</label>
                    <select id="date-select" class="block w-full p-2.5 mb-4 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Escolha a Data</option>
                        @foreach ($movie->screenings->groupBy('date') as $date => $sessions)
                            <option value="{{ $date }}">{{ $date }}</option>
                        @endforeach
                    </select>

                    <label for="time-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Selecione a Hora</label>
                    <select id="time-select" class="block w-full p-2.5 mb-4 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>
                        <option value="">Escolha a Hora</option>
                    </select>
                </div>
                <ul id="session-details" class="list-disc list-inside">
                    <!-- Session details will be populated here -->
                </ul>
            @else
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200 mt-6">Sessões</h2>
                <p class="text-gray-700 dark:text-gray-300">Nenhuma data disponível.</p>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateSelect = document.getElementById('date-select');
            const timeSelect = document.getElementById('time-select');
            const sessionDetails = document.getElementById('session-details');

            const sessions = @json($movie->screenings);

            dateSelect.addEventListener('change', function() {
                const selectedDate = this.value;
                timeSelect.innerHTML = '<option value="">Escolha a Hora</option>';

                if (selectedDate) {
                    timeSelect.disabled = false;
                    const availableTimes = sessions.filter(session => session.date === selectedDate);
                    availableTimes.forEach(session => {
                        const option = document.createElement('option');
                        option.value = session.start_time;
                        option.textContent = session.start_time;
                        timeSelect.appendChild(option);
                    });
                } else {
                    timeSelect.disabled = true;
                    sessionDetails.innerHTML = '';
                }
            });

            timeSelect.addEventListener('change', function() {
                const selectedTime = this.value;
                const selectedDate = dateSelect.value;

                sessionDetails.innerHTML = '';
                if (selectedDate && selectedTime) {
                    const session = sessions.find(session => session.date === selectedDate && session.start_time === selectedTime);
                    if (session) {
                        const li = document.createElement('li');
                        li.classList.add('mb-2');
                        li.classList.add('text-gray-700');
                        li.classList.add('dark:text-gray-300');
                        li.innerHTML = `
                            <span class="font-semibold">Theater:</span> ${session.theater.name} <br>
                            <span class="font-semibold">Date:</span> ${session.date} <br>
                            <span class="font-semibold">Start Time:</span> ${session.start_time} <br>
                            <span class="font-semibold">${session.isSoldOut ? 'Indisponível' : 'Disponível'}</span>
                        `;
                        sessionDetails.appendChild(li);
                    }
                }
            });
        });
    </script>
@endsection
