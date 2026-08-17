<x-admin-layout :title="$edition->exists ? 'Modifier l\'édition' : 'Nouvelle édition'"
                :fil="['Éditions' => route('admin.editions.index'), ($edition->exists ? $edition->nom : 'Nouvelle') => null]">

    <form method="POST"
          action="{{ $edition->exists ? route('admin.editions.update', $edition) : route('admin.editions.store') }}"
          class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        @csrf
        @if ($edition->exists) @method('PUT') @endif

        <div class="space-y-6 xl:col-span-2">
            <x-admin.card titre="Identité de l'édition">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <x-admin.field label="Nom" nom="nom" requis class="md:col-span-2">
                        <x-admin.input nom="nom" :value="old('nom', $edition->nom)" placeholder="FOLEM 2026" required />
                    </x-admin.field>

                    <x-admin.field label="Année" nom="annee" requis>
                        <x-admin.input type="number" nom="annee" :value="old('annee', $edition->annee ?? date('Y'))" required />
                    </x-admin.field>
                </div>

                <div class="mt-5">
                    <x-admin.field label="Thème" nom="theme" aide="Phrase affichée en sous-titre du hero sur le site public.">
                        <x-admin.input nom="theme" :value="old('theme', $edition->theme)"
                                       placeholder="Entrepreneuriat éthique et innovant pour un Burkina résilient" />
                    </x-admin.field>
                </div>

                <div class="mt-5">
                    <x-admin.field label="Description" nom="description"
                                   aide="Texte de présentation affiché sur la page de l'édition.">
                        <x-admin.textarea nom="description" rows="5">{{ old('description', $edition->description) }}</x-admin.textarea>
                    </x-admin.field>
                </div>
            </x-admin.card>

            <x-admin.card titre="Dates et lieu">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <x-admin.field label="Date de début" nom="date_debut" requis>
                        <x-admin.input type="date" nom="date_debut"
                                       :value="old('date_debut', $edition->date_debut?->format('Y-m-d'))" required />
                    </x-admin.field>

                    <x-admin.field label="Date de fin" nom="date_fin" requis>
                        <x-admin.input type="date" nom="date_fin"
                                       :value="old('date_fin', $edition->date_fin?->format('Y-m-d'))" required />
                    </x-admin.field>
                </div>

                <div class="mt-5">
                    <x-admin.field label="Lieu" nom="lieu">
                        <x-admin.input nom="lieu" :value="old('lieu', $edition->lieu)" placeholder="Université Joseph Ki-Zerbo, Ouagadougou" />
                    </x-admin.field>
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card titre="Publication">
                <x-admin.field label="Statut" nom="statut" requis
                               aide="Passer une édition en « active » archive automatiquement l'édition active précédente.">
                    <x-admin.select nom="statut">
                        @foreach (['draft' => 'Brouillon (invisible du public)', 'active' => 'Active (inscriptions ouvertes)', 'archived' => 'Archivée'] as $valeur => $libelle)
                            <option value="{{ $valeur }}" @selected(old('statut', $edition->statut ?? 'draft') === $valeur)>{{ $libelle }}</option>
                        @endforeach
                    </x-admin.select>
                </x-admin.field>

                <div class="mt-5">
                    <x-admin.field label="Tarif de repli (FCFA)" nom="montant_inscription"
                                   aide="Utilisé uniquement si aucune grille région × profil n'est renseignée.">
                        <x-admin.input type="number" min="0" step="500" nom="montant_inscription"
                                       :value="old('montant_inscription', $edition->montant_inscription)" />
                    </x-admin.field>
                </div>
            </x-admin.card>

            @if ($edition->exists)
                <x-admin.card titre="Contenus liés">
                    <div class="flex flex-col gap-2 text-theme-sm">
                        <a href="{{ route('admin.editions.prix.index', $edition) }}" class="text-brand-700 hover:underline dark:text-brand-300">Grille tarifaire</a>
                        <a href="{{ route('admin.editions.programmes.index', $edition) }}" class="text-brand-700 hover:underline dark:text-brand-300">Programme</a>
                        <a href="{{ route('admin.editions.intervenants.index', $edition) }}" class="text-brand-700 hover:underline dark:text-brand-300">Intervenants</a>
                        <a href="{{ route('admin.editions.partenaires.index', $edition) }}" class="text-brand-700 hover:underline dark:text-brand-300">Partenaires</a>
                        <a href="{{ route('admin.editions.galerie.index', $edition) }}" class="text-brand-700 hover:underline dark:text-brand-300">Galerie photo</a>
                    </div>
                </x-admin.card>
            @endif

            <div class="flex flex-wrap gap-3">
                <x-admin.button type="submit">{{ $edition->exists ? 'Enregistrer' : "Créer l'édition" }}</x-admin.button>
                <x-admin.button variante="secondaire" :href="route('admin.editions.index')">Annuler</x-admin.button>
            </div>
        </div>
    </form>
</x-admin-layout>
