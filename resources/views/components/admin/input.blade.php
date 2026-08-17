@props(['nom' => null, 'type' => 'text'])

@php
    $base = 'w-full rounded-lg border bg-white px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs transition placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500';
    $bordure = $nom && $errors->has($nom)
        ? 'border-error-400 focus:border-error-400 focus:ring-error-500/15'
        : 'border-gray-300 dark:border-gray-700';
@endphp

<input type="{{ $type }}"
       @if ($nom) name="{{ $nom }}" id="champ-{{ $nom }}" @endif
       @if ($nom && $errors->has($nom)) aria-invalid="true" @endif
       {{ $attributes->merge(['class' => $base.' '.$bordure]) }}>
