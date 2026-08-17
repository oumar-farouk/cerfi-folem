@props(['on'])

<div x-data="{ affiche: false }"
     x-init="@this.on('{{ $on }}', () => { affiche = true; setTimeout(() => affiche = false, 2500) })"
     x-show="affiche"
     x-transition
     style="display: none"
     {{ $attributes->merge(['class' => 'text-sm text-success-600']) }}>
    {{ $slot->isEmpty() ? 'Enregistré.' : $slot }}
</div>
