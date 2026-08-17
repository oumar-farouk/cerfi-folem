<x-site.section id="thematiques" fond="creme" surtitre="Contenus" titre="Les grandes thématiques"
                intro="Le forum articule ses panels et ateliers autour de trois axes qui reviennent, année après année, dans les besoins exprimés par les entrepreneurs.">

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        @foreach (config('folem.thematiques') as $index => $thematique)
            <article class="group relative overflow-hidden rounded-2xl bg-white p-7 shadow-sm ring-1 ring-sand-200 transition hover:-translate-y-1 hover:shadow-lg">
                <span class="font-display text-5xl font-bold text-sand-200 transition group-hover:text-accent-200">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </span>
                <h3 class="mt-3 font-display text-xl font-bold text-brand-900">{{ $thematique['titre'] }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-sand-600">{{ $thematique['texte'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-12">
        <h3 class="font-display text-xl font-bold text-brand-900">À qui s'adresse le forum</h3>
        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (config('folem.secteurs') as $secteur => $description)
                <div class="flex gap-3 rounded-xl border border-sand-200 bg-white/70 p-4">
                    <span class="mt-1 size-2 shrink-0 rounded-full bg-accent-500" aria-hidden="true"></span>
                    <div>
                        <p class="text-sm font-semibold text-sand-800">{{ $secteur }}</p>
                        <p class="mt-0.5 text-xs text-sand-500">{{ $description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-site.section>
