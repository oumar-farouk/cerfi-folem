<x-public-layout :title="$edition->nom" :ancres="false" :description="$edition->theme">

    <section class="relative overflow-hidden bg-brand-950 text-sand-50">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800" aria-hidden="true"></div>
        <div class="absolute inset-0 motif-mashrabiya opacity-70" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
            <a href="{{ route('accueil') }}" class="inline-flex items-center gap-1.5 text-sm text-sand-300 hover:text-white">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M19 12H5m0 0 6-6m-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Retour à l'accueil
            </a>

            <p class="mt-8 text-xs font-semibold uppercase tracking-[0.16em] text-accent-300">
                {{ $edition->statut === 'archived' ? 'Édition passée' : 'Édition' }}
            </p>
            <h1 class="mt-3 font-display text-4xl font-bold leading-tight sm:text-5xl">{{ $edition->nom }}</h1>

            @if ($edition->theme)
                <p class="mt-4 max-w-2xl font-display text-xl italic text-sand-200">« {{ $edition->theme }} »</p>
            @endif

            <p class="mt-6 text-sm text-sand-300">
                {{ $edition->date_debut?->translatedFormat('j F Y') }} au {{ $edition->date_fin?->translatedFormat('j F Y') }}
                @if ($edition->lieu) &middot; {{ $edition->lieu }} @endif
            </p>

            @if ($edition->estOuverte())
                <div class="mt-8">
                    <x-site.btn :href="route('inscription.form', $edition)" taille="grand">Je m'inscris</x-site.btn>
                </div>
            @endif
        </div>
    </section>

    @if ($edition->description)
        <x-site.section fond="clair" surtitre="Présentation" titre="À propos de cette édition">
            <div class="max-w-3xl space-y-4 leading-relaxed text-sand-700">
                @foreach (preg_split('/\R{2,}/', trim($edition->description)) as $paragraphe)
                    <p>{{ $paragraphe }}</p>
                @endforeach
            </div>
        </x-site.section>
    @endif

    @include('partials.accueil.programme', ['programmes' => $edition->programmes()->with('intervenants')->get()])

    @include('partials.accueil.intervenants', ['intervenants' => $edition->intervenants])

    @php $photos = $edition->getMedia('galerie'); @endphp
    @if ($photos->isNotEmpty())
        @include('partials.accueil.galerie', ['photos' => $photos])
    @endif

    @if ($edition->partenaires->isNotEmpty())
        <x-site.section fond="sombre" surtitre="Merci à eux" titre="Partenaires de l'édition" centre>
            <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-8">
                @foreach ($edition->partenaires as $partenaire)
                    <img src="{{ Storage::url($partenaire->logo) }}" alt="{{ $partenaire->nom }}" loading="lazy"
                         class="h-12 w-auto object-contain opacity-85 sm:h-14">
                @endforeach
            </div>
        </x-site.section>
    @endif
</x-public-layout>
