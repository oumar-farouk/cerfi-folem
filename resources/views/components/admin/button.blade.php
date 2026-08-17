@props([
    'variante' => 'primaire',
    'href' => null,
    'type' => 'submit',
    'icone' => null,
    'taille' => 'normal',
])

@php
    $classes = collect([
        'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        match ($taille) {
            'petit' => 'px-3 py-2 text-theme-xs',
            'grand' => 'px-6 py-3.5 text-theme-sm',
            default => 'px-4 py-2.5 text-theme-sm',
        },
        match ($variante) {
            'primaire' => 'bg-brand-600 text-white shadow-theme-xs hover:bg-brand-700',
            'accent' => 'bg-accent-500 text-white shadow-theme-xs hover:bg-accent-600',
            'secondaire' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-white/5',
            'danger' => 'bg-error-600 text-white hover:bg-error-700',
            'discret' => 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5',
            default => 'bg-brand-600 text-white hover:bg-brand-700',
        },
    ])->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icone) <x-admin.icone :nom="$icone" classe="size-4" /> @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icone) <x-admin.icone :nom="$icone" classe="size-4" /> @endif
        {{ $slot }}
    </button>
@endif
