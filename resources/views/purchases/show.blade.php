@extends('layouts.admin')

@section('header-title', 'Purchase Details')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden
                    shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Purchase Details
            </h2>
            <div class="mt-4">
                <p><strong>ID:</strong> {{ $purchase->id }}</p>
                <p><strong>Customer Name:</strong> {{ $purchase->customer_name }}</p>
                <p><strong>Total Price:</strong> ${{ $purchase->total_price }}</p>
                <p><strong>Payment Type:</strong> {{ $purchase->payment_type }}</p>
                <p><strong>Payment Ref:</strong> {{ $purchase->payment_ref }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}</p>
                <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($purchase->created_at)->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tickets</h3>
                <table class="table-auto border-collapse">
                    <thead>
                        <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                            <th class="px-2 py-2 text-left">Screening</th>
                            <th class="px-2 py-2 text-left">Seat</th>
                            <th class="px-2 py-2 text-left">Price</th>
                            <th class="px-2 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->tickets as $ticket)
                            <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                                <td class="px-2 py-2 text-left">{{ $ticket->screening->movie->title }} at {{ $ticket->screening->theater->name }}</td>
                                <td class="px-2 py-2 text-left">{{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</td>
                                <td class="px-2 py-2 text-left">${{ $ticket->price }}</td>
                                <td class="px-2 py-2 text-left">{{ $ticket->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
