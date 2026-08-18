@php
    /*
    | Navigation du back-office.
    |
    | Les entrées liées à une édition (programme, intervenants, partenaires,
    | galerie, tarifs) n'ont de sens qu'avec une édition sélectionnée : on
    | prend l'édition active, et à défaut la plus récente, pour éviter des
    | liens morts dans la barre latérale.
    */
    $editionCourante = \App\Models\Edition::active()->first()
        ?? \App\Models\Edition::orderByDesc('annee')->first();

    $groupes = [
        [
            'titre' => 'Pilotage',
            'items' => [
                [
                    'libelle' => 'Tableau de bord',
                    'url' => route('admin.dashboard'),
                    'actif' => request()->routeIs('admin.dashboard'),
                    'icone' => 'grille',
                ],
                [
                    'libelle' => 'Inscriptions',
                    'url' => route('admin.inscriptions.index'),
                    'actif' => request()->routeIs('admin.inscriptions.*'),
                    'icone' => 'liste',
                ],
            ],
        ],
        [
            'titre' => 'Éditions',
            'items' => array_values(array_filter([
                [
                    'libelle' => 'Toutes les éditions',
                    'url' => route('admin.editions.index'),
                    'actif' => request()->routeIs('admin.editions.index')
                        || request()->routeIs('admin.editions.create')
                        || request()->routeIs('admin.editions.edit'),
                    'icone' => 'calendrier',
                ],
                $editionCourante ? [
                    'libelle' => 'Programme',
                    'url' => route('admin.editions.programmes.index', $editionCourante),
                    'actif' => request()->routeIs('admin.editions.programmes.*') || request()->routeIs('admin.programmes.*'),
                    'icone' => 'horloge',
                ] : null,
                $editionCourante ? [
                    'libelle' => 'Intervenants',
                    'url' => route('admin.editions.intervenants.index', $editionCourante),
                    'actif' => request()->routeIs('admin.editions.intervenants.*') || request()->routeIs('admin.intervenants.*'),
                    'icone' => 'utilisateurs',
                ] : null,
                $editionCourante ? [
                    'libelle' => 'Partenaires',
                    'url' => route('admin.editions.partenaires.index', $editionCourante),
                    'actif' => request()->routeIs('admin.editions.partenaires.*') || request()->routeIs('admin.partenaires.*'),
                    'icone' => 'poignee',
                ] : null,
                $editionCourante ? [
                    'libelle' => 'Galerie',
                    'url' => route('admin.editions.galerie.index', $editionCourante),
                    'actif' => request()->routeIs('admin.editions.galerie.*'),
                    'icone' => 'image',
                ] : null,
                $editionCourante ? [
                    'libelle' => 'Grille tarifaire',
                    'url' => route('admin.editions.prix.index', $editionCourante),
                    'actif' => request()->routeIs('admin.editions.prix.*'),
                    'icone' => 'monnaie',
                ] : null,
            ])),
        ],
        [
            'titre' => 'Paramétrage',
            'items' => [
                [
                    'libelle' => 'Régions',
                    'url' => route('admin.regions.index'),
                    'actif' => request()->routeIs('admin.regions.*'),
                    'icone' => 'carte',
                ],
                [
                    'libelle' => 'Profils',
                    'url' => route('admin.profils.index'),
                    'actif' => request()->routeIs('admin.profils.*'),
                    'icone' => 'badge',
                ],
                [
                    'libelle' => 'Utilisateur',
                    'url' => route('admin.users.index'),
                    'actif' => request()->routeIs('admin.users.*'),
                    'icone' => 'badge',
                ],
            ],
        ],
    ];
@endphp

<aside
    class="fixed left-0 top-0 z-99999 flex h-screen flex-col border-r border-gray-200 bg-white px-5 transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen,
    }"
    @mouseenter="if (! $store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)"
    aria-label="Navigation principale">

    {{-- Logo --}}
    <div class="flex items-center py-7"
         :class="(! $store.sidebar.isExpanded && ! $store.sidebar.isHovered && ! $store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-between'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/brand/folem-icone.png') }}" alt="" class="h-9 w-9 shrink-0 object-contain">
            <span class="min-w-0"
                  x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                <span class="block font-display text-lg font-bold leading-tight text-brand-700 dark:text-brand-300">FOLEM</span>
                <span class="block text-theme-xs text-gray-400">Back-office CERFI</span>
            </span>
        </a>

        <button type="button"
                @click="$store.sidebar.setMobileOpen(false)"
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 xl:hidden dark:hover:bg-white/5"
                x-show="$store.sidebar.isMobileOpen"
                aria-label="Fermer le menu">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    {{-- Menu --}}
    <div class="flex flex-col overflow-y-auto pb-6 no-scrollbar">
        <nav class="flex flex-col gap-6">
            @foreach ($groupes as $groupe)
                <div>
                    <h2 class="mb-3 flex text-theme-xs uppercase tracking-wide text-gray-400"
                        :class="(! $store.sidebar.isExpanded && ! $store.sidebar.isHovered && ! $store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                        <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            {{ $groupe['titre'] }}
                        </span>
                        <span x-show="! $store.sidebar.isExpanded && ! $store.sidebar.isHovered && ! $store.sidebar.isMobileOpen"
                              aria-hidden="true">&middot;&middot;&middot;</span>
                    </h2>

                    <ul class="flex flex-col gap-1">
                        @foreach ($groupe['items'] as $item)
                            <li>
                                <a href="{{ $item['url'] }}"
                                   @class(['menu-item group', 'menu-item-active' => $item['actif'], 'menu-item-inactive' => ! $item['actif']])
                                   :class="(! $store.sidebar.isExpanded && ! $store.sidebar.isHovered && ! $store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'"
                                   @if ($item['actif']) aria-current="page" @endif>
                                    <span @class([
                                        'shrink-0',
                                        'menu-item-icon-active' => $item['actif'],
                                        'menu-item-icon-inactive' => ! $item['actif'],
                                    ])>
                                        <x-admin.icone :nom="$item['icone']" />
                                    </span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                          class="truncate">{{ $item['libelle'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        {{-- Raccourci vers le site public --}}
        <div class="mt-8" x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
            <div class="rounded-2xl bg-brand-50 p-4 dark:bg-brand-500/10">
                <p class="text-theme-sm font-semibold text-brand-800 dark:text-brand-200">Site public</p>
                <p class="mt-1 text-theme-xs text-brand-700/80 dark:text-brand-200/70">
                    Vérifiez le rendu vu par les participants avant chaque annonce.
                </p>
                <a href="{{ route('accueil') }}" target="_blank" rel="noopener"
                   class="mt-3 inline-flex items-center gap-1.5 text-theme-sm font-semibold text-brand-700 hover:underline dark:text-brand-300">
                    Ouvrir
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</aside>
