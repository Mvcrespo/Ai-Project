@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
    $seatLayout = $seatLayout ?? [];
@endphp

<div class="mt-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Seat Layout</h3>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
        @if ($mode !== 'show')
            Enter the number of rows and columns, then click "Generate Layout" to create the seat layout.
        @else
            Current seat layout of the theater.
        @endif
    </p>

    @if ($mode !== 'show')
        <div class="flex space-x-4 mb-4">
            <div>
                <label for="num-rows" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Rows</label>
                <input type="number" id="num-rows" name="num_rows" value="{{ old('num_rows', $mode === 'create' ? 0 : (count($seatLayout) > 0 ? count($seatLayout) : 0)) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:text-gray-300">
            </div>
            <div>
                <label for="num-cols" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Columns</label>
                <input type="number" id="num-cols" name="num_cols" value="{{ old('num_cols', $mode === 'create' ? 0 : (count($seatLayout) > 0 ? max(array_map('count', $seatLayout)) : 0)) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:text-gray-300">
            </div>
        </div>

        <button type="button" onclick="generateLayout(true)" class="px-4 py-2 bg-blue-500 text-white rounded">Generate Layout</button>
    @endif

    <div class="mt-4">
        <p class="text-sm">
            <span class="inline-block w-4 h-4 bg-green-500 mr-2"></span> Available
            <span class="inline-block w-4 h-4 bg-red-500 ml-4 mr-2"></span> Unavailable
        </p>
    </div>

    <div id="seat-layout" class="flex flex-wrap justify-center bg-gray-100 dark:bg-gray-800 p-4 mt-4 rounded-lg shadow-inner">
    </div>

    @if ($mode !== 'show')
        <input type="hidden" name="seat_layout" id="seat-layout-input" value="{{ old('seat_layout', json_encode($seatLayout)) }}">
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const seatLayoutInput = document.getElementById('seat-layout-input');
            const initialLayout = seatLayoutInput ? JSON.parse(seatLayoutInput.value || '{}') : @json($seatLayout);

            if (document.getElementById('num-rows') && document.getElementById('num-cols')) {
                document.getElementById('num-rows').value = Object.keys(initialLayout).length || 0;
                document.getElementById('num-cols').value = Math.max(...Object.values(initialLayout).map(seats => seats.length)) || 0;
            }

            generateLayout(false, initialLayout);
        });

        function generateLayout(resetAllToAvailable = false, initialLayout = {}) {
            const seatLayoutContainer = document.getElementById('seat-layout');
            const numRowsElement = document.getElementById('num-rows');
            const numColsElement = document.getElementById('num-cols');
            const numRows = numRowsElement ? numRowsElement.value : Object.keys(initialLayout).length;
            const numCols = numColsElement ? numColsElement.value : Math.max(...Object.values(initialLayout).map(seats => seats.length));
            seatLayoutContainer.innerHTML = '';

            let seatLayout = {};

            for (let i = 0; i < numRows; i++) {
                const rowLabel = String.fromCharCode(65 + i);
                const rowDiv = document.createElement('div');
                rowDiv.classList.add('flex', 'w-full', 'mb-2', 'justify-center');
                const rowLabelSpanStart = document.createElement('span');
                rowLabelSpanStart.classList.add('mr-4', 'flex', 'items-center', 'font-bold');
                rowLabelSpanStart.textContent = rowLabel;
                rowDiv.appendChild(rowLabelSpanStart);

                seatLayout[rowLabel] = [];

                for (let j = 1; j <= numCols; j++) {
                    const seatDiv = document.createElement('div');
                    seatDiv.id = `seat-${rowLabel}-${j}`;
                    seatDiv.classList.add('w-8', 'h-8', 'm-1', 'text-white', 'flex', 'items-center', 'justify-center', 'cursor-pointer', 'seat');

                    if (resetAllToAvailable) {
                        seatDiv.classList.add('bg-green-500');
                        seatLayout[rowLabel].push(j);
                    } else {
                        if (initialLayout[rowLabel] && initialLayout[rowLabel].includes(j)) {
                            seatDiv.classList.add('bg-green-500');
                            seatLayout[rowLabel].push(j);
                        } else {
                            seatDiv.classList.add('bg-red-500');
                        }
                    }

                    seatDiv.textContent = j;
                    if (!@json($readonly)) {
                        seatDiv.setAttribute('onclick', `toggleSeat('${rowLabel}', ${j})`);
                    }
                    rowDiv.appendChild(seatDiv);
                }

                const rowLabelSpanEnd = document.createElement('span');
                rowLabelSpanEnd.classList.add('ml-4', 'flex', 'items-center', 'font-bold');
                rowLabelSpanEnd.textContent = rowLabel;
                rowDiv.appendChild(rowLabelSpanEnd);

                seatLayoutContainer.appendChild(rowDiv);
            }

            if (!@json($readonly)) {
                document.getElementById('seat-layout-input').value = JSON.stringify(seatLayout);
            }
        }

        function toggleSeat(row, seat, updateInput = true) {
            const seatElement = document.getElementById(`seat-${row}-${seat}`);
            const seatLayoutInput = document.getElementById('seat-layout-input');
            let seatLayout = JSON.parse(seatLayoutInput.value || '{}');

            if (seatElement.classList.contains('bg-green-500')) {
                seatElement.classList.remove('bg-green-500');
                seatElement.classList.add('bg-red-500');

                seatLayout[row] = seatLayout[row].filter(s => s !== seat);
                if (seatLayout[row].length === 0) {
                    delete seatLayout[row];
                }
            } else {
                seatElement.classList.remove('bg-red-500');
                seatElement.classList.add('bg-green-500');

                if (!seatLayout[row]) {
                    seatLayout[row] = [];
                }

                seatLayout[row].push(seat);
            }

            if (updateInput) {
                document.getElementById('seat-layout-input').value = JSON.stringify(seatLayout);
            }
        }
    </script>
</div>
