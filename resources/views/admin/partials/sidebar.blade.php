@php
    /*
    | Navigation du back-office, filtrée par profil.
    |
    | Chaque entrée déclare les rôles autorisés. Le super-admin voit tout.
    | Une entrée est masquée si :
    |   - le rôle de l'utilisateur ne l'autorise pas ;
    |   - sa route n'existe pas encore (Route::has) ;
    |   - elle dépend d'une édition et aucune n'existe.
    | Un groupe sans entrée visible disparaît entièrement.
    |
    | Les entrées liées à une édition utilisent l'édition active, et à défaut
    | la plus récente, pour éviter des liens morts.
    */
    $user = auth()->user();

    $equipePilotage = ['super-admin', 'gestionnaire', 'moderateur', 'comptable'];

    $aRole = fn (array $roles) => $user && $user->hasAnyRole(array_merge(['super-admin'], $roles));

    $editionCourante = \App\Models\Edition::active()->first()
        ?? \App\Models\Edition::orderByDesc('annee')->first();

    // Page d'atterrissage selon le profil (l'agent d'accueil n'a pas de tableau de bord).
    $routeAccueilAdmin = $user && $user->hasAnyRole($equipePilotage) && Route::has('admin.dashboard')
        ? 'admin.dashboard'
        : (Route::has('admin.pointage.index') ? 'admin.pointage.index' : 'accueil');

    $libellesRoles = [
        'super-admin'   => 'Super administrateur',
        'gestionnaire'  => 'Gestionnaire',
        'moderateur'    => 'Modérateur',
        'comptable'     => 'Comptable',
        'agent-accueil' => "Agent d'accueil",
    ];
    $libelleRole = collect($libellesRoles)->first(fn ($l, $role) => $user?->hasRole($role));

    $definition = [
        [
            'titre' => 'Pilotage',
            'items' => [
                [
                    'libelle' => 'Tableau de bord',
                    'route'   => 'admin.dashboard',
                    'match'   => ['admin.dashboard'],
                    'roles'   => $equipePilotage,
                    'icone'   => 'grille',
                ],
            ],
        ],
        [
            'titre' => 'Participants',
            'items' => [
                [
                    'libelle' => 'Inscriptions',
                    'route'   => 'admin.inscriptions.index',
                    'match'   => ['admin.inscriptions.*'],
                    'roles'   => ['gestionnaire', 'moderateur', 'comptable'],
                    'icone'   => 'liste',
                ],
                [
                    'libelle' => 'Pointage jour J',
                    'route'   => 'admin.pointage.index',
                    'match'   => ['admin.pointage.*'],
                    'roles'   => ['gestionnaire', 'moderateur', 'agent-accueil'],
                    'icone'   => 'badge',
                ],
            ],
        ],
        [
            'titre' => 'Éditions',
            'items' => [
                [
                    'libelle' => 'Toutes les éditions',
                    'route'   => 'admin.editions.index',
                    'match'   => ['admin.editions.index', 'admin.editions.create', 'admin.editions.edit'],
                    'roles'   => ['gestionnaire'],
                    'icone'   => 'calendrier',
                ],
                [
                    'libelle'  => 'Programme',
                    'route'    => 'admin.editions.programmes.index',
                    'match'    => ['admin.editions.programmes.*', 'admin.programmes.*'],
                    'roles'    => ['gestionnaire'],
                    'icone'    => 'horloge',
                    'edition'  => true,
                ],
                [
                    'libelle'  => 'Intervenants',
                    'route'    => 'admin.editions.intervenants.index',
                    'match'    => ['admin.editions.intervenants.*', 'admin.intervenants.*'],
                    'roles'    => ['gestionnaire'],
                    'icone'    => 'utilisateurs',
                    'edition'  => true,
                ],
                [
                    'libelle'  => 'Partenaires',
                    'route'    => 'admin.editions.partenaires.index',
                    'match'    => ['admin.editions.partenaires.*', 'admin.partenaires.*'],
                    'roles'    => ['gestionnaire'],
                    'icone'    => 'poignee',
                    'edition'  => true,
                ],
                [
                    'libelle'  => 'Galerie',
                    'route'    => 'admin.editions.galerie.index',
                    'match'    => ['admin.editions.galerie.*'],
                    'roles'    => ['gestionnaire'],
                    'icone'    => 'image',
                    'edition'  => true,
                ],
            ],
        ],
        [
            'titre' => 'Finance',
            'items' => [
                [
                    'libelle' => 'Paiements',
                    'route'   => 'admin.paiements.index',
                    'match'   => ['admin.paiements.*'],
                    'roles'   => ['gestionnaire', 'comptable'],
                    'icone'   => 'monnaie',
                ],
                [
                    'libelle' => 'Encaisser un paiement',
                    'route'   => 'admin.paiements.encaisser',
                    'match'   => ['admin.paiements.encaisser', 'admin.paiements.manuel.*'],
                    'roles'   => ['gestionnaire', 'comptable'],
                    'icone'   => 'monnaie',
                ],
                [
                    'libelle'  => 'Grille tarifaire',
                    'route'    => 'admin.editions.prix.index',
                    'match'    => ['admin.editions.prix.*'],
                    'roles'    => ['gestionnaire', 'comptable'],
                    'icone'    => 'monnaie',
                    'edition'  => true,
                ],
            ],
        ],
        [
            'titre' => 'Paramétrage',
            'items' => [
                [
                    'libelle' => 'Régions',
                    'route'   => 'admin.regions.index',
                    'match'   => ['admin.regions.*'],
                    'roles'   => [],
                    'icone'   => 'carte',
                ],
                [
                    'libelle' => 'Profils',
                    'route'   => 'admin.profils.index',
                    'match'   => ['admin.profils.*'],
                    'roles'   => [],
                    'icone'   => 'badge',
                ],
            ],
        ],
        [
            'titre' => 'Administration',
            'items' => [
                [
                    'libelle' => 'Utilisateurs',
                    'route'   => 'admin.users.index',
                    'match'   => ['admin.users.*'],
                    'roles'   => [], // super-admin uniquement
                    'icone'   => 'badge',
                ],
                [
                    'libelle' => "Journal d'activité",
                    'route'   => 'admin.journal.index',
                    'match'   => ['admin.journal.*'],
                    'roles'   => [], // super-admin uniquement
                    'icone'   => 'liste',
                ],
            ],
        ],
    ];

    $groupes = collect($definition)
        ->map(function ($groupe) use ($aRole, $editionCourante) {
            $items = collect($groupe['items'])
                ->filter(fn ($i) => $aRole($i['roles'])
                    && Route::has($i['route'])
                    && (empty($i['edition']) || $editionCourante))
                ->map(fn ($i) => $i + [
                    'url'   => route($i['route'], ! empty($i['edition']) ? [$editionCourante] : []),
                    'actif' => request()->routeIs(...$i['match']),
                ])
                ->values();

            return ['titre' => $groupe['titre'], 'items' => $items];
        })
        ->filter(fn ($g) => $g['items']->isNotEmpty())
        ->values();
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
        <a href="{{ route($routeAccueilAdmin) }}" class="flex items-center gap-3">
            <img src="{{ asset('img/brand/folem-icone.png') }}" alt="" class="h-9 w-9 shrink-0 object-contain">
            <span class="min-w-0"
                  x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                <span class="block font-display text-lg font-bold leading-tight text-brand-700 dark:text-brand-300">FOLEM</span>
                <span class="block text-theme-xs text-gray-400">{{ $libelleRole ?? 'Back-office CERFI' }}</span>
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

        {{-- Raccourci vers le site public : utile à l'équipe de pilotage, pas à l'agent d'accueil --}}
        @if ($aRole($equipePilotage))
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
        @endif
    </div>
</aside>
