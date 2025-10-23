@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-yellow-400 text-base font-medium text-white bg-gray-900 transition duration-150 ease-in-out'
            : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
