<x-admin-layout :title="$intervenant->exists ? 'Modifier l\'intervenant' : 'Nouvel intervenant'"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Intervenants' => route('admin.editions.intervenants.index', $edition), ($intervenant->exists ? 'Modifier' : 'Nouveau') => null]">

    <form method="POST"
          action="{{ $intervenant->exists ? route('admin.intervenants.update', $intervenant) : route('admin.editions.intervenants.store', $edition) }}"
          enctype="multipart/form-data"
          class="max-w-3xl space-y-6">
        @csrf
        @if ($intervenant->exists) @method('PUT') @endif

        <x-admin.card titre="Identité">
            <x-admin.field label="Nom complet" nom="nom" requis>
                <x-admin.input nom="nom" :value="old('nom', $intervenant->nom)" required />
            </x-admin.field>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-admin.field label="Fonction" nom="fonction">
                    <x-admin.input nom="fonction" :value="old('fonction', $intervenant->fonction)" placeholder="Directeur général" />
                </x-admin.field>

                <x-admin.field label="Structure" nom="structure">
                    <x-admin.input nom="structure" :value="old('structure', $intervenant->structure)" />
                </x-admin.field>
            </div>

            <div class="mt-5">
                <x-admin.field label="Biographie" nom="bio" aide="Quelques lignes affichées sur la fiche publique.">
                    <x-admin.textarea nom="bio" rows="5">{{ old('bio', $intervenant->bio) }}</x-admin.textarea>
                </x-admin.field>
            </div>

            <div class="mt-5">
                <x-admin.field label="Profil LinkedIn" nom="linkedin_url">
                    <x-admin.input type="url" nom="linkedin_url" :value="old('linkedin_url', $intervenant->linkedin_url)"
                                   placeholder="https://www.linkedin.com/in/…" />
                </x-admin.field>
            </div>
        </x-admin.card>

        <x-admin.card titre="Photo" description="Format carré conseillé, 2 Mo maximum, JPG ou PNG.">
            @if ($intervenant->photo)
                <img src="{{ Storage::url($intervenant->photo) }}" alt="Photo actuelle"
                     class="mb-4 size-20 rounded-full object-cover">
            @endif

            <x-admin.field label="Nouvelle photo" nom="photo">
                <input type="file" name="photo" id="champ-photo" accept="image/jpeg,image/png,image/webp"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-theme-xs file:font-medium file:text-brand-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </x-admin.field>
        </x-admin.card>

        <div class="flex flex-wrap gap-3">
            <x-admin.button type="submit">Enregistrer</x-admin.button>
            <x-admin.button variante="secondaire" :href="route('admin.editions.intervenants.index', $edition)">Annuler</x-admin.button>
        </div>
    </form>
</x-admin-layout>
