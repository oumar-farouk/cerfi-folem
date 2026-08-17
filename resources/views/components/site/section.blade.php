@props([
    'id' => null,
    'surtitre' => null,
    'titre' => null,
    'intro' => null,
    'fond' => 'clair',
    'centre' => false,
])

@php
    $fonds = [
        'clair' => 'bg-sand-50 text-sand-900',
        'creme' => 'bg-sand-100 text-sand-900',
        'blanc' => 'bg-white text-sand-900',
        'sombre' => 'bg-brand-950 motif-mashrabiya text-sand-100',
        'vert' => 'bg-brand-800 text-white',
    ];
@endphp

<section @if ($id) id="{{ $id }}" @endif
         {{ $attributes->merge(['class' => 'scroll-mt-20 py-16 sm:py-20 '.($fonds[$fond] ?? $fonds['clair'])]) }}>
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        @if ($surtitre || $titre || $intro)
            <div @class(['max-w-2xl', 'mx-auto text-center' => $centre])>
                @if ($surtitre)
                    <p @class([
                        'text-xs font-semibold uppercase tracking-[0.14em]',
                        'text-accent-500' => in_array($fond, ['clair', 'creme', 'blanc']),
                        'text-accent-300' => in_array($fond, ['sombre', 'vert']),
                    ])>{{ $surtitre }}</p>
                @endif

                @if ($titre)
                    <h2 @class([
                        'mt-3 font-display text-3xl font-bold leading-tight sm:text-4xl',
                        'filet-titre' => ! $centre,
                    ])>{{ $titre }}</h2>
                @endif

                @if ($intro)
                    <p @class([
                        'mt-5 text-base leading-relaxed',
                        'text-sand-600' => in_array($fond, ['clair', 'creme', 'blanc']),
                        'text-sand-300' => in_array($fond, ['sombre', 'vert']),
                    ])>{{ $intro }}</p>
                @endif
            </div>
        @endif

        <div @class(['mt-12' => $surtitre || $titre || $intro])>
            {{ $slot }}
        </div>
    </div>
</section>
