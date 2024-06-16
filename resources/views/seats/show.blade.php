@extends('layouts.main')

@section('header-title', 'Seats of ' . $theater->name . ' for ' . $screening->movie->title)

@section('main')
<div class="flex flex-col items-center">
    <div id="message-container" class="fixed top-0 left-0 w-full flex justify-center"></div>
    <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
        <div class="font-base text-sm text-gray-700 dark:text-gray-300 mb-4">
            <h2 class="text-lg mb-4">Seats of {{ $theater->name }} for {{ $screening->movie->title }}</h2>
            <div class="flex justify-center">
                <div>
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
                            <div class="flex justify-center flex-nowrap w-full">
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
                                        $isAvailable = !$occupiedSeats->contains($seat->id);
                                    @endphp
                                    <div id="seat-{{ $seat->id }}" class="relative w-12 h-12 mx-1 seat-item {{ $isAvailable ? 'cursor-pointer' : 'cursor-not-allowed' }}" onclick="{{ $isAvailable ? "toggleSeatSelection({$seat->id}, '{$row}', {$seat->seat_number}, '{$screening->movie->title}', {$ticketPrice})" : '' }}">
                                        <img src="{{ asset($isAvailable ? 'icons/seat-available.png' : 'icons/seat-unavailable.png') }}"
                                             alt="{{ $isAvailable ? 'Available Seat' : 'Unavailable Seat' }}"
                                             class="w-full h-full">
                                        @if ($isAvailable)
                                            <span class="absolute inset-0 flex items-center justify-center text-white font-bold seat-number">
                                                {{ $seat->seat_number }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($index == 1 || $index == $totalSeats - 3)
                                        <div class="w-8 h-8 mx-1"></div>
                                    @endif
                                @endforeach
                                @for ($i = 0; $i < $rightPadding; $i++)
                                    <div class="w-8 h-8"></div>
                                @endfor
                            </div>
                            <div class="w-8 flex items-center justify-center font-bold text-lg">
                                {{ $row }}
                            </div>
                        </div>
                        @if ($loop->index == 7)
                            <div class="h-8"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-center mt-4">
            <div class="bg-black text-white font-bold py-2 px-28 rounded">
                Screen
            </div>
        </div>
    </div>
    <div id="selected-tickets" class="flex-1 p-4 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-50 rounded shadow-sm mt-4 w-full max-w-md">

    </div>
    <div class="mt-4">
        <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="post" class="mt-4">
            @csrf
            <div id="form-container"></div>
            <x-button element="submit" type="dark" text="Add All to Cart"/>
        </form>
    </div>
</div>
<script>
    let selectedSeats = [];

    function toggleSeatSelection(seatId, row, seatNumber, movieTitle, price) {
        const seatIndex = selectedSeats.findIndex(seat => seat.seatId === seatId);
        if (seatIndex === -1) {
            selectedSeats.push({ seatId, row, seatNumber, movieTitle, price });
        } else {
            selectedSeats.splice(seatIndex, 1);
        }
        updateSelectedTickets();
    }

    function updateSelectedTickets() {
        const selectedTicketsDiv = document.getElementById('selected-tickets');
        const allSeatElements = document.querySelectorAll('.seat-item');

        allSeatElements.forEach(el => {
            el.querySelector('.seat-number')?.classList.remove('text-3xl');
        });

        if (selectedSeats.length === 0) {
            selectedTicketsDiv.innerHTML = '<p>No seats selected.</p>';
            document.getElementById('add-all-to-cart').classList.add('hidden');
        } else {
            const ticketsHtml = selectedSeats.map(seat => {
                document.getElementById(`seat-${seat.seatId}`).querySelector('.seat-number').classList.add('text-3xl');
                return `
                    <div class="p-4 bg-white dark:bg-gray-900 rounded shadow-md mb-2">
                        <p><strong>Seat:</strong> ${seat.row}${seat.seatNumber}</p>
                        <p><strong>Movie:</strong> ${seat.movieTitle}</p>
                        <p><strong>Price:</strong> ${seat.price} $</p>
                    </div>
                `;
            }).join('');
            selectedTicketsDiv.innerHTML = ticketsHtml;
            document.getElementById('add-all-to-cart').classList.remove('hidden');
        }
    }

    document.getElementById('add-to-cart-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formContainer = document.getElementById('form-container');
        formContainer.innerHTML = '';

        selectedSeats.forEach(seat => {
            formContainer.innerHTML += `
                <input type="hidden" name="seat_id[]" value="${seat.seatId}">
                <input type="hidden" name="screening_id[]" value="{{ $screening->id }}">
                <input type="hidden" name="movie_title[]" value="${seat.movieTitle}">
                <input type="hidden" name="seat[]" value="${seat.row}${seat.seatNumber}">
                <input type="hidden" name="price[]" value="${seat.price}">
            `;
        });


        event.target.submit();
    });
</script>
@endsection
