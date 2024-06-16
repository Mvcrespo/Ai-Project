<div>
    <table class="table-auto border-collapse w-full">
        <thead>
            <tr class="border-b-2 border-gray-400 dark:border-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left hidden lg:table-cell">ID</th>
                <th class="px-2 py-2 text-left">Customer Name</th>
                <th class="px-2 py-2 text-left">Total Price</th>
                <th class="px-2 py-2 text-left">Payment Type</th>
                <th class="px-2 py-2 text-left">Payment Ref</th>
                <th class="px-2 py-2 text-left">Date</th>
                @if($showView)
                    <th></th>
                @endif
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr class="border-b border-gray-400 dark:border-gray-500">
                    <td class="px-2 py-2 text-left hidden lg:table-cell">{{ $purchase->id }}</td>
                    <td class="px-2 py-2 text-left">{{ $purchase->customer_name }}</td>
                    <td class="px-2 py-2 text-left">${{ $purchase->total_price }}</td>
                    <td class="px-2 py-2 text-left">{{ $purchase->payment_type }}</td>
                    <td class="px-2 py-2 text-left">{{ $purchase->payment_ref }}</td>
                    <td class="px-2 py-2 text-left">{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}</td>
                    @if($showView)
                        @can('view', $purchase)
                            <td>
                                <x-table.icon-show class="ps-3 px-0.5"
                                    href="{{ route('purchases.show', ['purchase' => $purchase]) }}"/>
                            </td>
                        @else
                            <td></td>
                        @endcan
                    @endif
                    <td>
                        @if($purchase->receipt_pdf_filename)
                            <a href="{{ route('purchase.download', $purchase->id) }}" class="text-white px-3 py-1 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 00-1.414 0L11 14.586V3a1 1 0 10-2 0v11.586L4.707 10.293a1 1 0 00-1.414 1.414l6 6a1 1 0 001.414 0l6-6a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
