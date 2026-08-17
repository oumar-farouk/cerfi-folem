<x-admin-layout title="Régions" :fil="['Paramétrage' => null, 'Régions' => null]">

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
            <x-admin.card titre="Ajouter une région"
                          description="Les régions servent d'axe à la grille tarifaire et au formulaire d'inscription.">
                <form method="POST" action="{{ route('admin.regions.store') }}" class="space-y-5">
                    @csrf

                    <x-admin.field label="Nom" nom="nom" requis>
                        <x-admin.input nom="nom" :value="old('nom')" required placeholder="Centre" />
                    </x-admin.field>

                    <x-admin.field label="Ordre d'affichage" nom="ordre">
                        <x-admin.input type="number" nom="ordre" :value="old('ordre', 0)" />
                    </x-admin.field>

                    <x-admin.button type="submit" class="w-full" icone="plus">Ajouter</x-admin.button>
                </form>
            </x-admin.card>
        </div>

        <div class="xl:col-span-2">
            <x-admin.card :padding="false" :titre="'Régions enregistrées ('.$regions->count().')'">
                @if ($regions->isEmpty())
                    <x-admin.empty icone="carte" titre="Aucune région" texte="Ajoutez au moins une région pour pouvoir définir des tarifs." />
                @else
                    <x-admin.table :entetes="['Nom', 'Ordre', 'Inscriptions', '']">
                        @foreach ($regions as $region)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <form method="POST" action="{{ route('admin.regions.update', $region) }}" id="region-{{ $region->id }}">
                                    @csrf @method('PUT')
                                </form>

                                <td class="px-5 py-3">
                                    <label class="sr-only" for="region-nom-{{ $region->id }}">Nom de la région</label>
                                    <input type="text" name="nom" value="{{ $region->nom }}" form="region-{{ $region->id }}"
                                           id="region-nom-{{ $region->id }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3">
                                    <label class="sr-only" for="region-ordre-{{ $region->id }}">Ordre</label>
                                    <input type="number" name="ordre" value="{{ $region->ordre }}" form="region-{{ $region->id }}"
                                           id="region-ordre-{{ $region->id }}"
                                           class="w-20 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3 text-theme-sm text-gray-500">{{ $region->inscriptions_count }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="submit" form="region-{{ $region->id }}"
                                                class="rounded-lg px-2.5 py-1.5 text-theme-xs font-medium text-brand-700 hover:bg-brand-50 dark:text-brand-300 dark:hover:bg-brand-500/10">
                                            Enregistrer
                                        </button>
                                        <x-admin.confirm :action="route('admin.regions.destroy', $region)"
                                                         :message="'Supprimer la région '.$region->nom.' ?'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-admin.table>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
