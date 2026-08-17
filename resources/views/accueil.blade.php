<x-public-layout :edition="$editionActive"
                 :description="$editionActive?->theme
                    ? $editionActive->nom.' — '.$editionActive->theme
                    : null">

    @include('partials.accueil.hero', ['edition' => $editionActive])

    @include('partials.accueil.presentation', ['archives' => $archives])

    @include('partials.accueil.thematiques')

    @include('partials.accueil.programme', ['programmes' => $programmes])

    @include('partials.accueil.intervenants', ['intervenants' => $intervenants])

    @if ($photos->isNotEmpty())
        @include('partials.accueil.galerie', ['photos' => $photos])
    @endif

    @include('partials.accueil.partenaires', ['partenaires' => $partenaires])

    @include('partials.accueil.inscription', ['edition' => $editionActive])

    @include('partials.accueil.retrouver-dossier')

    @include('partials.accueil.infos', ['edition' => $editionActive])
</x-public-layout>
