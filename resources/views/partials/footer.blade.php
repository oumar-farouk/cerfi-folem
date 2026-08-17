@php
    $contact = config('folem.contact');
    $reseaux = array_filter(config('folem.reseaux', []));
    $liensUtiles = config('folem.liens_utiles', []);
@endphp

<footer class="bg-brand-950 motif-mashrabiya text-sand-100">
    <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-4">

            <div class="md:col-span-2">
                <img src="{{ asset('img/brand/folem-logo-blanc.png') }}" alt="FOLEM" class="h-12 w-auto">
                <p class="mt-5 max-w-md text-sm leading-relaxed text-sand-300">
                    Le Forum du Leadership et de l'Entrepreneuriat Musulmans est une initiative du
                    {{ config('folem.organisation.nom_complet') }}, association burkinabè reconnue depuis 1989.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <img src="{{ asset('img/brand/cerfi-logo.png') }}" alt="CERFI"
                         class="h-14 w-auto rounded-lg bg-white/95 p-1.5">
                    <p class="text-xs text-sand-400">Organisé par le CERFI</p>
                </div>
            </div>

            <div>
                <h2 class="font-display text-lg font-bold text-white">Nous joindre</h2>
                <address class="mt-4 space-y-2 text-sm not-italic text-sand-300">
                    <p>{{ $contact['adresse'] }}</p>
                    <p>
                        <a href="mailto:{{ $contact['email'] }}" class="transition hover:text-accent-300">{{ $contact['email'] }}</a>
                    </p>
                    @foreach ($contact['telephones'] as $telephone)
                        <p>
                            <a href="tel:{{ str_replace(' ', '', $telephone) }}" class="transition hover:text-accent-300">{{ $telephone }}</a>
                        </p>
                    @endforeach
                </address>

                @if (! empty($reseaux))
                    <div class="mt-5 flex gap-3">
                        @foreach ($reseaux as $nom => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                               class="rounded-lg border border-white/20 px-3 py-1.5 text-xs capitalize transition hover:border-accent-400 hover:text-accent-300">
                                {{ $nom }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h2 class="font-display text-lg font-bold text-white">Liens utiles</h2>
                <ul class="mt-4 space-y-2 text-sm text-sand-300">
                    @foreach ($liensUtiles as $lien)
                        <li>
                            <a href="{{ $lien['url'] }}" target="_blank" rel="noopener"
                               class="transition hover:text-accent-300">{{ $lien['libelle'] }}</a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('accueil') }}#retrouver-dossier" class="transition hover:text-accent-300">
                            Payer ou récupérer un récépissé
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="transition hover:text-accent-300">Espace organisateur</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-xs text-sand-400">
            <p>&copy; {{ date('Y') }} CERFI. Tous droits réservés.</p>
        </div>
    </div>
</footer>
