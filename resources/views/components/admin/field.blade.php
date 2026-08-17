@props([
    'label' => null,
    'nom' => null,
    'requis' => false,
    'aide' => null,
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label @if ($nom) for="champ-{{ $nom }}" @endif
               class="block text-theme-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if ($requis) <span class="text-error-500" aria-hidden="true">*</span> @endif
        </label>
    @endif

    {{ $slot }}

    @if ($aide)
        <p class="text-theme-xs text-gray-400">{{ $aide }}</p>
    @endif

    @if ($nom)
        @error($nom)
            <p class="text-theme-xs text-error-600">{{ $message }}</p>
        @enderror
    @endif
</div>
