<x-site.section id="intervenants" fond="creme" surtitre="Ils prendront la parole" titre="Intervenants">

    @if ($intervenants->isEmpty())
        <div class="rounded-2xl border border-dashed border-sand-300 bg-white/60 px-6 py-14 text-center">
            <p class="font-display text-xl font-bold text-brand-900">Panel en cours de constitution</p>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-sand-600">
                Les intervenants confirmés seront présentés ici au fur et à mesure des accords.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($intervenants as $intervenant)
                <article class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-sand-200 transition hover:shadow-lg">
                    <div class="aspect-4/3 overflow-hidden bg-sand-100">
                        @if ($intervenant->photo)
                            <img src="{{ Storage::url($intervenant->photo) }}" alt="{{ $intervenant->nom }}" loading="lazy"
                                 class="size-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex size-full items-center justify-center bg-brand-800 motif-mashrabiya">
                                <span class="font-display text-4xl font-bold text-white/90">
                                    {{ \Illuminate\Support\Str::of($intervenant->nom)->substr(0, 2)->upper() }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="font-display text-lg font-bold text-brand-900">{{ $intervenant->nom }}</h3>
                        <p class="mt-1 text-sm text-accent-600">{{ $intervenant->fonction }}</p>
                        @if ($intervenant->structure)
                            <p class="text-sm text-sand-500">{{ $intervenant->structure }}</p>
                        @endif

                        @if ($intervenant->bio)
                            <p class="mt-3 line-clamp-4 text-sm leading-relaxed text-sand-600">{{ $intervenant->bio }}</p>
                        @endif

                        @if ($intervenant->linkedin_url)
                            <a href="{{ $intervenant->linkedin_url }}" target="_blank" rel="noopener nofollow"
                               class="mt-auto inline-flex items-center gap-1.5 pt-4 text-sm font-semibold text-brand-700 hover:underline">
                                Profil LinkedIn
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-site.section>
