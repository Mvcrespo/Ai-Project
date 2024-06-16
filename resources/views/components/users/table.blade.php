<div {{ $attributes }}>
    <table class="min-w-full table-auto border-collapse">
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left hidden lg:table-cell">Name</th>
                <th class="px-2 py-2 text-left">Email</th>
                <th class="px-2 py-2 text-left">Type</th>
                @if($showView)
                    <th></th>
                @endif
                @if($showEdit)
                    <th></th>
                @endif
                @if($showDelete)
                    <th></th>
                @endif
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-b border-b-gray-400 dark:border-b-gray-500 {{ $user->trashed() ? 'bg-red-100 dark:bg-red-900' : '' }}">
                    <td class="px-2 py-2 text-left hidden lg:table-cell">{{ $user->name }}</td>
                    <td class="px-2 py-2 text-left">{{ $user->email }}</td>
                    <td class="px-2 py-2 text-left">{{ $user->type }}</td>
                    @if($showView && !$user->trashed() && $user->type != 'C')
                        @can('view', $user)
                            <td>
                                <x-table.icon-show class="ps-3 px-0.5"
                                    href="{{ route('users.show', ['user' => $user]) }}"/>
                            </td>
                        @else
                            <td></td>
                        @endcan
                    @endif
                    @if($showEdit && !$user->trashed() && $user->type != 'C')
                        @can('update', $user)
                            <td>
                                <x-table.icon-edit class="px-0.5"
                                    href="{{ route('users.edit', ['user' => $user]) }}"/>
                            </td>
                        @else
                            <td></td>
                        @endcan
                    @endif
                    @if(!$user->trashed() && $showDelete && $user->type != 'C')
                        @can('delete', $user)
                            <td>
                                <x-table.icon-delete class="px-0.5"
                                    action="{{ route('users.destroy', ['user' => $user]) }}"/>
                            </td>
                        @else
                            <td></td>
                        @endcan
                    @endif
                    @if($user->type == 'C')
                        <td>
                            @if($user->blocked)
                                <form method="POST" action="{{ route('users.unblock', ['user' => $user]) }}">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <x-table.icon-lock class="px-0.5" href="#"/>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('users.block', ['user' => $user]) }}">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                        <x-table.icon-lock class="px-0.5" href="#"/>
                                    </button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
