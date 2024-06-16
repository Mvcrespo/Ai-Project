@extends('layouts.main')

@section('header-title')
    {{ $movie->title }}
@endsection

@section('main')
    <div class="container mx-auto px-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg overflow-hidden mb-8 p-6">
            <div class="flex flex-col md:flex-row">
                <div class="md:flex-shrink-0 md:w-1/3">
                    <div class="p-4 bg-white rounded-lg shadow-md">
                        @php
                            $defaultPosterUrl = asset('img/default_poster.png');
                            $posterUrl = $movie->poster_filename ? asset('storage/posters/' . $movie->poster_filename) : $defaultPosterUrl;
                        @endphp
                        <img src="{{ $posterUrl }}" alt="{{ $movie->title }} poster" class="w-full h-auto rounded-lg mb-4 md:mb-0">
                    </div>
                </div>
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
                            @if (\Carbon\Carbon::parse($date)->isToday() || \Carbon\Carbon::parse($date)->isFuture())
                                <option value="{{ $date }}">{{ $date }}</option>
                            @endif
                        @endforeach
                    </select>

                    <label for="time-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Selecione a Hora</label>
                    <select id="time-select" class="block w-full p-2.5 mb-4 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled>
                        <option value="">Escolha a Hora</option>
                    </select>
                </div>
                <div id="session-details" class="mb-4">

                </div>
                <div id="go-to-theater" class="hidden">
                    <a href="#" id="theater-link" class="inline-block px-6 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">Buy Ticket</a>
                </div>
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
            const goToTheaterDiv = document.getElementById('go-to-theater');
            const theaterLink = document.getElementById('theater-link');

            const today = new Date().toISOString().split('T')[0];
            const sessions = @json($movie->screenings).filter(session => session.date >= today);

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
                    goToTheaterDiv.classList.add('hidden');
                }
            });

            timeSelect.addEventListener('change', function() {
                const selectedTime = this.value;
                const selectedDate = dateSelect.value;

                sessionDetails.innerHTML = '';
                if (selectedDate && selectedTime) {
                    const session = sessions.find(session => session.date === selectedDate && session.start_time === selectedTime);
                    if (session) {
                        const detailsHTML = `
                            <div class="text-gray-700 dark:text-gray-300">
                                <p><span class="font-semibold">Theater:</span> ${session.theater.name}</p>
                                <p><span class="font-semibold">Date:</span> ${session.date}</p>
                                <p><span class="font-semibold">Start Time:</span> ${session.start_time}</p>
                                <p class="${session.isSoldOut ? 'text-red-500' : 'text-green-500'}">${session.isSoldOut ? 'Indisponível' : 'Disponível'}</p>
                            </div>
                        `;
                        sessionDetails.innerHTML = detailsHTML;

                        if (!session.isSoldOut) {
                            goToTheaterDiv.classList.remove('hidden');
                            theaterLink.href = `/theaters/${session.theater.id}/seats/${session.id}`;
                        } else {
                            goToTheaterDiv.classList.add('hidden');
                        }
                    }
                }
            });
        });
    </script>
@endsection
