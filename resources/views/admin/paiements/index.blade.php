@php
    $parMode = $stats['par_mode'];
    $totalMobile = (int) ($parMode['ligdicash']->total ?? 0);
    $totalManuel = (int) $parMode->except('ligdicash')->sum('total');
    $nombreManuel = (int) $parMode->except('ligdicash')->sum('nombre');
@endphp

<x-admin-layout title="Paiements" :fil="['Paiements' => null]">

    {{-- Filtres --}}
    <form method="GET" class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <x-admin.field label="Édition" nom="edition_id">
                <x-admin.select nom="edition_id" onchange="this.form.submit()">
                    @foreach ($editions as $e)
                        <option value="{{ $e->id }}" @selected($edition && $edition->id === $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field label="Statut" nom="statut">
                <x-admin.select nom="statut" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach ($statuts as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field label="Mode de paiement" nom="mode">
                <x-admin.select nom="mode" onchange="this.form.submit()">
                    <option value="">Tous les modes</option>
                    @foreach ($modes as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(request('mode') === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field label="Du" nom="du">
                <x-admin.input nom="du" type="date" :value="request('du')" />
            </x-admin.field>

            <x-admin.field label="Au" nom="au">
                <x-admin.input nom="au" type="date" :value="request('au')" />
            </x-admin.field>

            <x-admin.field label="Recherche" nom="q" class="md:col-span-2">
                <div class="flex gap-2">
                    <x-admin.input nom="q" :value="request('q')" placeholder="Nom, téléphone, code d'inscription ou référence" />
                    <x-admin.button type="submit" variante="secondaire" icone="recherche">Filtrer</x-admin.button>
                </div>
            </x-admin.field>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
            <p class="text-theme-xs text-gray-500">
                {{ $paiements->total() }} paiement(s) correspondent aux filtres.
            </p>
            <div class="flex flex-wrap gap-2">
                @hasanyrole('super-admin|gestionnaire|comptable')
                    <x-admin.button taille="petit" icone="monnaie" :href="route('admin.paiements.encaisser')">
                        Encaisser un paiement
                    </x-admin.button>
                @endhasanyrole
                @if (request()->hasAny(['statut', 'mode', 'q', 'du', 'au']))
                    <x-admin.button variante="discret" taille="petit"
                                    :href="route('admin.paiements.index', ['edition_id' => $edition?->id])">
                        Réinitialiser
                    </x-admin.button>
                @endif
                <x-admin.button taille="petit" icone="telecharger"
                                :href="route('admin.paiements.export', array_filter([
                                    'edition_id' => $edition?->id,
                                    'statut' => request('statut'),
                                    'mode' => request('mode'),
                                    'q' => request('q'),
                                    'du' => request('du'),
                                    'au' => request('au'),
                                ]))">
                    Exporter en Excel
                </x-admin.button>
            </div>
        </div>
    </form>

    {{-- Indicateurs : paiements réussis du périmètre filtré --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-admin.stat libelle="Encaissé ({{ $stats['nombre'] }})"
                      :valeur="number_format($stats['encaisse'], 0, ',', ' ').' F'" icone="monnaie" />
        <x-admin.stat libelle="Mobile money"
                      :valeur="number_format($totalMobile, 0, ',', ' ').' F'" icone="badge" />
        <x-admin.stat libelle="Paiements manuels ({{ $nombreManuel }})"
                      :valeur="number_format($totalManuel, 0, ',', ' ').' F'" icone="liste" />
    </div>

    <x-admin.card :padding="false">
        @if ($paiements->isEmpty())
            <x-admin.empty titre="Aucun paiement"
                           texte="Aucun paiement ne correspond aux filtres sélectionnés." />
        @else
            <x-admin.table :entetes="['Date', 'Participant', 'Mode', 'Référence', 'Montant', 'Statut', '']">
                @foreach ($paiements as $paiement)
                    <tr class="align-top hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-5 py-4 text-theme-xs text-gray-400">
                            {{ $paiement->created_at->format('d/m/Y') }}<br>{{ $paiement->created_at->format('H:i') }}
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $paiement->inscription?->participant?->nom_complet }}</p>
                            <p class="font-mono text-theme-xs text-gray-500">{{ $paiement->inscription?->code_inscription }}</p>
                        </td>
                        <td class="px-5 py-4 text-theme-xs text-gray-500">
                            <span class="text-theme-sm text-gray-700 dark:text-gray-300">{{ $paiement->libelleMode() }}</span>
                            @if ($paiement->estManuel() && $paiement->enregistrePar)
                                <span class="mt-1 block text-gray-400">par {{ $paiement->enregistrePar->name }}</span>
                            @elseif ($paiement->operateur)
                                <span class="mt-1 block text-gray-400">{{ $paiement->operateur }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-theme-xs text-gray-500">
                            {{ $paiement->reference_manuelle ?? $paiement->reference_ligdicash ?? '—' }}
                            @if ($paiement->note)
                                <span class="mt-1 block text-gray-400" title="{{ $paiement->note }}">{{ \Illuminate\Support\Str::limit($paiement->note, 40) }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">
                            {{ number_format($paiement->montant, 0, ',', ' ') }} F
                        </td>
                        <td class="px-5 py-4">
                            <x-admin.badge :type="match ($paiement->statut) {
                                'completed' => 'succes',
                                'pending' => 'attente',
                                'failed', 'cancelled' => 'erreur',
                                default => 'neutre',
                            }">{{ $paiement->libelleStatut() }}</x-admin.badge>
                            @if ($paiement->confirmed_at)
                                <span class="mt-1 block text-theme-xs text-gray-400">{{ $paiement->confirmed_at->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($paiement->inscription)
                                <x-admin.button variante="discret" taille="petit"
                                                :href="route('admin.inscriptions.show', $paiement->inscription)">
                                    Voir
                                </x-admin.button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>

            <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">
                {{ $paiements->withQueryString()->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
