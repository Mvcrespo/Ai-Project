<!DOCTYPE html>
<html>
<head>
    <title>Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .customer-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .customer-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .customer-details {
            display: flex;
            flex-direction: column;
        }
        .ticket {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 5px;
        }
        .ticket:not(:first-child) {
            page-break-before: always; /* Adiciona uma quebra de página antes de cada ticket, exceto o primeiro */
        }
        .ticket img {
            margin-top: 10px;
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
            <p><strong>Name:</strong> {{ $purchase->customer_name ?? 'Guest' }}</p>
            <p><strong>Email:</strong> {{ $purchase->customer_email ?? 'Not Provided' }}</p>
        </div>
    </div>

    @isset($tickets)
        @foreach($tickets as $ticket)
            <div class="ticket">
                <p><strong>Ticket ID:</strong> {{ $ticket->id }}</p>
                <p><strong>Movie:</strong> {{ $ticket->screening->movie->title }}</p>
                <p><strong>Theater:</strong> {{ $ticket->screening->theater->name }}</p>
                <p><strong>Date & Time:</strong> {{ $ticket->screening->date }} {{ $ticket->screening->start_time }}</p>
                <p><strong>Seat:</strong> {{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</p>
                <p><strong>QR Code:</strong></p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $ticket->qrcode_url }}" alt="QR Code">
            </div>
        @endforeach
    @else
        <div class="ticket">
            <p><strong>Ticket ID:</strong> {{ $ticket->id }}</p>
            <p><strong>Movie:</strong> {{ $ticket->screening->movie->title }}</p>
            <p><strong>Theater:</strong> {{ $ticket->screening->theater->name }}</p>
            <p><strong>Date & Time:</strong> {{ $ticket->screening->date }} {{ $ticket->screening->start_time }}</p>
            <p><strong>Seat:</strong> {{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</p>
            <p><strong>QR Code:</strong></p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $ticket->qrcode_url }}" alt="QR Code">
        </div>
    @endisset
</body>
</html>
