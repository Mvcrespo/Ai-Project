<div {{ $attributes->merge(['class' => 'hover:text-gray-900 dark:hover:text-gray-200']) }}>
    <a href="{{ $href }}">
        <svg class="hover:stroke-2 w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 17a2 2 0 1 0 4 0 2 2 0 0 0-4 0zM6 10V7a6 6 0 1 1 12 0v3M4 10h16v10H4V10z" />
        </svg>
    </a>
</div>
