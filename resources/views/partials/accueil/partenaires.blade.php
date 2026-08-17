<x-site.section id="partenaires" fond="sombre" surtitre="Sponsoring"
                titre="Devenez partenaire du forum"
                intro="Associer votre structure au FOLEM, c'est toucher plusieurs centaines de décideurs et d'entrepreneurs venus des treize régions du pays, sur trois jours de rencontres.">

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        @foreach (config('folem.packs_partenariat') as $pack)
            <article class="rounded-2xl border border-white/15 bg-white/[0.06] p-6 backdrop-blur">
                <h3 class="font-display text-2xl font-bold text-accent-300">{{ $pack['niveau'] }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-sand-300">{{ $pack['texte'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-10 flex flex-wrap items-center gap-4">
        <x-site.btn :href="'mailto:'.config('folem.contact.email').'?subject='.rawurlencode('Partenariat FOLEM')">
            Demander le dossier de partenariat
        </x-site.btn>
        <p class="text-sm text-sand-400">
            ou appelez le {{ config('folem.contact.telephones')[0] }}
        </p>
    </div>

    @if ($partenaires->isNotEmpty())
        <div class="mt-16 border-t border-white/10 pt-10">
            <p class="text-center text-xs font-semibold uppercase tracking-[0.14em] text-sand-400">
                Ils nous accompagnent
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-8">
                @foreach ($partenaires as $partenaire)
                    @php $logo = Storage::url($partenaire->logo); @endphp

                    @if ($partenaire->lien_site)
                        <a href="{{ $partenaire->lien_site }}" target="_blank" rel="noopener nofollow"
                           class="transition hover:opacity-100" title="{{ $partenaire->nom }}">
                            <img src="{{ $logo }}" alt="{{ $partenaire->nom }}" loading="lazy"
                                 class="h-12 w-auto object-contain opacity-80 transition hover:opacity-100 sm:h-14">
                        </a>
                    @else
                        <img src="{{ $logo }}" alt="{{ $partenaire->nom }}" loading="lazy"
                             class="h-12 w-auto object-contain opacity-80 sm:h-14">
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</x-site.section>
