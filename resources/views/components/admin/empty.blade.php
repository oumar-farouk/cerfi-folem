@props(['titre' => 'Rien à afficher', 'texte' => null, 'icone' => 'liste'])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <span class="flex size-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-white/5">
        <x-admin.icone :nom="$icone" classe="size-7" />
    </span>
    <p class="mt-4 font-semibold text-gray-700 dark:text-gray-200">{{ $titre }}</p>
    @if ($texte)
        <p class="mt-1 max-w-md text-theme-sm text-gray-500 dark:text-gray-400">{{ $texte }}</p>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
