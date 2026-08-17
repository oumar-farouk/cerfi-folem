@php
    /*
    | La barre de navigation change selon le contexte : sur la page d'accueil
    | on pointe vers les ancres de section, ailleurs on renvoie vers l'accueil.
    */
    $editionNav = $edition ?? \App\Models\Edition::active()->first();
    $prefixe = $ancres ? '' : route('accueil');

    $liens = [
        ['libelle' => 'Le forum', 'href' => $prefixe.'#le-forum'],
        ['libelle' => 'Programme', 'href' => $prefixe.'#programme'],
        ['libelle' => 'Intervenants', 'href' => $prefixe.'#intervenants'],
        ['libelle' => 'Partenaires', 'href' => $prefixe.'#partenaires'],
        ['libelle' => 'Infos pratiques', 'href' => $prefixe.'#infos'],
    ];
@endphp

<header class="sticky top-0 z-99 border-b border-sand-200/80 bg-sand-50/95 backdrop-blur"
        x-data="{ menuOuvert: false }"
        @keydown.escape.window="menuOuvert = false">

    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">

        <a href="{{ route('accueil') }}" class="flex shrink-0 items-center gap-2.5" aria-label="FOLEM, accueil">
            <img src="{{ asset('img/brand/folem-logo.png') }}" alt="FOLEM" class="h-10 w-auto sm:h-11">
        </a>

        <nav class="hidden items-center gap-7 lg:flex" aria-label="Navigation principale">
            @foreach ($liens as $lien)
                <a href="{{ $lien['href'] }}"
                   class="text-sm font-medium text-sand-700 transition hover:text-brand-700">{{ $lien['libelle'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('accueil') }}#retrouver-dossier"
               class="hidden rounded-lg border border-sand-300 px-4 py-2.5 text-sm font-semibold text-sand-700 transition hover:border-brand-500 hover:text-brand-700 sm:inline-flex">
                Payer / Récépissé
            </a>

            @if ($editionNav?->estOuverte())
                <a href="{{ route('inscription.form', $editionNav) }}"
                   class="inline-flex rounded-lg bg-accent-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-accent-600">
                    Je m'inscris
                </a>
            @endif

            <button type="button"
                    @click="menuOuvert = ! menuOuvert"
                    class="rounded-lg border border-sand-300 p-2.5 text-sand-700 lg:hidden"
                    :aria-expanded="menuOuvert"
                    aria-controls="menu-mobile"
                    aria-label="Ouvrir le menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path x-show="! menuOuvert" d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                    <path x-show="menuOuvert" d="M18 6 6 18M6 6l12 12" stroke-linecap="round" style="display: none"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div id="menu-mobile" x-show="menuOuvert" x-collapse class="border-t border-sand-200 lg:hidden" style="display: none">
        <nav class="mx-auto max-w-6xl px-4 py-3 sm:px-6" aria-label="Navigation mobile">
            <ul class="flex flex-col">
                @foreach ($liens as $lien)
                    <li>
                        <a href="{{ $lien['href'] }}" @click="menuOuvert = false"
                           class="block rounded-lg px-3 py-3 text-sm font-medium text-sand-700 hover:bg-sand-100">
                            {{ $lien['libelle'] }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="{{ route('accueil') }}#retrouver-dossier" @click="menuOuvert = false"
                       class="block rounded-lg px-3 py-3 text-sm font-medium text-sand-700 hover:bg-sand-100">
                        Payer / Récépissé
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
