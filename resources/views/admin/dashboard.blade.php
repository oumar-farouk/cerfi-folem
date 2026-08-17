<x-admin-layout title="Tableau de bord">

    @if (! $edition)
        <x-admin.card>
            <x-admin.empty
                icone="calendrier"
                titre="Aucune édition enregistrée"
                texte="Créez une première édition pour ouvrir les inscriptions et commencer à suivre les indicateurs.">
                <x-slot:action>
                    <x-admin.button :href="route('admin.editions.create')" icone="plus">Créer une édition</x-admin.button>
                </x-slot:action>
            </x-admin.empty>
        </x-admin.card>
    @else

        {{-- Sélecteur d'édition --}}
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <x-admin.field label="Édition suivie" nom="edition_id" class="min-w-56">
                <x-admin.select nom="edition_id" onchange="this.form.submit()">
                    @foreach ($editions as $e)
                        <option value="{{ $e->id }}" @selected($e->id === $edition->id)>
                            {{ $e->nom }} @if ($e->statut === 'active') (active) @endif
                        </option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <div class="ml-auto flex flex-wrap gap-2">
                <x-admin.button variante="secondaire" :href="route('admin.editions.edit', $edition)" icone="crayon">
                    Modifier l'édition
                </x-admin.button>
                <x-admin.button :href="route('admin.inscriptions.index', ['edition_id' => $edition->id])" icone="liste">
                    Voir les inscriptions
                </x-admin.button>
            </div>
        </form>

        <div class="mb-6 rounded-2xl bg-brand-900 motif-mashrabiya p-6 text-white">
            <p class="text-theme-xs uppercase tracking-wide text-brand-200">
                {{ ['active' => 'Édition active', 'draft' => 'Brouillon', 'archived' => 'Archivée'][$edition->statut] ?? $edition->statut }}
            </p>
            <h2 class="mt-1 font-display text-title-sm font-bold">{{ $edition->nom }}</h2>
            @if ($edition->theme)
                <p class="mt-1 max-w-2xl text-theme-sm italic text-brand-100">{{ $edition->theme }}</p>
            @endif
            <p class="mt-3 text-theme-sm text-brand-200">
                {{ $edition->date_debut?->translatedFormat('d F Y') }} au {{ $edition->date_fin?->translatedFormat('d F Y') }}
                @if ($edition->lieu) &middot; {{ $edition->lieu }} @endif
            </p>
        </div>

        {{-- Indicateurs --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat libelle="Inscriptions totales" :valeur="number_format($stats['total'], 0, ',', ' ')" icone="liste" />

            <x-admin.stat
                libelle="Inscriptions payées"
                :valeur="number_format($stats['payees'], 0, ',', ' ')"
                icone="badge"
                :variation="$stats['conversion'].' %'"
                :sens="$stats['conversion'] >= 60 ? 'haut' : ($stats['conversion'] >= 30 ? 'neutre' : 'bas')"
                note="Taux de conversion" />

            <x-admin.stat
                libelle="En attente de paiement"
                :valeur="number_format($stats['attente'], 0, ',', ' ')"
                icone="horloge"
                note="Codes générés, paiement non finalisé" />

            <x-admin.stat
                libelle="Recettes encaissées"
                :valeur="number_format($stats['recettes'], 0, ',', ' ').' F'"
                icone="monnaie"
                :note="'Panier moyen : '.number_format($stats['panier'], 0, ',', ' ').' FCFA'" />
        </div>

        {{-- Courbe des trente derniers jours --}}
        <div class="mt-6">
            <x-admin.card
                titre="Rythme des inscriptions"
                description="Trente derniers jours : inscriptions créées et paiements confirmés.">
                <div x-data="graphique({
                        chart: { type: 'area', height: 320, fontFamily: 'inherit', toolbar: { show: false } },
                        colors: ['#00a650', '#ef6325'],
                        series: [
                            { name: 'Inscriptions créées', data: @js($serieJours['creees']) },
                            { name: 'Paiements confirmés', data: @js($serieJours['payees']) },
                        ],
                        xaxis: { categories: @js($serieJours['labels']), tickAmount: 10, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { min: 0, forceNiceScale: true },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2.5 },
                        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
                        grid: { strokeDashArray: 4, borderColor: '#e4e7ec' },
                        legend: { position: 'top', horizontalAlign: 'right' },
                        tooltip: { x: { show: true } },
                    })"
                     wire:ignore></div>
            </x-admin.card>
        </div>

        {{-- Répartitions --}}
        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-admin.card titre="Par région" description="Dix régions les plus représentées.">
                @if (empty($parRegion['labels']))
                    <x-admin.empty icone="carte" titre="Pas encore de données" texte="La répartition apparaîtra dès les premières inscriptions." />
                @else
                    <div x-data="graphique({
                            chart: { type: 'bar', height: 340, fontFamily: 'inherit', toolbar: { show: false } },
                            colors: ['#00a650'],
                            series: [{ name: 'Inscriptions', data: @js($parRegion['valeurs']) }],
                            xaxis: { categories: @js($parRegion['labels']) },
                            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                            dataLabels: { enabled: false },
                            grid: { strokeDashArray: 4, borderColor: '#e4e7ec' },
                        })"
                         wire:ignore></div>
                @endif
            </x-admin.card>

            <x-admin.card titre="Par profil" description="Répartition des participants selon la grille tarifaire.">
                @if (empty($parProfil['labels']))
                    <x-admin.empty icone="badge" titre="Pas encore de données" texte="La répartition apparaîtra dès les premières inscriptions." />
                @else
                    <div x-data="graphique({
                            chart: { type: 'donut', height: 340, fontFamily: 'inherit' },
                            colors: ['#00a650', '#ef6325', '#1fb978', '#f79a6a', '#05572e', '#8fe0bb', '#b23c10'],
                            series: @js($parProfil['valeurs']),
                            labels: @js($parProfil['labels']),
                            legend: { position: 'bottom' },
                            dataLabels: { enabled: true, formatter: (v) => Math.round(v) + '%' },
                            plotOptions: { pie: { donut: { size: '62%' } } },
                        })"
                         wire:ignore></div>
                @endif
            </x-admin.card>
        </div>

        {{-- Dernières inscriptions --}}
        <div class="mt-6">
            <x-admin.card titre="Dernières inscriptions" :padding="false">
                <x-slot:actions>
                    <x-admin.button variante="secondaire" taille="petit"
                                    :href="route('admin.inscriptions.index', ['edition_id' => $edition->id])">
                        Tout voir
                    </x-admin.button>
                </x-slot:actions>

                @if ($dernieres->isEmpty())
                    <x-admin.empty titre="Aucune inscription pour l'instant"
                                   texte="Les inscriptions apparaîtront ici dès que le formulaire public sera utilisé." />
                @else
                    <x-admin.table :entetes="['Code', 'Participant', 'Profil', 'Statut', 'Montant', 'Reçue le']">
                        @foreach ($dernieres as $inscription)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="px-5 py-3.5 font-mono text-theme-xs text-gray-600 dark:text-gray-300">
                                    {{ $inscription->code_inscription }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ $inscription->participant?->nom_complet }}
                                    </p>
                                    <p class="text-theme-xs text-gray-500">{{ $inscription->region?->nom }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-theme-sm text-gray-600 dark:text-gray-300">
                                    {{ $inscription->profil?->nom ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <x-admin.badge :type="$inscription->badgeStatut()">{{ $inscription->libelleStatut() }}</x-admin.badge>
                                </td>
                                <td class="px-5 py-3.5 text-theme-sm text-gray-600 dark:text-gray-300">
                                    {{ number_format($inscription->montant, 0, ',', ' ') }} F
                                </td>
                                <td class="px-5 py-3.5 text-theme-xs text-gray-400">
                                    {{ $inscription->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </x-admin.table>
                @endif
            </x-admin.card>
        </div>
    @endif
</x-admin-layout>
