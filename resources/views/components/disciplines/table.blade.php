<div {{ $attributes }}>
    <table class="table-auto border-collapse">
        <thead>
        <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
            <th class="px-2 py-2 text-left hidden sm:table-cell dark:text-gray-100">Abbreviation</th>
            <th class="px-2 py-2 text-left dark:text-gray-100">Name</th>
            @if($showCourse)
                <th class="px-2 py-2 text-left hidden md:table-cell dark:text-gray-100">Course</th>
            @endif
            <th class="px-2 py-2 text-right hidden md:table-cell dark:text-gray-100">Year</th>
            <th class="px-2 py-2 text-left hidden md:table-cell dark:text-gray-100">Semester</th>
            <th class="px-2 py-2 text-right hidden lg:table-cell dark:text-gray-100">ECTS</th>
            <th class="px-2 py-2 text-right hidden lg:table-cell dark:text-gray-100">Hours</th>
            <th class="px-2 py-2 text-left hidden lg:table-cell dark:text-gray-100">Optional</th>
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
        @foreach ($disciplines as $discipline)
            <tr class="border-b border-b-gray-400 dark:border-b-gray-500 dark:text-gray-100">
                <td class="px-2 py-2 text-left hidden sm:table-cell dark:text-gray-100">{{ $discipline->abbreviation }}</td>
                <td class="px-2 py-2 text-left dark:text-gray-100">{{ $discipline->name }}</td>
                @if($showCourse)
                    <td class="px-2 py-2 text-left hidden md:table-cell dark:text-gray-100">{{ $discipline->courseRef->name }}</td>
                @endif
                <td class="px-2 py-2 text-right hidden md:table-cell dark:text-gray-100">{{ $discipline->year }}</td>
                <td class="px-2 py-2 text-left hidden md:table-cell dark:text-gray-100">{{ $discipline->semesterDescription }}</td>
                <td class="px-2 py-2 text-right hidden lg:table-cell dark:text-gray-100">{{ $discipline->ECTS }}</td>
                <td class="px-2 py-2 text-right hidden lg:table-cell dark:text-gray-100">{{ $discipline->hours }}</td>
                <td class="px-2 py-2 text-left hidden lg:table-cell dark:text-gray-100">{{ $discipline->optional ? 'optional' : '' }}</td>
                @if($showView)
                    <td>
                        <x-table.icon-show class="ps-3 px-0.5 dark:text-gray-100"
                        href="{{ route('disciplines.show', ['discipline' => $discipline]) }}"/>
                    </td>
                @endif
                @if($showEdit)
                    <td>
                        <x-table.icon-edit class="px-0.5"
                        href="{{ route('disciplines.edit', ['discipline' => $discipline]) }}"/>
                    </td>
                @endif
                @if($showDelete)
                    <td>
                        <x-table.icon-delete class="px-0.5"
                        action="{{ route('disciplines.destroy', ['discipline' => $discipline]) }}"/>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
