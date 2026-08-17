<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    // Un seul état dominant par écran : couleur, icône et message ne laissent aucune ambiguïté.
    $etats = [
        'valide' => [
            'couleur' => 'bg-emerald-600',
            'icone' => 'check',
            'titre' => 'Inscription valide',
            'sous_titre' => 'Accès autorisé',
        ],
        'non_paye' => [
            'couleur' => 'bg-amber-500',
            'icone' => 'alert',
            'titre' => 'Paiement non confirmé',
            'sous_titre' => "Inscription trouvée mais non payée — orienter vers l'accueil",
        ],
        'invalide' => [
            'couleur' => 'bg-red-600',
            'icone' => 'cross',
            'titre' => 'Code invalide',
            'sous_titre' => 'Ce récépissé ne correspond à aucun enregistrement valide',
        ],
        'introuvable' => [
            'couleur' => 'bg-red-600',
            'icone' => 'cross',
            'titre' => 'Inscription introuvable',
            'sous_titre' => "Ce code n'existe pas dans la base",
        ],
    ];
    $etat = $etats[$statut];
@endphp
<body class="min-h-screen {{ $etat['couleur'] }} flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 text-center">

        {{-- Icône géante, seule chose qu'un agent presse a besoin de voir --}}
        <div class="w-24 h-24 rounded-full {{ $etat['couleur'] }} flex items-center justify-center mx-auto mb-6">
            @if ($etat['icone'] === 'check')
                <svg class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            @elseif ($etat['icone'] === 'alert')
                <svg class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @else
                <svg class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            @endif
        </div>

        <h1 class="text-2xl font-bold mb-1">{{ $etat['titre'] }}</h1>
        <p class="text-gray-500 mb-6">{{ $etat['sous_titre'] }}</p>

        @if (isset($inscription))
            <div class="bg-gray-50 rounded-lg p-4 text-left text-sm space-y-1.5 mb-4">
                <p><span class="text-gray-400">Participant :</span> <span class="font-semibold">{{ $inscription->participant->prenom }} {{ $inscription->participant->nom }}</span></p>
                <p><span class="text-gray-400">Édition :</span> {{ $inscription->edition->nom }}</p>
                @if ($inscription->profil)
                    <p><span class="text-gray-400">Profil :</span> {{ $inscription->profil->nom }}</p>
                @endif
                @if ($inscription->region)
                    <p><span class="text-gray-400">Région :</span> {{ $inscription->region->nom }}</p>
                @endif
            </div>
        @endif

        <p class="text-xs text-gray-400 font-mono">{{ $code }}</p>
        <p class="text-xs text-gray-300 mt-4">Vérifié le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
