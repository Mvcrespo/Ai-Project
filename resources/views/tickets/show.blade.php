@extends('layouts.main')

@section('header-title', 'Ticket')

@section('main')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-10">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold mb-2">Ticket Details</h1>
            <p class="text-lg text-gray-600">Ticket ID: {{ $ticket->id }}</p>
        </div>
        <div class="mb-6">
            <p class="text-lg"><strong>Theater:</strong> {{ $ticket->screening->theater->name }}</p>
            <p class="text-lg"><strong>Movie:</strong> {{ $ticket->screening->movie->title }}</p>
            <p class="text-lg"><strong>Date & Time:</strong> {{ $ticket->screening->date }} {{ $ticket->screening->start_time }}</p>
            <p class="text-lg"><strong>Seat:</strong> Row {{ $ticket->seat->row }}, Number {{ $ticket->seat->seat_number }}</p>
        </div>
        @if ($ticket->purchase->customer)
            <div class="flex items-center mb-6">
                @if ($ticket->purchase->customer->avatar)
                    <img class="w-20 h-20 rounded-full mr-4" src="{{ asset('storage/avatars/' . $ticket->purchase->customer->avatar) }}" alt="Customer Avatar">
                @endif
                <div>
                    <p class="text-lg"><strong>Customer Name:</strong> {{ $ticket->purchase->customer->name }}</p>
                    <p class="text-lg"><strong>Customer Email:</strong> {{ $ticket->purchase->customer->email }}</p>
                </div>
            </div>
        @endif
        <div class="text-center mb-6">
            <p class="text-xl font-semibold"><strong>Status:</strong> <span class="{{ $ticket->status == 'valid' ? 'text-green-600' : 'text-red-600' }}">{{ $ticket->status == 'valid' ? 'Valid' : 'Invalid' }}</span></p>
        </div>
        @if ($ticket->status == 'valid')
            <div class="text-center">
                <img class="mx-auto" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $ticket->qrcode_url }}" alt="QR Code">
                <p class="text-lg mt-4">Scan this QR code at the entrance</p>
            </div>
        @endif
    </div>
@endsection