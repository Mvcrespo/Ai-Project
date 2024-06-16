@extends('layouts.main')

@section('header-title', 'Session Control')

@section('main')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Select Screening Session</h1>

    <form action="{{ route('session.control') }}" method="GET" class="mb-4 flex items-center gap-4">
        <input type="text" name="search" class="form-control block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search for a movie, date (e.g., 2024-06-14 or 06-14), year, or theater..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Search</button>
    </form>

    <div id="selected-session" class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hidden">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Selected Session</h2>
        <div class="text-gray-900 dark:text-gray-100">
            <p id="selected-movie"></p>
            <p id="selected-theater"></p>
            <p id="selected-date"></p>
            <p id="selected-time"></p>
        </div>
    </div>

    <form action="{{ route('session.validate') }}" method="POST" class="mb-4">
        @csrf
        <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg max-h-96 overflow-y-scroll">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Movie</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Theater</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($screenings as $screening)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" onclick="selectSession({{ $screening->id }}, '{{ $screening->movie->title }}', '{{ $screening->theater->name }}', '{{ $screening->date }}', '{{ \Carbon\Carbon::parse($screening->start_time)->format('H:i') }}', this)">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $screening->movie->title }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $screening->theater->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $screening->date }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($screening->start_time)->format('H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <input type="hidden" id="screening_id" name="screening_id" required>
        @error('screening_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <div class="mt-4">
            <label for="ticket_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ticket ID (optional)</label>
            <input type="text" id="ticket_id" name="ticket_id" class="block w-full mt-1 text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @error('ticket_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-4">
            <label for="qrcode_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">QR Code URL (optional)</label>
            <input type="text" id="qrcode_url" name="qrcode_url" class="block w-full mt-1 text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @error('qrcode_url')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mt-4">Validate Ticket</button>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectedSessionId = localStorage.getItem('selectedSessionId');
        if (selectedSessionId) {
            const selectedSessionDetails = JSON.parse(localStorage.getItem('selectedSessionDetails'));
            document.getElementById('screening_id').value = selectedSessionId;
            if (selectedSessionDetails) {
                showSelectedSession(selectedSessionDetails.movie, selectedSessionDetails.theater, selectedSessionDetails.date, selectedSessionDetails.time);
            }
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const rowId = row.getAttribute('onclick').match(/\d+/)[0];
                if (rowId == selectedSessionId) {
                    row.classList.add('bg-blue-100', 'dark:bg-blue-700');
                }
            });
        }
    });

    function selectSession(id, movie, theater, date, time, row) {
        document.getElementById('screening_id').value = id;
        localStorage.setItem('selectedSessionId', id);
        const sessionDetails = { movie, theater, date, time };
        localStorage.setItem('selectedSessionDetails', JSON.stringify(sessionDetails));
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(r => r.classList.remove('bg-blue-100', 'dark:bg-blue-700'));
        row.classList.add('bg-blue-100', 'dark:bg-blue-700');
        showSelectedSession(movie, theater, date, time);
    }

    function showSelectedSession(movie, theater, date, time) {
        document.getElementById('selected-session').classList.remove('hidden');
        document.getElementById('selected-movie').innerText = `Movie: ${movie}`;
        document.getElementById('selected-theater').innerText = `Theater: ${theater}`;
        document.getElementById('selected-date').innerText = `Date: ${date}`;
        document.getElementById('selected-time').innerText = `Time: ${time}`;
    }
</script>
@endsection
