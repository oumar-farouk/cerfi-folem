<x-admin-layout :title="$partenaire->exists ? 'Modifier le partenaire' : 'Nouveau partenaire'"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Partenaires' => route('admin.editions.partenaires.index', $edition), ($partenaire->exists ? 'Modifier' : 'Nouveau') => null]">

    <form method="POST"
          action="{{ $partenaire->exists ? route('admin.partenaires.update', $partenaire) : route('admin.editions.partenaires.store', $edition) }}"
          enctype="multipart/form-data"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($partenaire->exists) @method('PUT') @endif

        <x-admin.card titre="Informations">
            <x-admin.field label="Nom de la structure" nom="nom" requis>
                <x-admin.input nom="nom" :value="old('nom', $partenaire->nom)" required />
            </x-admin.field>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-admin.field label="Type" nom="type" requis>
                    <x-admin.select nom="type">
                        <option value="partenaire" @selected(old('type', $partenaire->type) === 'partenaire')>Partenaire</option>
                        <option value="sponsor" @selected(old('type', $partenaire->type) === 'sponsor')>Sponsor</option>
                    </x-admin.select>
                </x-admin.field>

                <x-admin.field label="Niveau" nom="niveau" aide="Platine, Or, Argent…">
                    <x-admin.input nom="niveau" :value="old('niveau', $partenaire->niveau)" />
                </x-admin.field>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-admin.field label="Site web" nom="lien_site">
                    <x-admin.input type="url" nom="lien_site" :value="old('lien_site', $partenaire->lien_site)" placeholder="https://" />
                </x-admin.field>

                <x-admin.field label="Ordre d'affichage" nom="ordre" aide="Le plus petit nombre apparaît en premier.">
                    <x-admin.input type="number" nom="ordre" :value="old('ordre', $partenaire->ordre ?? 0)" />
                </x-admin.field>
            </div>
        </x-admin.card>

        <x-admin.card titre="Logo" description="PNG à fond transparent de préférence, 2 Mo maximum.">
            @if ($partenaire->logo)
                <img src="{{ Storage::url($partenaire->logo) }}" alt="Logo actuel" class="mb-4 h-14 object-contain">
            @endif

            <x-admin.field :label="$partenaire->exists ? 'Remplacer le logo' : 'Logo'" nom="logo" :requis="! $partenaire->exists">
                <input type="file" name="logo" id="champ-logo" accept="image/jpeg,image/png,image/webp,image/svg+xml"
                       @required(! $partenaire->exists)
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-theme-xs file:font-medium file:text-brand-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </x-admin.field>
        </x-admin.card>

        <div class="flex flex-wrap gap-3">
            <x-admin.button type="submit">Enregistrer</x-admin.button>
            <x-admin.button variante="secondaire" :href="route('admin.editions.partenaires.index', $edition)">Annuler</x-admin.button>
        </div>
    </form>
</x-admin-layout>
