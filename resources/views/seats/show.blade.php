@extends('layouts.main')

@section('header-title', 'Seats of ' . $theater->name)

@section('main')
    <div class="flex flex-col items-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <div class="font-base text-sm text-gray-700 dark:text-gray-300 mb-4">
                <h2 class="text-lg mb-4">Seats of {{ $theater->name }}</h2>
                @php
                    $seatsByRow = $seats->groupBy('row');
                    $maxSeatsInRow = $seatsByRow->max(function($row) {
                        return $row->count();
                    });
                @endphp
                @foreach ($seatsByRow as $row => $rowSeats)
                    <div class="flex items-center w-full mb-2">
                        <div class="w-8 flex items-center justify-center font-bold text-lg">
                            {{ $row }}
                        </div>
                        <div class="flex justify-center w-full">
                            @php
                                $totalSeats = $rowSeats->count();
                                $leftPadding = max(0, (int)(($maxSeatsInRow - $totalSeats) / 2));
                                $rightPadding = $maxSeatsInRow - $totalSeats - $leftPadding;
                            @endphp
                            @for ($i = 0; $i < $leftPadding; $i++)
                                <div class="w-8 h-8"></div>
                            @endfor
                            @foreach ($rowSeats as $index => $seat)
                                @php
                                    $isAvailable = $seat->tickets->contains(function ($ticket) {
                                        return $ticket->isAvailable();
                                    });
                                @endphp
                                <div class="relative w-12 h-12 mx-1">
                                    <img src="{{ asset($isAvailable ? 'icons/seat-available.png' : 'icons/seat-unavailable.png') }}"
                                         alt="{{ $isAvailable ? 'Available Seat' : 'Unavailable Seat' }}"
                                         class="w-full h-full">
                                    <span class="absolute inset-0 flex items-center justify-center text-white font-bold">
                                        {{ $seat->seat_number }}
                                    </span>
                                </div>
                                @if ($index == 1 || $index == $totalSeats - 3)
                                    <div class="w-8 h-8 mx-1"></div> <!-- Corredor -->
                                @endif
                            @endforeach
                            @for ($i = 0; $i < $rightPadding; $i++)
                                <div class="w-8 h-8"></div>
                            @endfor
                        </div>
                    </div>
                    @if ($loop->index == 7)
                        <div class="h-8"></div> <!-- Espaço entre as filas após a sétima fila -->
                    @endif
                @endforeach
            </div>
            <div class="flex justify-center mt-4">
                <div class="bg-black text-white font-bold py-2 px-28 rounded">
                    Ecran
                </div>
            </div>
        </div>
    </div>
@endsection
