@php
    $ouverte = $edition?->estOuverte();
    $tarifMini = $edition?->tarifMinimum();
    $joursRestants = $edition?->date_debut ? now()->startOfDay()->diffInDays($edition->date_debut, false) : null;
@endphp

<section class="relative overflow-hidden bg-brand-950 text-sand-50">
    <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800" aria-hidden="true"></div>
    <div class="absolute inset-0 motif-mashrabiya opacity-70" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28">
        <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-12">

            <div class="lg:col-span-7">
                @if ($edition)
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-accent-300">
                        CERFI &middot; {{ $edition->lieu ?: 'Ouagadougou' }}
                    </p>

                    <h1 class="mt-4 font-display text-4xl font-bold leading-[1.08] sm:text-5xl lg:text-6xl">
                        {{ $edition->nom }}
                    </h1>

                    @if ($edition->theme)
                        <p class="mt-5 max-w-xl font-display text-xl italic leading-relaxed text-sand-200 sm:text-2xl">
                            « {{ $edition->theme }} »
                        </p>
                    @endif

                    <p class="mt-7 max-w-xl text-base leading-relaxed text-sand-300">
                        Trois jours de rencontres, de formation et d'affaires pour les entrepreneurs et cadres
                        musulmans du Burkina Faso, dans un cadre pensé pour faire naître des projets concrets.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-3">
                        @if ($ouverte)
                            <x-site.btn :href="route('inscription.form', $edition)" taille="grand">
                                Je m'inscris
                                @if ($tarifMini)
                                    <span class="font-normal opacity-90">&middot; dès {{ number_format($tarifMini, 0, ',', ' ') }} F</span>
                                @endif
                            </x-site.btn>
                        @endif

                        <x-site.btn href="#programme" variante="contour-clair" taille="grand">
                            Voir le programme
                        </x-site.btn>
                    </div>

                    @if (! $ouverte)
                        <p class="mt-5 inline-flex rounded-lg border border-white/20 px-4 py-2.5 text-sm text-sand-200">
                            Les inscriptions en ligne sont actuellement fermées pour cette édition.
                        </p>
                    @endif
                @else
                    <h1 class="font-display text-4xl font-bold leading-tight sm:text-5xl">
                        Forum du Leadership et de l'Entrepreneuriat Musulmans
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-relaxed text-sand-300">
                        La prochaine édition est en préparation. Revenez bientôt pour découvrir le thème,
                        le programme et les modalités d'inscription.
                    </p>
                @endif
            </div>

            {{-- Carte d'informations clés --}}
            @if ($edition)
                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-white/15 bg-white/[0.06] p-6 backdrop-blur sm:p-8">
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-accent-300">Dates</dt>
                                <dd class="mt-1.5 text-lg font-semibold">
                                    {{ $edition->date_debut?->translatedFormat('j') }}
                                    @if ($edition->date_debut?->format('m') !== $edition->date_fin?->format('m'))
                                        {{ $edition->date_debut?->translatedFormat('F') }}
                                    @endif
                                    au {{ $edition->date_fin?->translatedFormat('j F Y') }}
                                </dd>
                                @if ($joursRestants !== null && $joursRestants > 0)
                                    <dd class="mt-1 text-sm text-sand-300">Dans {{ (int) $joursRestants }} jours</dd>
                                @endif
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-accent-300">Lieu</dt>
                                <dd class="mt-1.5 text-lg font-semibold">{{ $edition->lieu ?: 'Ouagadougou, Burkina Faso' }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-accent-300">Participation</dt>
                                <dd class="mt-1.5 text-lg font-semibold">
                                    @if ($tarifMini)
                                        À partir de {{ number_format($tarifMini, 0, ',', ' ') }} FCFA
                                    @else
                                        Tarifs communiqués prochainement
                                    @endif
                                </dd>
                                <dd class="mt-1 text-sm text-sand-300">Paiement par Orange Money ou Moov Money</dd>
                            </div>
                        </dl>

                        <div class="mt-7 border-t border-white/15 pt-5">
                            <p class="text-sm text-sand-300">Déjà inscrit ?</p>
                            <a href="#retrouver-dossier" class="mt-1 inline-flex items-center gap-1.5 text-sm font-semibold text-accent-300 hover:text-accent-200">
                                Payer ou télécharger votre récépissé
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 12h14m0 0-6-6m6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
