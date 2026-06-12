@props(['active' => false, 'collapsed' => false, 'label' => ''])

@php
$expanded = $collapsed
    ? 'flex items-center justify-center px-3 py-2.5 text-sm rounded-lg border-l-4 border-transparent transition duration-150 ease-in-out'
    : 'flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg border-l-4 transition duration-150 ease-in-out';

$activeClasses = 'bg-sky-50 text-sky-700 border-sky-600 font-semibold';
$inactiveClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 border-transparent font-medium';

$classes = ($active ?? false)
    ? "$expanded $activeClasses"
    : "$expanded $inactiveClasses";
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}
   @if($collapsed && $label)
   title="{{ $label }}"
   @endif>
    {{ $slot }}
</a>
