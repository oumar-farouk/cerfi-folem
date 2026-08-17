@props([
    'titre' => null,
    'description' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]']) }}>
    @if ($titre || $description || isset($actions))
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800 md:px-6">
            <div class="min-w-0">
                @if ($titre)
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $titre }}</h3>
                @endif
                @if ($description)
                    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div @class(['px-5 py-5 md:px-6' => $padding])>
        {{ $slot }}
    </div>
</div>
