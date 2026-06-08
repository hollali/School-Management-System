@props(['disabled' => false, 'toggle' => false])

@php
    $isPassword = ($attributes->get('type') ?? '') === 'password';
@endphp

@if($toggle && $isPassword)
    <div class="relative">
        <input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
        <button type="button" aria-label="Toggle password visibility" title="Toggle password visibility" class="absolute inset-y-0 end-0 px-3 flex items-center text-gray-500" onclick="(function(btn){
            const input = btn.closest('div').querySelector('input');
            if (input.type === 'password') { input.type = 'text'; btn.innerHTML = '🙈'; }
            else { input.type = 'password'; btn.innerHTML = '👁️'; }
        })(this)">👁️</button>
    </div>
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
@endif
