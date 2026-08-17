<x-public-layout>
    <div class="max-w-lg mx-auto p-8 text-center">
        @if ($inscription->estPayee())
            <h1 class="text-2xl font-bold text-emerald-700">Paiement confirmé</h1>
            <p class="mt-2">Merci {{ $inscription->participant->prenom }}, votre inscription à
                {{ $inscription->edition->nom }} est validée.</p>
            <a href="{{ route('recu.telecharger', $inscription->code_inscription) }}"
               class="inline-block mt-6 bg-emerald-700 text-white px-6 py-2 rounded font-semibold">
                Télécharger mon récépissé
            </a>
        @else
            <h1 class="text-2xl font-bold text-amber-600">Paiement en cours de vérification</h1>
            <p class="mt-2">Nous confirmons votre transaction avec l'opérateur mobile money. Cela peut prendre
                quelques minutes. Revenez sur cette page avec votre code : {{ $inscription->code_inscription }}</p>
        @endif
    </div>
</x-public-layout>
