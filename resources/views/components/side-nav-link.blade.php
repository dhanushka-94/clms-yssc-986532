@props(['active'])

@php
$classes = ($active ?? false)
    ? 'flex items-center px-3 py-2 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:text-blue-800 hover:bg-blue-100 transition-colors duration-150'
    : 'flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a> 