@extends('layouts.main')

@section('header-title', 'Ticket')

@section('main')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-10">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold mb-2">Ticket Details</h1>
            <p class="text-lg text-gray-600">Ticket ID: {{ $ticket->id }}</p>
        </div>
        <div class="mb-6">
            <p class="text-lg"><strong>Movie:</strong> {{ $ticket->screening->movie->title }}</p>
            <p class="text-lg"><strong>Theater:</strong> {{ $ticket->screening->theater->name }}</p>
            <p class="text-lg"><strong>Date & Time:</strong> {{ $ticket->screening->date }} {{ $ticket->screening->start_time }}</p>
            <p class="text-lg"><strong>Seat:</strong> {{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</p>
        </div>
        @if ($ticket->purchase->customer)
            <div class="flex items-center mb-6">
                @if ($ticket->purchase->customer->photo_filename)
                    <img class="w-20 h-20 rounded-full mr-4" src="{{ asset('storage/photos/' . $ticket->purchase->customer->photo_filename) }}" alt="Customer Avatar">
                @endif
                <div>
                    <p class="text-lg"><strong>Customer Name:</strong> {{ $ticket->purchase->customer->name }}</p>
                    <p class="text-lg"><strong>Customer Email:</strong> {{ $ticket->purchase->customer->email }}</p>
                </div>
            </div>
        @endif

        @if(!isset($isValidationEmploy) && !isset($isValidation))
            <div class="text-center mb-6">
                <p class="text-xl font-semibold"><strong>Status:</strong> <span class="{{ $ticket->status == 'valid' ? 'text-green-600' : 'text-red-600' }}">{{ $ticket->status == 'valid' ? 'Valid' : 'Invalid' }}</span></p>
            </div>
        @endif

        @if(isset($isValidationEmploy) && $isValidationEmploy)
            <div class="text-center mt-6">
                <form action="{{ route('tickets.validateByQrCode', ['qrcode_url' => basename($ticket->qrcode_url)]) }}" method="POST">
                    @csrf
                    <button type="submit" name="action" value="validate" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-700">Validate</button>
                    <button type="submit" name="action" value="invalidate" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700">Invalidate</button>
                </form>
            </div>
        @endif

        @if((isset($isValidation) && $isValidation) && (!isset($isValidationEmploy) || !$isValidationEmploy))
            <div class="text-center mt-6">
                <a href="{{ route('session.control') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Back to Session Control</a>
            </div>
        @endif
    </div>
@endsection
