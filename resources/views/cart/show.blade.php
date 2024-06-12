@extends('layouts.main')

@section('header-title', 'Shopping Cart')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            @if($cart->isEmpty())
                <h3 class="text-xl w-96 text-center">Cart is Empty</h3>
            @else
            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Ticket ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Seat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Movie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($cart as $ticket)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->price }} $</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->screening->movie->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->screening->date }} & {{ $ticket->screening->start_time }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-table.icon-delete class="px-0.5"
                                        method="post"
                                        action="{{ route('cart.remove', ['ticket' => $ticket]) }}"/>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-12">
                <div class="flex justify-between space-x-12 items-end">
                    <div>
                        <h3 class="mb-4 text-xl">Shopping Cart Confirmation</h3>
                        <form action="{{ route('cart.confirm') }}" method="post">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-4">
                                    <x-field.input name="name" label="Name" width="full" value="{{ old('name', Auth::user()?->name) }}"/>
                                    <x-field.input name="email" label="Email" width="full" type="email" value="{{ old('email', Auth::user()?->email) }}"/>
                                    <x-field.input name="nif" label="NIF" width="full" value="{{ old('nif', Auth::user()?->customer->nif ?? '') }}"/>
                                    <x-field.select name="payment_type" label="Payment Type" width="full" :options="[
                                        '' => 'Select a payment type...',
                                        'PAYPAL' => 'PayPal',
                                        'VISA' => 'Visa',
                                        'MBWAY' => 'MBWay'
                                    ]" :value="old('payment_type', Auth::user()?->customer->payment_type ?? '')" />
                                    <x-field.input name="payment_reference" label="Payment Reference" width="full" value="{{ old('payment_reference', Auth::user()?->customer->payment_ref ?? '') }}"/>
                                </div>
                            </div>
                            <x-button element="submit" type="dark" text="Confirm Purchase" class="mt-4"/>
                        </form>
                    </div>
                    <div>
                        <form action="{{ route('cart.clear') }}" method="post">
                            @csrf
                            <x-button element="submit" type="danger" text="Clear Cart" class="mt-4"/>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
