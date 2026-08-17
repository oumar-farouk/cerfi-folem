<x-admin-layout title="Éditions" :fil="['Éditions' => null]">

    <x-admin.card :padding="false"
                  titre="Toutes les éditions"
                  description="Une seule édition peut être active à la fois. Passer une édition en « active » archive automatiquement les autres.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.editions.create')" icone="plus">Nouvelle édition</x-admin.button>
        </x-slot:actions>

        @if ($editions->isEmpty())
            <x-admin.empty icone="calendrier"
                           titre="Aucune édition"
                           texte="Créez la première édition pour ouvrir les inscriptions du forum.">
                <x-slot:action>
                    <x-admin.button :href="route('admin.editions.create')" icone="plus">Créer une édition</x-admin.button>
                </x-slot:action>
            </x-admin.empty>
        @else
            <x-admin.table :entetes="['Édition', 'Dates', 'Statut', 'Inscriptions', 'Contenus', '']">
                @foreach ($editions as $edition)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $edition->nom }}</p>
                            @if ($edition->theme)
                                <p class="mt-0.5 max-w-sm truncate text-theme-xs text-gray-500">{{ $edition->theme }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">
                            {{ $edition->date_debut?->format('d/m/Y') }}<br>
                            <span class="text-theme-xs text-gray-400">au {{ $edition->date_fin?->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <x-admin.badge :type="match ($edition->statut) {
                                'active' => 'succes',
                                'draft' => 'attente',
                                default => 'neutre',
                            }">
                                {{ ['draft' => 'Brouillon', 'active' => 'Active', 'archived' => 'Archivée'][$edition->statut] ?? $edition->statut }}
                            </x-admin.badge>
                        </td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">
                            {{ $edition->inscriptions_count }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1.5 text-theme-xs">
                                <a href="{{ route('admin.editions.programmes.index', $edition) }}" class="rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300">Programme</a>
                                <a href="{{ route('admin.editions.intervenants.index', $edition) }}" class="rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300">Intervenants</a>
                                <a href="{{ route('admin.editions.partenaires.index', $edition) }}" class="rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300">Partenaires</a>
                                <a href="{{ route('admin.editions.galerie.index', $edition) }}" class="rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300">Galerie</a>
                                <a href="{{ route('admin.editions.prix.index', $edition) }}" class="rounded-lg bg-brand-50 px-2.5 py-1 font-medium text-brand-700 hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-300">Tarifs</a>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <x-admin.button variante="discret" taille="petit" icone="crayon"
                                                :href="route('admin.editions.edit', $edition)">Modifier</x-admin.button>

                                @if ($edition->inscriptions_count === 0)
                                    <x-admin.confirm :action="route('admin.editions.destroy', $edition)"
                                                     :message="'Supprimer définitivement '.$edition->nom.' ?'" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin-layout>
