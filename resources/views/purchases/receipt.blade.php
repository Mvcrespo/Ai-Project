<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Purchase Receipt</h1>
        </div>
        <div class="mb-6">
            <p class="text-lg text-gray-700"><strong>Purchase Number:</strong> {{ $purchase->id }}</p>
            <p class="text-lg text-gray-700"><strong>Date of Purchase:</strong> {{ $purchase->created_at->format('d-m-Y H:i:s') }}</p>
            <p class="text-lg text-gray-700"><strong>Payment Type:</strong> {{ $purchase->payment_type }}</p>
            <p class="text-lg text-gray-700"><strong>Payment Reference:</strong> {{ $purchase->payment_ref }}</p>
            <p class="text-lg text-gray-700"><strong>Name:</strong> {{ $purchase->customer_name }}</p>
            <p class="text-lg text-gray-700"><strong>Email:</strong> {{ $purchase->customer_email }}</p>
            <p class="text-lg text-gray-700"><strong>NIF:</strong> {{ $purchase->nif }}</p>
        </div>
        <table class="w-full table-auto mb-6">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2 text-left text-gray-600">Ticket ID</th>
                    <th class="px-4 py-2 text-left text-gray-600">Theater</th>
                    <th class="px-4 py-2 text-left text-gray-600">Movie</th>
                    <th class="px-4 py-2 text-left text-gray-600">Screening Date</th>
                    <th class="px-4 py-2 text-left text-gray-600">Seat</th>
                    <th class="px-4 py-2 text-left text-gray-600">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->tickets as $ticket)
                    <tr class="bg-white border-b">
                        <td class="px-4 py-2 text-gray-700">{{ $ticket->id }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $ticket->screening->theater->name }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $ticket->screening->movie->title }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $ticket->screening->date }} - {{ $ticket->screening->start_time }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</td>
                        <td class="px-4 py-2 text-gray-700">${{ number_format($ticket->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right">
            <p class="text-xl font-semibold text-gray-800"><strong>Total:</strong> ${{ number_format($purchase->total_price, 2) }}</p>
        </div>
    </div>
</body>
</html>
