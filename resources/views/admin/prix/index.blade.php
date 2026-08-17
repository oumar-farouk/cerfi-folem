<x-admin-layout :title="'Grille tarifaire — '.$edition->nom"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Tarifs' => null]">

    @if ($regions->isEmpty() || $profils->isEmpty())
        <x-admin.card>
            <x-admin.empty icone="monnaie"
                           titre="Paramétrage incomplet"
                           texte="Une grille tarifaire croise les régions et les profils. Renseignez au moins une région et un profil avant de fixer les tarifs.">
                <x-slot:action>
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-admin.button :href="route('admin.regions.index')" variante="secondaire">Gérer les régions</x-admin.button>
                        <x-admin.button :href="route('admin.profils.index')">Gérer les profils</x-admin.button>
                    </div>
                </x-slot:action>
            </x-admin.empty>
        </x-admin.card>
    @else
        <form method="POST" action="{{ route('admin.editions.prix.store', $edition) }}">
            @csrf

            <x-admin.card :padding="false"
                          titre="Tarifs par région et par profil"
                          description="Un champ vide retire la combinaison : elle ne sera pas proposée au participant lors de l'inscription.">
                <x-slot:actions>
                    <x-admin.button variante="secondaire" taille="petit" :href="route('admin.regions.index')">Régions</x-admin.button>
                    <x-admin.button variante="secondaire" taille="petit" :href="route('admin.profils.index')">Profils</x-admin.button>
                </x-slot:actions>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[40rem] text-left">
                        <thead class="border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th scope="col" class="sticky left-0 bg-white px-5 py-3 text-theme-xs font-medium uppercase tracking-wide text-gray-500 dark:bg-gray-900">
                                    Région
                                </th>
                                @foreach ($profils as $profil)
                                    <th scope="col" class="px-4 py-3 text-theme-xs font-medium uppercase tracking-wide text-gray-500">
                                        {{ $profil->nom }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($regions as $region)
                                <tr>
                                    <th scope="row" class="sticky left-0 bg-white px-5 py-3 text-theme-sm font-medium text-gray-800 dark:bg-gray-900 dark:text-white/90">
                                        {{ $region->nom }}
                                    </th>
                                    @foreach ($profils as $profil)
                                        <td class="px-4 py-2.5">
                                            <label class="sr-only" for="tarif-{{ $region->id }}-{{ $profil->id }}">
                                                Tarif {{ $region->nom }} / {{ $profil->nom }}
                                            </label>
                                            <input type="number" min="0" step="500" inputmode="numeric"
                                                   id="tarif-{{ $region->id }}-{{ $profil->id }}"
                                                   name="montants[{{ $region->id }}][{{ $profil->id }}]"
                                                   value="{{ old("montants.{$region->id}.{$profil->id}", data_get($grille, "{$region->id}.{$profil->id}")) }}"
                                                   placeholder="—"
                                                   class="w-28 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-5 py-4 dark:border-gray-800">
                    <p class="text-theme-xs text-gray-500">Montants exprimés en francs CFA.</p>
                    <x-admin.button type="submit">Enregistrer la grille</x-admin.button>
                </div>
            </x-admin.card>
        </form>
    @endif
</x-admin-layout>
