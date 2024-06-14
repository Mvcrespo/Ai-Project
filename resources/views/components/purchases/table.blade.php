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
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
