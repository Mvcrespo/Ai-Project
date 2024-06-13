@extends('layouts.main')

@section('header-title', 'Shopping Cart')

@section('main')
<div class="flex justify-center">
    <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
        @if($cart->isEmpty())
            <h3 class="text-xl w-96 text-center dark:text-gray-50">Cart is Empty</h3>
        @else
            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Seat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Movie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Screening ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Final Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-50">
                        @php
                            $totalInitialPrice = 0;
                            $totalDiscount = 0;
                            $totalFinalPrice = 0;
                            $configuration = \App\Models\Configuration::first();
                            $discount = Auth::check() && $configuration ? $configuration->registered_customer_ticket_discount : 0;
                        @endphp
                        @foreach ($cart as $item)
                            @php
                                $initialPrice = $item['price'];
                                $discountedPrice = $initialPrice - $discount;
                                $totalInitialPrice += $initialPrice;
                                $totalDiscount += $discount;
                                $totalFinalPrice += $discountedPrice;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item['seat'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item['movie_title'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item['screening_id'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($initialPrice, 2) }} $</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($discount, 2) }} $</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($discountedPrice, 2) }} $</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-table.icon-delete class="px-0.5"
                                    method="post"
                                    action="{{ route('cart.remove', ['seat_id' => $item['seat_id'], 'screening_id' => $item['screening_id']]) }}"/>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-12">
                <div class="flex justify-between space-x-12 items-end">
                    <div class="w-1/2">
                        <h3 class="mb-4 text-xl dark:text-gray-50">Shopping Cart Confirmation</h3>
                        <form action="{{ route('purchase.store') }}" method="post">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-4">
                                    <x-field.input name="name" label="Name" width="full" value="{{ old('name', Auth::user()?->name) }}" class="dark:text-gray-50"/>
                                    <x-field.input name="email" label="Email" width="full" type="email" value="{{ old('email', Auth::user()?->email) }}" class="dark:text-gray-50"/>
                                    <x-field.input name="nif" label="NIF" width="full" value="{{ old('nif', Auth::user()?->customer->nif ?? '') }}" class="dark:text-gray-50"/>
                                    <x-field.select name="payment_type" label="Payment Type" width="full" :options="[
                                        '' => 'Select a payment type...',
                                        'PAYPAL' => 'PayPal',
                                        'VISA' => 'Visa',
                                        'MBWAY' => 'MBWay'
                                    ]" :value="old('payment_type', Auth::user()?->customer->payment_type ?? '')" class="dark:text-gray-50"/>
                                    <x-field.input name="payment_reference" label="Payment Reference" width="full" value="{{ old('payment_reference', Auth::user()?->customer->payment_ref ?? '') }}" class="dark:text-gray-50"/>
                                    <x-field.input name="cvv" label="CVV" width="full" id="cvv-field" class="dark:text-gray-50 hidden"/>
                                </div>
                            </div>
                            <x-button element="submit" type="dark" text="Confirm Purchase" class="mt-4"/>
                        </form>
                    </div>
                    <div class="w-1/3 text-right dark:text-gray-50">
                        <p class="text-lg font-bold">Total Initial Price: {{ number_format($totalInitialPrice, 2) }} $</p>
                        <p class="text-lg font-bold">Total Discount: -{{ number_format($totalDiscount, 2) }} $</p>
                        <p class="text-lg font-bold">Total Final Price: {{ number_format($totalFinalPrice, 2) }} $</p>
                        @if($discount > 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">Discount applied: {{ number_format($discount, 2) }} $ per ticket</p>
                        @endif
                        <form action="{{ route('cart.clear') }}" method="post" class="mt-4">
                            @csrf
                            <x-button element="submit" type="danger" text="Clear Cart"/>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        toggleCVVField(document.querySelector('select[name="payment_type"]').value);

        document.querySelector('select[name="payment_type"]').addEventListener('change', function() {
            toggleCVVField(this.value);
        });
    });

    function toggleCVVField(paymentType) {
        var cvvField = document.getElementById('cvv-field');
        if (paymentType === 'VISA') {
            cvvField.classList.remove('hidden');
            cvvField.required = true;
        } else {
            cvvField.classList.add('hidden');
            cvvField.required = false;
        }
    }
</script>
