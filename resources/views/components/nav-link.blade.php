@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-lg bg-sky-50 text-sky-700 border-l-4 border-sky-600 transition duration-150 ease-in-out'
            : 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 border-l-4 border-transparent transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
