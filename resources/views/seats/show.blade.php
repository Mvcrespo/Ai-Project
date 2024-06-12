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
                            <div class="flex flex-wrap justify-center w-full">
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
                                        $ticket = $seat->tickets->first();
                                        $isAvailable = $ticket && $ticket->isAvailable();
                                    @endphp
                                    <div id="seat-{{ $seat->id }}" class="relative w-12 h-12 mx-1 cursor-pointer seat-item" onclick="showTicketDetails({{ $seat->id }}, {{ $screening->id }}, '{{ $row }}', {{ $seat->seat_number }})">
                                        <img src="{{ asset($isAvailable ? 'icons/seat-available.png' : 'icons/seat-unavailable.png') }}"
                                             alt="{{ $isAvailable ? 'Available Seat' : 'Unavailable Seat' }}"
                                             class="w-full h-full">
                                        <span class="absolute inset-0 flex items-center justify-center text-white font-bold seat-number">
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
                            <div class="w-8 flex items-center justify-center font-bold text-lg">
                                {{ $row }}
                            </div>
                        </div>
                        @if ($loop->index == 7)
                            <div class="h-8"></div> <!-- Espaço entre as filas após a sétima fila -->
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
    <div id="ticket-details" class="flex-1 p-4 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-50 rounded shadow-sm mt-4 w-full max-w-md">
        <!-- Bilhete -->
    </div>
</div>
<script>
    function showTicketDetails(seatId, screeningId, row, seatNumber) {
        fetch(`/seats/${seatId}/ticket-details?screening_id=${screeningId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('ticket-details').innerHTML = `<p>${data.error}</p>`;
                } else {
                    document.getElementById('ticket-details').innerHTML = `
                        <!-- Bilhete -->
                        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow-md">
                            <p class="text-lg font-bold mb-2">Ticket Details</p>
                            <p><strong>Ticket ID:</strong> ${data.id}</p>
                            <p><strong>Seat:</strong> ${row}${seatNumber}</p>
                            <p><strong>Price:</strong> ${data.price} $</p>
                            <p><strong>Status:</strong> <span style="color: ${data.status ? 'green' : 'red'};">${data.status ? 'Available Seat' : 'Unavailable Seat'}</span></p>
                            <p><strong>Purchase ID:</strong> ${data.purchase_id}</p>
                            ${data.status ? '<button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded" onclick="addToCart(' + data.id + ')">Add to Cart</button>' : ''}
                        </div>
                    `;
                }
                // Highlight the selected seat
                document.querySelectorAll('.seat-item').forEach(el => {
                    el.classList.remove('text-3xl');
                    el.querySelector('.seat-number').classList.remove('text-3xl');
                });
                const selectedSeat = document.getElementById(`seat-${seatId}`);
                selectedSeat.querySelector('.seat-number').classList.add('text-3xl');
            });
    }

    function addToCart(seatId) {
        fetch(`/cart/add/${seatId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('Ticket added to cart', 'success');
            } else {
                showMessage('Ticket could not be added to cart: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An unexpected error occurred.', 'error');
        });
    }

    function showMessage(message, type) {
        const messageContainer = document.getElementById('message-container');
        const messageElement = document.createElement('div');
        messageElement.textContent = message;
        messageElement.className = `p-4 rounded shadow-md text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        messageContainer.appendChild(messageElement);

        setTimeout(() => {
            messageContainer.removeChild(messageElement);
            location.reload(); // Reload the page
        }, 1400);
    }
</script>
@endsection
