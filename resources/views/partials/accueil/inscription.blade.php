@php
    $ouverte = $edition?->estOuverte();
    $tarifMini = $edition?->tarifMinimum();
@endphp

<x-site.section id="inscription" fond="blanc">
    <div class="overflow-hidden rounded-3xl border border-sand-200 bg-sand-50">
        <div class="grid grid-cols-1 lg:grid-cols-2">

            <div class="p-8 sm:p-10 lg:p-12">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-accent-500">Participation</p>
                <h2 class="mt-3 font-display text-3xl font-bold leading-tight text-brand-900 sm:text-4xl">
                    Prêt à rejoindre le forum ?
                </h2>

                @if ($ouverte)
                    <p class="mt-5 leading-relaxed text-sand-600">
                        L'inscription prend deux minutes. Vous recevez immédiatement un code personnel, puis vous
                        payez par mobile money quand vous le souhaitez. Le récépissé se télécharge dès le paiement
                        confirmé.
                    </p>

                    @if ($tarifMini)
                        <p class="mt-6 text-sm text-sand-500">
                            Tarifs à partir de
                            <span class="font-display text-2xl font-bold text-brand-800">{{ number_format($tarifMini, 0, ',', ' ') }} FCFA</span>
                            selon votre région et votre profil.
                        </p>
                    @endif

                    <div class="mt-8 flex flex-wrap gap-3">
                        <x-site.btn :href="route('inscription.form', $edition)" taille="grand">Commencer mon inscription</x-site.btn>
                        <x-site.btn href="#retrouver-dossier" variante="contour" taille="grand">J'ai déjà un code</x-site.btn>
                    </div>
                @else
                    <p class="mt-5 leading-relaxed text-sand-600">
                        Les inscriptions en ligne ne sont pas ouvertes pour le moment. Écrivez-nous pour être
                        informé de l'ouverture.
                    </p>
                    <div class="mt-8">
                        <x-site.btn :href="'mailto:'.config('folem.contact.email')" taille="grand">Nous écrire</x-site.btn>
                    </div>
                @endif
            </div>

            {{-- Marche à suivre --}}
            <div class="bg-brand-900 motif-mashrabiya p-8 text-sand-100 sm:p-10 lg:p-12">
                <h3 class="font-display text-xl font-bold text-white">Comment ça se passe</h3>

                <ol class="mt-7 space-y-7">
                    @foreach ([
                        ['titre' => 'Vous remplissez le formulaire', 'texte' => 'Identité, région, profil et jours de présence. Le tarif s\'affiche automatiquement.'],
                        ['titre' => 'Vous recevez votre code', 'texte' => 'Un code du type FLM-XXXXXX, à conserver. Il permet de reprendre le dossier plus tard.'],
                        ['titre' => 'Vous payez par mobile money', 'texte' => 'Orange Money ou Moov Money, en suivant les instructions de l\'opérateur.'],
                        ['titre' => 'Vous téléchargez votre récépissé', 'texte' => 'Le PDF est disponible immédiatement après confirmation du paiement.'],
                    ] as $index => $etape)
                        <li class="flex gap-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-accent-400/60 font-mono text-sm font-bold text-accent-300">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-white">{{ $etape['titre'] }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-sand-300">{{ $etape['texte'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</x-site.section>
