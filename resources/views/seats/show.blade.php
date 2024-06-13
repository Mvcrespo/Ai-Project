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
    <div id="selected-tickets" class="flex-1 p-4 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-50 rounded shadow-sm mt-4 w-full max-w-md">
        <!-- Lista de Tickets Selecionados -->
    </div>
    <div class="mt-4">
        <button id="add-all-to-cart" class="px-4 py-2 bg-blue-500 text-white rounded hidden" onclick="addAllToCart()">Add All to Cart</button>
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

    async function addAllToCart() {
        const results = [];

        for (const seat of selectedSeats) {
            const payload = {
                seat_id: seat.seatId,
                screening_id: {{ $screening->id }},
                movie_title: seat.movieTitle,
                seat: seat.row + seat.seatNumber,
                price: seat.price
            };

            try {
                const response = await fetch(`/cart/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                results.push(data);
            } catch (error) {
                console.error('Error:', error);
                results.push({ success: false });
            }
        }

        // Processar todos os resultados após enviar todos os pedidos
        const successCount = results.filter(item => item.success).length;
        const errorCount = results.length - successCount;

        showMessage(`${successCount} tickets added to cart. ${errorCount} failed.`, 'success');
        selectedSeats = [];
        updateSelectedTickets();
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
