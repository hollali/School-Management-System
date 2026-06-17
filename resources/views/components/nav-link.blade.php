@props(['active' => false, 'label' => ''])

@php
$baseClasses = 'flex items-center w-full text-sm rounded-lg transition-all duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-900';
$activeClasses = 'bg-sky-600 dark:bg-sky-600 text-white font-semibold shadow-sm shadow-sky-500/20';
$inactiveClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-slate-300 dark:hover:bg-white/[0.10] dark:hover:text-white font-medium';
@endphp

<a {{ $attributes->merge(['class' => trim("$baseClasses " . ($active ? $activeClasses : $inactiveClasses))]) }}
   x-bind:class="collapsed ? 'justify-center mx-2 py-3' : 'gap-3 px-3 py-2.5'"
   x-bind:title="collapsed ? '{{ $label }}' : ''"
   @if($active) aria-current="page" @endif>
    {{ $slot }}
</a>
