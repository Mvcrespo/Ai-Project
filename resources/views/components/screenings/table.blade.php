<div {{ $attributes }}>
    <table class="table-auto border-collapse w-full">
        <thead>
        <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
            <th class="px-2 py-2 text-left">Theater</th>
            <th class="px-2 py-2 text-left">Movie</th>
            @if($showView)
                <th></th>
            @endif
            @if($showEdit)
                <th></th>
            @endif
            @if($showDelete)
                <th></th>
            @endif
        </tr>
        </thead>
        <tbody>
        @forelse ($screenings as $screening)
            <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-2 py-2 text-left">{{ $screening->theater->name }}</td>
                <td class="px-2 py-2 text-left">{{ $screening->movie->title }}</td>
                @if($showView)
                    <td>
                        @can('view', $screening)
                            <x-table.icon-show class="ps-3 px-0.5" href="{{ route('screenings.show', ['screening' => $screening]) }}"/>
                        @endcan
                    </td>
                @endif
                @if($showEdit)
                    <td>
                        @can('update', $screening)
                            <x-table.icon-edit class="px-0.5" href="{{ route('screenings.edit', ['screening' => $screening]) }}"/>
                        @endcan
                    </td>
                @endif
                @if($showDelete)
                @can('delete', $screening)
                    <td>
                        <x-table.icon-delete class="px-0.5"
                            action="{{ route('screenings.destroy', ['screening' => $screening]) }}"/>
                    </td>
                @endcan
            @endif
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-2 py-2 text-center">No screenings found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
