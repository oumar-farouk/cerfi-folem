@props(['title' => '', 'fil' => []])

<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $title }}</h1>

    <nav aria-label="Fil d'Ariane">
        <ol class="flex flex-wrap items-center gap-1.5 text-theme-sm text-gray-500">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 dark:hover:text-brand-300">Accueil</a>
            </li>
            @foreach ($fil as $libelle => $url)
                <li aria-hidden="true">/</li>
                <li>
                    @if ($url)
                        <a href="{{ $url }}" class="hover:text-brand-600 dark:hover:text-brand-300">{{ $libelle }}</a>
                    @else
                        <span class="text-gray-800 dark:text-white/90">{{ $libelle }}</span>
                    @endif
                </li>
            @endforeach
            @if (empty($fil))
                <li aria-hidden="true">/</li>
                <li class="text-gray-800 dark:text-white/90">{{ $title }}</li>
            @endif
        </ol>
    </nav>
</div>
