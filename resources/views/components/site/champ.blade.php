@props([
    'label' => null,
    'nom' => null,
    'requis' => false,
    'aide' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if ($label)
        <label @if ($nom) for="champ-{{ $nom }}" @endif class="block text-sm font-medium text-sand-700">
            {{ $label }}
            @if ($requis) <span class="text-error-500" aria-hidden="true">*</span> @endif
        </label>
    @endif

    <div class="mt-2">
        {{ $slot }}
    </div>

    @if ($aide)
        <p class="mt-1.5 text-xs text-sand-500">{{ $aide }}</p>
    @endif

    @if ($nom)
        @error($nom)
            <p class="mt-1.5 text-sm text-error-600">{{ $message }}</p>
        @enderror
    @endif
</div>
