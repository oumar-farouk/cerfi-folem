<x-admin-layout :title="$inscription->participant->nom_complet"
                :fil="['Inscriptions' => route('admin.inscriptions.index'), $inscription->code_inscription => null]">

    @if (session('succes'))
        <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-theme-sm text-green-800">{{ session('succes') }}</div>
    @endif
    @if (session('erreur'))
        <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-theme-sm text-red-800">{{ session('erreur') }}</div>
    @endif

    {{-- En-tête du dossier --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div>
            <p class="font-mono text-theme-sm text-gray-700 dark:text-gray-300">{{ $inscription->code_inscription }}</p>
            <p class="mt-1 text-theme-xs text-gray-500">{{ $inscription->edition->nom }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-admin.badge :type="$inscription->badgeStatut()">{{ $inscription->libelleStatut() }}</x-admin.badge>
            <span class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                {{ number_format($inscription->montant, 0, ',', ' ') }} F
            </span>
            @if ($inscription->estPayee())
                @hasanyrole('super-admin|gestionnaire|moderateur')
                    <form method="POST" action="{{ route('admin.inscriptions.recepisse', $inscription) }}">
                        @csrf
                        <x-admin.button type="submit" variante="secondaire" taille="petit">Renvoyer le récépissé</x-admin.button>
                    </form>
                @endhasanyrole
            @endif
        </div>
    </div>

    {{-- Encaissement manuel : espèces, virement, chèque --}}
    @unless ($inscription->estPayee() || $inscription->statut === 'cancelled')
        @hasanyrole('super-admin|gestionnaire|comptable')
            <form id="encaissement" method="POST" action="{{ route('admin.paiements.manuel.store', $inscription) }}"
                  class="mb-6 scroll-mt-24 rounded-2xl border border-amber-200 bg-amber-50/60 p-5 dark:border-amber-500/30 dark:bg-amber-500/5"
                  onsubmit="return confirm('Confirmer la réception de {{ number_format($inscription->montant, 0, ',', ' ') }} F ? Le récépissé sera envoyé au participant.')">
                @csrf

                <h2 class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                    Encaisser un paiement · {{ number_format($inscription->montant, 0, ',', ' ') }} F
                </h2>
                <p class="mb-4 mt-1 text-theme-xs text-gray-500">
                    L'inscription passera en « Payée », le récépissé sera généré et envoyé au participant.
                </p>

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-theme-sm text-red-800">{{ $errors->first() }}</div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-admin.field label="Mode de paiement" nom="mode">
                        <x-admin.select nom="mode" required>
                            @foreach ($modesManuels as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('mode') === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </x-admin.select>
                    </x-admin.field>

                    <x-admin.field label="N° de virement, chèque ou reçu" nom="reference_manuelle">
                        <x-admin.input nom="reference_manuelle" :value="old('reference_manuelle')" maxlength="100"
                                       placeholder="Obligatoire pour un virement ou un chèque" />
                    </x-admin.field>

                    <x-admin.field label="Note (facultatif)" nom="note" class="md:col-span-2">
                        <textarea id="note" name="note" rows="2" maxlength="500"
                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 dark:border-gray-700 dark:text-white/90">{{ old('note') }}</textarea>
                    </x-admin.field>
                </div>

                <div class="mt-4">
                    <x-admin.button type="submit" icone="monnaie">Valider l'encaissement</x-admin.button>
                </div>
            </form>
        @endhasanyrole
    @endunless

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Dossier --}}
        <x-admin.card>
            <h2 class="mb-4 text-theme-sm font-semibold text-gray-800 dark:text-white/90">Dossier</h2>
            <dl class="space-y-3 text-theme-sm">
                @foreach ([
                    'E-mail' => $inscription->participant->email,
                    'Téléphone' => $inscription->participant->telephone,
                    'Structure' => $inscription->participant->structure,
                    'Fonction' => $inscription->participant->fonction,
                    'Région' => $inscription->region?->nom,
                    'Profil' => $inscription->profil?->nom,
                    'Jours' => $inscription->joursFormates(),
                    'Payée le' => $inscription->paid_at?->format('d/m/Y H:i'),
                    'Besoins particuliers' => $inscription->besoins_particuliers,
                ] as $libelle => $valeur)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">{{ $libelle }}</dt>
                        <dd class="text-right text-gray-800 dark:text-white/90">{{ $valeur ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-admin.card>

        <div class="space-y-6">

            {{-- Paiements --}}
            <x-admin.card :padding="false">
                <h2 class="border-b border-gray-100 px-5 py-4 text-theme-sm font-semibold text-gray-800 dark:border-gray-800 dark:text-white/90">Paiements</h2>
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($inscription->paiements as $paiement)
                        <li class="flex items-start justify-between gap-3 px-5 py-4">
                            <div>
                                <p class="text-theme-sm text-gray-800 dark:text-white/90">{{ $paiement->libelleMode() }}</p>
                                <p class="text-theme-xs text-gray-400">
                                    {{ $paiement->created_at->format('d/m/Y H:i') }}
                                    @if ($paiement->reference_manuelle) &middot; {{ $paiement->reference_manuelle }} @endif
                                    @if ($paiement->enregistrePar) &middot; par {{ $paiement->enregistrePar->name }} @endif
                                </p>
                                @if ($paiement->note)
                                    <p class="mt-1 text-theme-xs text-gray-400">{{ $paiement->note }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-theme-sm text-gray-700 dark:text-gray-300">{{ number_format($paiement->montant, 0, ',', ' ') }} F</p>
                                <x-admin.badge :type="match ($paiement->statut) {
                                    'completed' => 'succes',
                                    'pending' => 'attente',
                                    'failed', 'cancelled' => 'erreur',
                                    default => 'neutre',
                                }">{{ $paiement->libelleStatut() }}</x-admin.badge>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-theme-sm text-gray-400">Aucun paiement enregistré.</li>
                    @endforelse
                </ul>
            </x-admin.card>

            {{-- Présence --}}
            <x-admin.card :padding="false">
                <h2 class="border-b border-gray-100 px-5 py-4 text-theme-sm font-semibold text-gray-800 dark:border-gray-800 dark:text-white/90">Présence</h2>
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($inscription->pointages as $pointage)
                        <li class="flex justify-between gap-3 px-5 py-4 text-theme-sm">
                            <span class="text-gray-800 dark:text-white/90">{{ $pointage->jour->translatedFormat('l d F') }} à {{ $pointage->pointe_a->format('H:i') }}</span>
                            <span class="text-theme-xs text-gray-400">
                                {{ $pointage->methode === 'manuel' ? 'saisie manuelle' : 'scan' }}
                                @if ($pointage->agent) &middot; {{ $pointage->agent->name }} @endif
                            </span>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-theme-sm text-gray-400">Aucun pointage pour le moment.</li>
                    @endforelse
                </ul>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
