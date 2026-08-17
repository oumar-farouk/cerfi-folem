<x-public-layout>
    <div class="max-w-lg mx-auto p-8 text-center">
        <h1 class="text-2xl font-bold text-red-600">Paiement annulé</h1>
        <p class="mt-2">Vous pouvez réessayer à tout moment avec votre code : {{ $inscription->code_inscription }}</p>
        <a href="{{ route('paiement.initier', $inscription->code_inscription) }}"
           class="inline-block mt-6 bg-emerald-700 text-white px-6 py-2 rounded font-semibold">
            Réessayer le paiement
        </a>
    </div>
</x-public-layout>
