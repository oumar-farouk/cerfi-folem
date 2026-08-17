@props([
    'libelle',
    'valeur',
    'icone' => 'grille',
    'variation' => null,
    'sens' => 'neutre',
    'note' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] md:p-6']) }}>
    <span class="flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
        <x-admin.icone :nom="$icone" classe="size-6" />
    </span>

    <div class="mt-5 flex items-end justify-between gap-3">
        <div class="min-w-0">
            <span class="block text-theme-sm text-gray-500 dark:text-gray-400">{{ $libelle }}</span>
            <h4 class="mt-1 truncate text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $valeur }}</h4>
            @if ($note)
                <span class="mt-1 block text-theme-xs text-gray-400">{{ $note }}</span>
            @endif
        </div>

        @if ($variation !== null)
            <span @class([
                'inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-0.5 text-theme-xs font-medium',
                'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' => $sens === 'haut',
                'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400' => $sens === 'bas',
                'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-300' => $sens === 'neutre',
            ])>{{ $variation }}</span>
        @endif
    </div>
</div>
