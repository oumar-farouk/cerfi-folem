@props([
    'href' => null,
    'variante' => 'accent',
    'type' => 'submit',
    'taille' => 'normal',
])

@php
    $classes = collect([
        'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        $taille === 'grand' ? 'px-7 py-4 text-base' : ($taille === 'petit' ? 'px-4 py-2 text-sm' : 'px-6 py-3.5 text-sm'),
        match ($variante) {
            'accent' => 'bg-accent-500 text-white shadow-sm hover:bg-accent-600',
            'vert' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-700',
            'clair' => 'bg-white text-brand-800 shadow-sm hover:bg-sand-100',
            'contour-clair' => 'border border-white/40 text-white hover:border-white hover:bg-white/10',
            'contour' => 'border border-sand-300 text-sand-800 hover:border-brand-500 hover:text-brand-700',
            default => 'bg-accent-500 text-white hover:bg-accent-600',
        },
    ])->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
