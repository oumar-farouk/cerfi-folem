@props(['nom' => null, 'rows' => 4])

@php
    $base = 'w-full rounded-lg border bg-white px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs transition placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:bg-gray-900 dark:text-white/90';
    $bordure = $nom && $errors->has($nom)
        ? 'border-error-400'
        : 'border-gray-300 dark:border-gray-700';
@endphp

<textarea @if ($nom) name="{{ $nom }}" id="champ-{{ $nom }}" @endif
          rows="{{ $rows }}"
          {{ $attributes->merge(['class' => $base.' '.$bordure]) }}>{{ $slot }}</textarea>
