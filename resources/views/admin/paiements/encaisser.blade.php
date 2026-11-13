<x-admin-layout title="Encaisser un paiement"
                :fil="['Paiements' => route('admin.paiements.index'), 'Encaissement' => null]">

    {{-- Recherche du dossier à encaisser --}}
    <form method="GET" class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <x-admin.field label="Édition" nom="edition_id">
                <x-admin.select nom="edition_id" onchange="this.form.submit()">
                    @foreach ($editions as $e)
                        <option value="{{ $e->id }}" @selected($edition && $edition->id === $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field label="Retrouver le dossier" nom="q" class="md:col-span-3">
                <div class="flex gap-2">
                    <x-admin.input nom="q" :value="request('q')" placeholder="Code d'inscription, nom, téléphone ou structure" autofocus />
                    <x-admin.button type="submit" variante="secondaire" icone="recherche">Rechercher</x-admin.button>
                </div>
            </x-admin.field>
        </div>

        <p class="mt-4 border-t border-gray-100 pt-4 text-theme-xs text-gray-500 dark:border-gray-800">
            @if (request()->filled('q'))
                {{ $inscriptions->count() }} dossier(s) à encaisser correspondent à la recherche.
            @else
                Les 15 dernières inscriptions en attente de paiement. Utilisez la recherche pour en retrouver une autre.
            @endif
        </p>
    </form>

    <x-admin.card :padding="false">
        @if ($inscriptions->isEmpty())
            <x-admin.empty titre="Aucun dossier à encaisser"
                           texte="Aucune inscription impayée ne correspond à cette recherche." />
        @else
            <x-admin.table :entetes="['Code', 'Participant', 'Contact', 'Statut', 'Montant', '']">
                @foreach ($inscriptions as $inscription)
                    <tr class="align-top hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-5 py-4">
                            <span class="font-mono text-theme-xs text-gray-700 dark:text-gray-300">{{ $inscription->code_inscription }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $inscription->participant?->nom_complet }}</p>
                            <p class="text-theme-xs text-gray-500">{{ $inscription->participant?->structure ?: 'Sans structure' }}</p>
                        </td>
                        <td class="px-5 py-4 text-theme-xs text-gray-500">
                            {{ $inscription->participant?->email }}<br>
                            {{ $inscription->participant?->telephone }}
                        </td>
                        <td class="px-5 py-4">
                            <x-admin.badge :type="$inscription->badgeStatut()">{{ $inscription->libelleStatut() }}</x-admin.badge>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">
                            {{ number_format($inscription->montant, 0, ',', ' ') }} F
                        </td>
                        <td class="px-5 py-4 text-right">
                            <x-admin.button taille="petit" icone="monnaie"
                                            :href="route('admin.inscriptions.show', $inscription).'#encaissement'">
                                Encaisser
                            </x-admin.button>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin-layout>
