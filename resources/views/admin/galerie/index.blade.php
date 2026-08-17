<x-admin-layout :title="'Galerie — '.$edition->nom"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Galerie' => null]">

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
            <x-admin.card titre="Ajouter des photos" description="JPG, PNG ou WebP. 5 Mo par fichier, 20 fichiers par envoi.">
                <form method="POST" action="{{ route('admin.editions.galerie.store', $edition) }}" enctype="multipart/form-data">
                    @csrf

                    <x-admin.field label="Fichiers" nom="photos">
                        <input type="file" name="photos[]" id="champ-photos" multiple required
                               accept="image/jpeg,image/png,image/webp"
                               class="w-full rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-6 text-theme-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-theme-xs file:font-medium file:text-brand-700 dark:border-gray-700 dark:bg-white/[0.02] dark:text-gray-300">
                    </x-admin.field>

                    <x-admin.button type="submit" class="mt-4 w-full">Envoyer</x-admin.button>
                </form>
            </x-admin.card>
        </div>

        <div class="xl:col-span-2">
            <x-admin.card :titre="'Photos publiées ('.$photos->count().')'">
                @if ($photos->isEmpty())
                    <x-admin.empty icone="image"
                                   titre="Galerie vide"
                                   texte="Les photos ajoutées ici illustrent la page publique de cette édition." />
                @else
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach ($photos as $photo)
                            <figure class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                                <img src="{{ $photo->getUrl() }}" alt="{{ $photo->name }}" loading="lazy"
                                     class="h-32 w-full object-cover transition group-hover:scale-105">

                                <form method="POST" action="{{ route('admin.editions.galerie.destroy', [$edition, $photo->id]) }}"
                                      class="absolute right-1.5 top-1.5 opacity-0 transition focus-within:opacity-100 group-hover:opacity-100"
                                      x-data
                                      @submit.prevent="if (window.confirm('Supprimer cette photo ?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="flex size-7 items-center justify-center rounded-full bg-error-600 text-white shadow-theme-sm hover:bg-error-700"
                                            aria-label="Supprimer la photo">
                                        <x-admin.icone nom="corbeille" classe="size-3.5" />
                                    </button>
                                </form>
                            </figure>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
