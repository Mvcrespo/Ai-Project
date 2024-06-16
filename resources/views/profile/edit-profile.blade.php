@extends('layouts.main')

@section('header-title', $user->name)

@section('main')
<div class="flex flex-col space-y-6">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
        <div class="max-w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Edit Profile "{{ $user->name }}"
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 mb-6">
                        Click on "Save" button to store the information.
                    </p>
                </header>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <x-field.input name="name" label="Name" width="full" value="{{ old('name', $user->name) }}"/>
                            <x-field.input name="email" label="Email" width="full" type="email" value="{{ old('email', $user->email) }}"/>

                            @if ($customer)
                                <x-field.input name="nif" label="NIF" width="full" value="{{ old('nif', $customer->nif) }}"/>
                                <x-field.select name="payment_type" label="Payment Type" width="full" :options="[
                                    '' => 'Select a payment type...',
                                    'PAYPAL' => 'PayPal',
                                    'VISA' => 'Visa',
                                    'MBWAY' => 'MBWay'
                                ]" :value="old('payment_type', $customer->payment_type ?? '')" />
                                <x-field.input name="payment_ref" label="Payment Reference" width="full" value="{{ old('payment_ref', $customer->payment_ref) }}"/>
                            @endif
                        </div>

                        <div class="flex flex-col items-center space-y-4">
                            <div class="mb-4">
                                <label for="photo_file" class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-center">Profile Photo</label>
                                @php
                                    $photoUrl = $user->photo_filename ? asset('storage/photos/' . $user->photo_filename) : asset('/img/default_user.png');
                                @endphp
                                <img src="{{ $photoUrl }}" alt="Profile Photo" class="mb-2 w-52 h-65 object-cover mx-auto">
                                <input type="file" name="photo_file" id="photo_file" class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 dark:file:bg-gray-700 dark:file:text-gray-300 hover:file:bg-indigo-100 dark:hover:file:bg-gray-600"/>
                            </div>
                        </div>
                    </div>
                    <div class="flex mt-6">
                        <x-button element="submit" type="dark" text="Save" class="uppercase"/>
                        <x-button element="a" type="light" text="Cancel" class="uppercase ms-4" href="{{ url()->previous() }}"/>
                    </div>
                </form>

                <div class="mt-6 flex flex-col items-center">
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Email Verification Status</label>
                    @if ($user->email_verified_at)
                        <span class="text-green-500">Verified</span>
                    @else
                        <span class="text-red-500">Not Verified</span>
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <x-button element="submit" type="dark" text="Verify Email" class="mt-2"/>
                        </form>
                        @if (session('status') == 'verification-link-sent')
                            <div class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg mt-6">
        <div class="max-w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Purchases and Tickets
                    </h2>
                </header>

                @foreach($purchases as $purchase)
                    <div class="my-4 p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Purchase on {{ \Carbon\Carbon::parse($purchase->created_at)->format('Y-m-d H:i:s') }}</h3>
                            <div class="flex space-x-2">
                                <button class="bg-blue-500 text-white px-3 py-1 rounded-lg" onclick="toggleTickets('tickets-{{ $purchase->id }}')">Details</button>
                                @if ($purchase->receipt_pdf_filename)
                                    <a href="{{ route('purchase.download', $purchase->id) }}" class="bg-green-500 text-white px-3 py-1 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 00-1.414 0L11 14.586V3a1 1 0 10-2 0v11.586L4.707 10.293a1 1 0 00-1.414 1.414l6 6a1 1 0 001.414 0l6-6a1 1 0 000-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <p class="dark:text-gray-400">Purchase ID: {{ $purchase->id }}</p>
                        <div class="hidden dark:text-gray-400 mt-2" id="tickets-{{ $purchase->id }}">
                            <p >Total Price: ${{ $purchase->total_price }}</p>
                            <p>Payment Type: {{ $purchase->payment_type }}</p>
                            <p>Payment Ref: {{ $purchase->payment_ref }}</p>
                            <div class="mt-4">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Tickets</h4>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Screening</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Seat</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($purchase->tickets as $ticket)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $ticket->screening->movie->title }} at {{ $ticket->screening->theater->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $ticket->seat->row }}{{ $ticket->seat->seat_number }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">${{ $ticket->price }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $ticket->status }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                                    <div class="flex space-x-2">
                                                        <x-table.icon-show class="ps-3 px-0.5" href="{{ route('tickets.show', $ticket->id) }}"/>
                                                        @if($ticket->status == 'valid')
                                                            <a href="{{ route('tickets.download', $ticket->id) }}" class="text-white px-3 py-1 rounded-lg">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 00-1.414 0L11 14.586V3a1 1 0 10-2 0v11.586L4.707 10.293a1 1 0 00-1.414 1.414l6 6a1 1 0 001.414 0l6-6a1 1 0 000-1.414z" clip-rule="evenodd" />
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $purchases->links() }}
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    function toggleTickets(id) {
        const element = document.getElementById(id);
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    }
</script>
@endsection
