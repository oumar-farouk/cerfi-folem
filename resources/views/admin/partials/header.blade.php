@php
    $utilisateur = auth()->user();
    $initiales = collect(explode(' ', (string) $utilisateur?->name))
        ->filter()
        ->take(2)
        ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
        ->implode('');
@endphp

<header class="sticky top-0 z-999 flex w-full border-b border-gray-200 bg-white/95 backdrop-blur dark:border-gray-800 dark:bg-gray-900/95">
    <div class="flex w-full items-center justify-between gap-3 px-4 py-3 md:px-6">

        <div class="flex min-w-0 items-center gap-3">
            {{-- Burger mobile / réduction desktop --}}
            <button type="button"
                    @click="window.innerWidth < 1280 ? $store.sidebar.toggleMobileOpen() : $store.sidebar.toggleExpanded()"
                    class="rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/5"
                    aria-label="Afficher ou masquer le menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="min-w-0">
                <p class="truncate text-theme-xs uppercase tracking-wide text-gray-400">Administration</p>
                <p class="truncate font-semibold text-gray-800 dark:text-white/90">{{ $titrePage ?? 'Tableau de bord' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Bascule clair / sombre --}}
            <button type="button"
                    @click="$store.theme.toggle()"
                    class="rounded-full border border-gray-200 p-2.5 text-gray-500 transition hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/5"
                    :aria-label="$store.theme.theme === 'dark' ? 'Passer en thème clair' : 'Passer en thème sombre'">
                <svg class="size-5" x-show="$store.theme.theme === 'light'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="size-5" x-show="$store.theme.theme === 'dark'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" style="display: none">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- Menu utilisateur --}}
            <div class="relative" x-data="{ ouvert: false }" @keydown.escape.window="ouvert = false">
                <button type="button"
                        @click="ouvert = ! ouvert"
                        class="flex items-center gap-2 rounded-full border border-gray-200 py-1.5 pl-1.5 pr-3 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5"
                        :aria-expanded="ouvert"
                        aria-haspopup="menu">
                    <span class="flex size-8 items-center justify-center rounded-full bg-brand-500 text-theme-xs font-bold text-white">
                        {{ $initiales ?: 'FL' }}
                    </span>
                    <span class="hidden max-w-[10rem] truncate text-theme-sm font-medium text-gray-700 sm:block dark:text-gray-300">
                        {{ $utilisateur?->name }}
                    </span>
                    <svg class="size-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div x-show="ouvert"
                     @click.outside="ouvert = false"
                     x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-60 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-800"
                     role="menu"
                     style="display: none">
                    <div class="border-b border-gray-100 px-3 pb-3 pt-2 dark:border-gray-700">
                        <p class="truncate text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ $utilisateur?->name }}</p>
                        <p class="truncate text-theme-xs text-gray-500">{{ $utilisateur?->email }}</p>
                        @if ($utilisateur && $utilisateur->getRoleNames()->isNotEmpty())
                            <p class="mt-2 inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                                {{ $utilisateur->getRoleNames()->implode(', ') }}
                            </p>
                        @endif
                    </div>

                    <a href="{{ route('profile') }}" role="menuitem"
                       class="mt-1 flex items-center gap-2 rounded-lg px-3 py-2 text-theme-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">
                        Mon compte
                    </a>

                    <a href="{{ route('accueil') }}" target="_blank" rel="noopener" role="menuitem"
                       class="flex items-center gap-2 rounded-lg px-3 py-2 text-theme-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">
                        Voir le site public
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-gray-100 pt-1 dark:border-gray-700">
                        @csrf
                        <button type="submit" role="menuitem"
                                class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-theme-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10">
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
