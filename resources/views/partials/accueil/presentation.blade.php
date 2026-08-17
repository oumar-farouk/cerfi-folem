<x-site.section id="le-forum" fond="clair" surtitre="À propos"
                titre="Le principal cadre national dédié à l'entrepreneuriat musulman">

    <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
        <div>
            <h3 class="font-display text-2xl font-bold text-brand-900">Le CERFI</h3>
            <p class="mt-4 leading-relaxed text-sand-700">{{ config('folem.presentation.cerfi') }}</p>
            <a href="{{ config('folem.organisation.site') }}" target="_blank" rel="noopener"
               class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:underline">
                Découvrir le CERFI
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <div>
            <h3 class="font-display text-2xl font-bold text-brand-900">Le FOLEM</h3>
            <p class="mt-4 leading-relaxed text-sand-700">{{ config('folem.presentation.folem') }}</p>

            @if ($archives->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($archives as $archive)
                        <a href="{{ route('editions.show', $archive) }}"
                           class="rounded-lg border border-sand-300 px-3 py-1.5 text-sm font-medium text-sand-700 transition hover:border-brand-500 hover:text-brand-700">
                            {{ $archive->nom }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Valeurs --}}
    <div class="mt-14 grid grid-cols-1 gap-5 sm:grid-cols-3">
        @foreach (config('folem.valeurs') as $valeur)
            <article class="rounded-2xl border border-sand-200 bg-white p-6 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                <h3 class="font-display text-2xl font-bold text-brand-800">{{ $valeur['titre'] }}</h3>
                <p class="mt-0.5 text-xs font-semibold uppercase tracking-[0.12em] text-accent-500">{{ $valeur['sous_titre'] }}</p>
                <p class="mt-4 text-sm leading-relaxed text-sand-600">{{ $valeur['texte'] }}</p>
            </article>
        @endforeach
    </div>
</x-site.section>
