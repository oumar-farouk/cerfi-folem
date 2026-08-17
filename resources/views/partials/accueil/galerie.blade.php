<x-site.section id="galerie" fond="blanc" surtitre="En images" titre="Retour sur les éditions précédentes"
                intro="Quelques instants des forums passés, entre panels, ateliers et rencontres d'affaires.">

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($photos as $photo)
            <figure class="group overflow-hidden rounded-xl bg-sand-100">
                <img src="{{ $photo->getUrl() }}" alt="{{ $photo->name }}" loading="lazy"
                     class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
            </figure>
        @endforeach
    </div>
</x-site.section>
