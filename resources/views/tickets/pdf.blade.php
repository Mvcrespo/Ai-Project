<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .customer-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .customer-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 16px;
        }
        .customer-details {
            font-size: 18px;
        }
        .customer-details p {
            margin: 0;
        }
        .ticket {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            page-break-after: always;
        }
        .ticketlast {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .ticket p {
            font-size: 18px;
            margin-bottom: 8px;
        }
        .ticket p strong {
            display: inline-block;
            width: 120px;
        }
        .ticket img {
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <h1>Tickets for Purchase #{{ $purchase->id }}</h1>
    <div class="customer-info">
        @if ($purchase->customer && $purchase->customer->photo_filename)
            <img src="{{ asset('storage/photos/' . $purchase->customer->photo_filename) }}" alt="Customer Avatar">
        @else
            <img src="{{ asset('/img/default_user.png') }}" alt="Default Avatar">
        @endif
        <div class="customer-details">
            <p><strong>Name:</strong> {{ $purchase->customer_name }}</p>
            <p><strong>Email:</strong> {{ $purchase->customer_email }}</p>
        </div>
    </div>

    @isset($tickets)
        @foreach($tickets as $index => $ticket)
            @if ($index === count($tickets) - 1)
                <div class="ticketlast">
            @else
                <div class="ticket">
            @endif
                    <p><strong>Ticket ID:</strong> {{ $ticket->id }}</p>
                    <p><strong>Movie:</strong> {{ $ticket->screening->movie->title }}</p>
                    <p><strong>Theater:</strong> {{ $ticket->screening->theater->name }}</p>
                    <p><strong>Date & Time:</strong> {{ $ticket->screening->date }} {{ $ticket->screening->start_time }}</p>
                    <p><strong>Seat:</strong> {{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</p>
                    <p><strong>QR Code:</strong></p>
                    <img class="mt-2" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $ticket->qrcode_url }}" alt="QR Code">
                </div>
        @endforeach
    @else
        <div class="ticketlast">
            <p><strong>Ticket ID:</strong> {{ $ticket->id }}</p>
            <p><strong>Movie:</strong> {{ $ticket->screening->movie->title }}</p>
            <p><strong>Theater:</strong> {{ $ticket->screening->theater->name }}</p>
            <p><strong>Date & Time:</strong> {{ $ticket->screening->date }} {{ $ticket->screening->start_time }}</p>
            <p><strong>Seat:</strong> {{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</p>
            <p><strong>QR Code:</strong></p>
            <img class="mt-2" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $ticket->qrcode_url }}" alt="QR Code">
        </div>
    @endisset
</body>
</html>
