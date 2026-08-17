<x-admin-layout :title="$programme->exists ? 'Modifier la session' : 'Nouvelle session'"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Programme' => route('admin.editions.programmes.index', $edition), ($programme->exists ? 'Modifier' : 'Nouvelle') => null]">

    <form method="POST"
          action="{{ $programme->exists ? route('admin.programmes.update', $programme) : route('admin.editions.programmes.store', $edition) }}"
          class="max-w-3xl space-y-6">
        @csrf
        @if ($programme->exists) @method('PUT') @endif

        <x-admin.card titre="Contenu de la session">
            <x-admin.field label="Titre" nom="titre" requis>
                <x-admin.input nom="titre" :value="old('titre', $programme->titre)" required
                               placeholder="Panel 1 : financer sa croissance sans intérêt" />
            </x-admin.field>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-admin.field label="Type" nom="type" aide="Panel, atelier, plénière, cérémonie, pause…">
                    <x-admin.input nom="type" :value="old('type', $programme->type)" />
                </x-admin.field>

                <x-admin.field label="Salle" nom="salle">
                    <x-admin.input nom="salle" :value="old('salle', $programme->salle)" />
                </x-admin.field>
            </div>

            <div class="mt-5">
                <x-admin.field label="Description" nom="description">
                    <x-admin.textarea nom="description" rows="4">{{ old('description', $programme->description) }}</x-admin.textarea>
                </x-admin.field>
            </div>
        </x-admin.card>

        <x-admin.card titre="Créneau">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <x-admin.field label="Date" nom="date" requis>
                    <x-admin.input type="date" nom="date" required
                                   min="{{ $edition->date_debut?->format('Y-m-d') }}"
                                   max="{{ $edition->date_fin?->format('Y-m-d') }}"
                                   :value="old('date', $programme->date?->format('Y-m-d'))" />
                </x-admin.field>

                <x-admin.field label="Heure de début" nom="heure_debut" requis>
                    <x-admin.input type="time" nom="heure_debut" required
                                   :value="old('heure_debut', \Illuminate\Support\Str::of($programme->heure_debut)->substr(0, 5))" />
                </x-admin.field>

                <x-admin.field label="Heure de fin" nom="heure_fin">
                    <x-admin.input type="time" nom="heure_fin"
                                   :value="old('heure_fin', \Illuminate\Support\Str::of($programme->heure_fin)->substr(0, 5))" />
                </x-admin.field>
            </div>
        </x-admin.card>

        <x-admin.card titre="Intervenants"
                      description="Sélection multiple : maintenez Ctrl (ou Cmd sur Mac) pour choisir plusieurs personnes.">
            @if ($intervenants->isEmpty())
                <x-admin.empty icone="utilisateurs"
                               titre="Aucun intervenant enregistré"
                               texte="Ajoutez d'abord des intervenants à cette édition pour pouvoir les rattacher aux sessions.">
                    <x-slot:action>
                        <x-admin.button variante="secondaire" :href="route('admin.editions.intervenants.create', $edition)">
                            Ajouter un intervenant
                        </x-admin.button>
                    </x-slot:action>
                </x-admin.empty>
            @else
                <x-admin.field label="Rattacher des intervenants" nom="intervenants">
                    <select name="intervenants[]" id="champ-intervenants" multiple size="6"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @php $selection = collect(old('intervenants', $programme->intervenants->pluck('id')->all())); @endphp
                        @foreach ($intervenants as $intervenant)
                            <option value="{{ $intervenant->id }}" @selected($selection->contains($intervenant->id))>
                                {{ $intervenant->nom }}@if ($intervenant->structure) — {{ $intervenant->structure }}@endif
                            </option>
                        @endforeach
                    </select>
                </x-admin.field>
            @endif
        </x-admin.card>

        <div class="flex flex-wrap gap-3">
            <x-admin.button type="submit">Enregistrer</x-admin.button>
            <x-admin.button variante="secondaire" :href="route('admin.editions.programmes.index', $edition)">Annuler</x-admin.button>
        </div>
    </form>
</x-admin-layout>
