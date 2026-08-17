<x-admin-layout title="Inscriptions" :fil="['Inscriptions' => null]">

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
                    @foreach (\App\Models\Inscription::STATUTS as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field label="Recherche" nom="q" class="md:col-span-2">
                <div class="flex gap-2">
                    <x-admin.input nom="q" :value="request('q')" placeholder="Nom, email, téléphone ou code d'inscription" />
                    <x-admin.button type="submit" variante="secondaire" icone="recherche">Filtrer</x-admin.button>
                </div>
            </x-admin.field>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
            <p class="text-theme-xs text-gray-500">
                {{ $inscriptions->total() }} inscription(s) correspondent aux filtres.
            </p>
            <div class="flex flex-wrap gap-2">
                @if (request()->hasAny(['statut', 'q']))
                    <x-admin.button variante="discret" taille="petit"
                                    :href="route('admin.inscriptions.index', ['edition_id' => $edition?->id])">
                        Réinitialiser
                    </x-admin.button>
                @endif
                <x-admin.button taille="petit" icone="telecharger"
                                :href="route('admin.inscriptions.export', array_filter([
                                    'edition_id' => $edition?->id,
                                    'statut' => request('statut'),
                                    'q' => request('q'),
                                ]))">
                    Exporter en Excel
                </x-admin.button>
            </div>
        </div>
    </form>

    {{-- Indicateurs --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-admin.stat libelle="Total" :valeur="number_format($stats['total'], 0, ',', ' ')" icone="liste" />
        <x-admin.stat libelle="Payées" :valeur="number_format($stats['payees'], 0, ',', ' ')" icone="badge" />
        <x-admin.stat libelle="Recettes" :valeur="number_format($stats['ca'], 0, ',', ' ').' F'" icone="monnaie" />
    </div>

    <x-admin.card :padding="false">
        @if ($inscriptions->isEmpty())
            <x-admin.empty titre="Aucune inscription"
                           texte="Aucun dossier ne correspond aux filtres sélectionnés." />
        @else
            <x-admin.table :entetes="['Code', 'Participant', 'Contact', 'Origine', 'Statut', 'Montant', 'Reçue le']">
                @foreach ($inscriptions as $inscription)
                    <tr class="align-top hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-5 py-4">
                            <span class="font-mono text-theme-xs text-gray-700 dark:text-gray-300">{{ $inscription->code_inscription }}</span>
                            @if ($inscription->besoins_particuliers)
                                <span class="mt-1.5 block" title="{{ $inscription->besoins_particuliers }}">
                                    <x-admin.badge type="attente">Besoin particulier</x-admin.badge>
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $inscription->participant?->nom_complet }}</p>
                            <p class="text-theme-xs text-gray-500">
                                {{ $inscription->participant?->structure ?: 'Sans structure' }}
                                @if ($inscription->participant?->fonction) &middot; {{ $inscription->participant->fonction }} @endif
                            </p>
                            <p class="mt-1 text-theme-xs text-gray-400">{{ $inscription->joursFormates() }}</p>
                        </td>
                        <td class="px-5 py-4 text-theme-xs text-gray-500">
                            {{ $inscription->participant?->email }}<br>
                            {{ $inscription->participant?->telephone }}
                        </td>
                        <td class="px-5 py-4 text-theme-xs text-gray-500">
                            {{ $inscription->region?->nom ?? '—' }}<br>
                            <span class="text-gray-400">{{ $inscription->profil?->nom ?? '—' }}</span>
                            @if ($inscription->source_connaissance)
                                <span class="mt-1 block text-gray-400">via {{ $inscription->source_connaissance }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <x-admin.badge :type="$inscription->badgeStatut()">{{ $inscription->libelleStatut() }}</x-admin.badge>
                            @if ($inscription->paid_at)
                                <span class="mt-1 block text-theme-xs text-gray-400">{{ $inscription->paid_at->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">
                            {{ number_format($inscription->montant, 0, ',', ' ') }} F
                        </td>
                        <td class="px-5 py-4 text-theme-xs text-gray-400">
                            {{ $inscription->created_at->format('d/m/Y') }}<br>{{ $inscription->created_at->format('H:i') }}
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>

            <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">
                {{ $inscriptions->withQueryString()->links() }}
            </div>
        @endif
    </x-admin.card>
</x-admin-layout>
