@php
    $etapes = [
        1 => ['titre' => 'Qui êtes-vous ?', 'sous' => 'Identité et coordonnées'],
        2 => ['titre' => 'Votre participation', 'sous' => 'Région, profil et présence'],
        3 => ['titre' => 'Confirmation', 'sous' => 'Vérification et validation'],
    ];
@endphp

<div class="bg-sand-100">
    @if ($inscriptionTerminee)

        {{-- Écran de confirmation --}}
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24">
            <div class="overflow-hidden rounded-3xl border border-sand-200 bg-white shadow-sm">
                <div class="bg-brand-900 motif-mashrabiya px-8 py-10 text-center text-white">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-white/15">
                        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <h1 class="mt-5 font-display text-3xl font-bold">Inscription enregistrée</h1>
                    <p class="mt-2 text-sm text-sand-300">{{ $edition->nom }}</p>
                </div>

                <div class="p-8 sm:p-10">
                    <p class="text-center text-sm text-sand-600">Votre code d'inscription</p>

                    <div class="mt-4 flex flex-col items-center gap-3" x-data="copier(@js($codeGenere))">
                        <p class="rounded-xl border-2 border-dashed border-brand-300 bg-brand-25 px-8 py-5 font-mono text-3xl font-bold tracking-[0.2em] text-brand-800">
                            {{ $codeGenere }}
                        </p>

                        <button type="button" @click="copierTexte()"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-brand-700 transition hover:bg-brand-50">
                            <span x-show="! copie">Copier le code</span>
                            <span x-show="copie" class="text-success-600" style="display: none">Code copié</span>
                        </button>
                    </div>

                    <div class="mt-6 rounded-xl border border-warning-200 bg-warning-50 p-4">
                        <p class="text-sm leading-relaxed text-warning-800">
                            Notez ce code et gardez-le. Il est le seul moyen de reprendre votre dossier, de payer
                            plus tard ou de retélécharger votre récépissé.
                        </p>
                    </div>

                    <dl class="mt-7 divide-y divide-sand-200 border-y border-sand-200 text-sm">
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-sand-500">Participant</dt>
                            <dd class="text-right font-medium text-sand-900">{{ $prenom }} {{ $nom }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-sand-500">Montant à régler</dt>
                            <dd class="text-right font-display text-lg font-bold text-brand-800">
                                {{ number_format($montantRegle, 0, ',', ' ') }} FCFA
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-8 flex flex-col gap-3">
                        <x-site.btn :href="route('paiement.initier', $codeGenere)" taille="grand" class="w-full">
                            Payer maintenant par mobile money
                        </x-site.btn>
                        <x-site.btn :href="route('accueil')" variante="contour" class="w-full">
                            Payer plus tard, retour à l'accueil
                        </x-site.btn>
                    </div>
                </div>
            </div>
        </div>

    @else

        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-16">

            {{-- En-tête --}}
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-accent-500">Inscription</p>
                <h1 class="mt-3 font-display text-3xl font-bold text-brand-900 sm:text-4xl">{{ $edition->nom }}</h1>
                @if ($edition->theme)
                    <p class="mx-auto mt-3 max-w-xl text-sm italic text-sand-600">« {{ $edition->theme }} »</p>
                @endif
            </div>

            {{-- Progression --}}
            <nav class="mt-10" aria-label="Étapes de l'inscription">
                <ol class="flex items-center">
                    @foreach ($etapes as $numero => $infos)
                        <li @class(['flex items-center', 'flex-1' => ! $loop->last])>
                            <button type="button"
                                    wire:click="allerA({{ $numero }})"
                                    @disabled($numero >= $etape)
                                    @class([
                                        'flex items-center gap-2.5 text-left',
                                        'cursor-pointer' => $numero < $etape,
                                    ])
                                    @if ($numero === $etape) aria-current="step" @endif>
                                <span @class([
                                    'flex size-9 shrink-0 items-center justify-center rounded-full text-sm font-bold transition',
                                    'bg-brand-600 text-white' => $numero === $etape,
                                    'bg-brand-100 text-brand-700' => $numero < $etape,
                                    'bg-sand-200 text-sand-500' => $numero > $etape,
                                ])>
                                    @if ($numero < $etape)
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                            <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @else
                                        {{ $numero }}
                                    @endif
                                </span>
                                <span class="hidden sm:block">
                                    <span @class([
                                        'block text-sm font-semibold',
                                        'text-brand-900' => $numero <= $etape,
                                        'text-sand-400' => $numero > $etape,
                                    ])>{{ $infos['titre'] }}</span>
                                </span>
                            </button>

                            @if (! $loop->last)
                                <span @class([
                                    'mx-3 h-0.5 flex-1 rounded-full',
                                    'bg-brand-400' => $numero < $etape,
                                    'bg-sand-200' => $numero >= $etape,
                                ])></span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            <form wire:submit="submit" class="mt-8">

                {{-- Leurre anti-robot, masqué visuellement et pour les lecteurs d'écran --}}
                <div class="absolute h-0 w-0 overflow-hidden" aria-hidden="true">
                    <label for="site_web">Ne pas remplir ce champ</label>
                    <input type="text" id="site_web" wire:model="site_web" tabindex="-1" autocomplete="off">
                </div>

                <div class="rounded-3xl border border-sand-200 bg-white p-6 shadow-sm sm:p-9">

                    {{-- ÉTAPE 1 --}}
                    @if ($etape === 1)
                        <h2 class="font-display text-2xl font-bold text-brand-900">{{ $etapes[1]['titre'] }}</h2>
                        <p class="mt-1.5 text-sm text-sand-500">{{ $etapes[1]['sous'] }}</p>

                        <div class="mt-7 grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-site.champ label="Prénom" nom="prenom" requis>
                                <input type="text" wire:model.blur="prenom" id="champ-prenom" autocomplete="given-name"
                                       @class(['champ-form', 'champ-erreur' => $errors->has('prenom')])>
                            </x-site.champ>

                            <x-site.champ label="Nom" nom="nom" requis>
                                <input type="text" wire:model.blur="nom" id="champ-nom" autocomplete="family-name"
                                       @class(['champ-form', 'champ-erreur' => $errors->has('nom')])>
                            </x-site.champ>

                            <x-site.champ label="Adresse e-mail" nom="email" requis>
                                <input type="email" wire:model.blur="email" id="champ-email" autocomplete="email"
                                       @class(['champ-form', 'champ-erreur' => $errors->has('email')])>
                            </x-site.champ>

                            <x-site.champ label="Téléphone" nom="telephone" requis
                                          aide="Format 226XXXXXXXX, c'est le numéro qui recevra la demande de paiement.">
                                <input type="tel" wire:model.blur="telephone" id="champ-telephone" inputmode="numeric"
                                       placeholder="22670000000" maxlength="11" autocomplete="tel"
                                       @class(['champ-form', 'champ-erreur' => $errors->has('telephone')])>
                            </x-site.champ>

                            <x-site.champ label="Structure ou organisation" nom="structure">
                                <input type="text" wire:model.blur="structure" id="champ-structure" autocomplete="organization"
                                       @class(['champ-form', 'champ-erreur' => $errors->has('structure')])>
                            </x-site.champ>

                            <x-site.champ label="Fonction" nom="fonction">
                                <input type="text" wire:model.blur="fonction" id="champ-fonction" autocomplete="organization-title"
                                       @class(['champ-form', 'champ-erreur' => $errors->has('fonction')])>
                            </x-site.champ>
                        </div>

                        <div class="mt-7">
                            <fieldset>
                                <legend class="text-sm font-medium text-sand-700">
                                    Secteur d'activité <span class="text-error-500" aria-hidden="true">*</span>
                                </legend>

                                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @foreach (config('folem.secteurs') as $secteur => $description)
                                        <label @class([
                                            'flex cursor-pointer gap-3 rounded-xl border p-4 transition',
                                            'border-brand-500 bg-brand-25 ring-2 ring-brand-500/20' => $secteur_activite === $secteur,
                                            'border-sand-200 bg-white hover:border-brand-300' => $secteur_activite !== $secteur,
                                        ])>
                                            <input type="radio" wire:model.live="secteur_activite" value="{{ $secteur }}"
                                                   name="secteur_activite" class="mt-0.5 size-4 shrink-0 accent-brand-600">
                                            <span>
                                                <span class="block text-sm font-semibold text-sand-900">{{ $secteur }}</span>
                                                <span class="mt-0.5 block text-xs text-sand-500">{{ $description }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                @error('secteur_activite')
                                    <p class="mt-2 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>
                    @endif

                    {{-- ÉTAPE 2 --}}
                    @if ($etape === 2)
                        <h2 class="font-display text-2xl font-bold text-brand-900">{{ $etapes[2]['titre'] }}</h2>
                        <p class="mt-1.5 text-sm text-sand-500">{{ $etapes[2]['sous'] }}</p>

                        <div class="mt-7 grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-site.champ label="Région" nom="region_id" requis>
                                <select wire:model.live="region_id" id="champ-region_id"
                                        @class(['champ-form', 'champ-erreur' => $errors->has('region_id')])>
                                    <option value="">Choisir une région</option>
                                    @foreach ($this->regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->nom }}</option>
                                    @endforeach
                                </select>
                            </x-site.champ>

                            <x-site.champ label="Je participe en tant que" nom="profil_id" requis>
                                <select wire:model.live="profil_id" id="champ-profil_id"
                                        @class(['champ-form', 'champ-erreur' => $errors->has('profil_id')])>
                                    <option value="">Choisir un profil</option>
                                    @foreach ($this->profils as $profil)
                                        <option value="{{ $profil->id }}">{{ $profil->nom }}</option>
                                    @endforeach
                                </select>
                            </x-site.champ>
                        </div>

                        {{-- Tarif calculé en direct --}}
                        <div class="mt-6 rounded-2xl border border-sand-200 bg-sand-50 p-5">
                            @if ($this->tarifCalcule() !== null)
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-accent-500">Votre tarif</p>
                                <p class="mt-2 font-display text-4xl font-bold text-brand-800">
                                    {{ number_format($this->tarifCalcule(), 0, ',', ' ') }}
                                    <span class="text-xl font-semibold">FCFA</span>
                                </p>
                                <p class="mt-2 text-xs text-sand-500">
                                    Tarif appliqué à votre combinaison région et profil. Le paiement se fait après validation.
                                </p>
                            @elseif ($region_id && $profil_id)
                                <p class="text-sm text-error-600">
                                    Aucun tarif n'est proposé pour cette combinaison. Choisissez un autre profil ou
                                    contactez l'organisation.
                                </p>
                            @else
                                <p class="text-sm text-sand-500">
                                    Sélectionnez votre région et votre profil pour voir le montant de votre inscription.
                                </p>
                            @endif
                        </div>

                        {{-- Jours de participation --}}
                        @if (! empty($this->joursForum()))
                            <div class="mt-7">
                                <fieldset>
                                    <legend class="text-sm font-medium text-sand-700">
                                        Jours de participation <span class="text-error-500" aria-hidden="true">*</span>
                                    </legend>
                                    <p class="mt-1 text-xs text-sand-500">
                                        Cette information sert à dimensionner les salles et la restauration.
                                    </p>

                                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        @foreach ($this->joursForum() as $jour)
                                            <label @class([
                                                'flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition',
                                                'border-brand-500 bg-brand-25 ring-2 ring-brand-500/20' => in_array($jour['valeur'], $jours, true),
                                                'border-sand-200 bg-white hover:border-brand-300' => ! in_array($jour['valeur'], $jours, true),
                                            ])>
                                                <input type="checkbox" wire:model.live="jours" value="{{ $jour['valeur'] }}"
                                                       class="size-4 shrink-0 rounded accent-brand-600">
                                                <span>
                                                    <span class="block text-sm font-semibold capitalize text-sand-900">{{ $jour['jour'] }}</span>
                                                    <span class="block text-xs text-sand-500">{{ $jour['libelle'] }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('jours')
                                        <p class="mt-2 text-sm text-error-600">{{ $message }}</p>
                                    @enderror
                                </fieldset>
                            </div>
                        @endif

                        <div class="mt-7 grid grid-cols-1 gap-5">
                            <x-site.champ label="Besoins particuliers" nom="besoins_particuliers"
                                          aide="Restrictions alimentaires, accessibilité, autre point à signaler.">
                                <textarea wire:model.blur="besoins_particuliers" id="champ-besoins_particuliers" rows="3"
                                          maxlength="500"
                                          @class(['champ-form', 'champ-erreur' => $errors->has('besoins_particuliers')])></textarea>
                            </x-site.champ>

                            <x-site.champ label="Comment avez-vous connu le FOLEM ?" nom="source_connaissance">
                                <select wire:model.blur="source_connaissance" id="champ-source_connaissance"
                                        @class(['champ-form', 'champ-erreur' => $errors->has('source_connaissance')])>
                                    <option value="">Sans réponse</option>
                                    @foreach (config('folem.sources_connaissance') as $source)
                                        <option value="{{ $source }}">{{ $source }}</option>
                                    @endforeach
                                </select>
                            </x-site.champ>
                        </div>
                    @endif

                    {{-- ÉTAPE 3 --}}
                    @if ($etape === 3)
                        <h2 class="font-display text-2xl font-bold text-brand-900">{{ $etapes[3]['titre'] }}</h2>
                        <p class="mt-1.5 text-sm text-sand-500">{{ $etapes[3]['sous'] }}</p>

                        <dl class="mt-7 divide-y divide-sand-200 rounded-2xl border border-sand-200 bg-sand-50 px-5 text-sm">
                            @foreach ([
                                'Participant' => $prenom.' '.$nom,
                                'E-mail' => $email,
                                'Téléphone' => $telephone,
                                'Structure' => $structure ?: '—',
                                'Fonction' => $fonction ?: '—',
                                'Secteur' => $secteur_activite,
                                'Région' => $this->regions->firstWhere('id', $region_id)?->nom ?? '—',
                                'Profil' => $this->profils->firstWhere('id', $profil_id)?->nom ?? '—',
                                'Jours' => collect($jours)->map(fn ($j) => \Illuminate\Support\Carbon::parse($j)->translatedFormat('D j M'))->implode(', '),
                                'Besoins particuliers' => $besoins_particuliers ?: '—',
                                'Connu via' => $source_connaissance ?: '—',
                            ] as $libelle => $valeur)
                                <div class="flex flex-wrap justify-between gap-3 py-3">
                                    <dt class="text-sand-500">{{ $libelle }}</dt>
                                    <dd class="text-right font-medium text-sand-900">{{ $valeur }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="mt-6 rounded-2xl bg-brand-900 p-6 text-white">
                            <div class="flex flex-wrap items-baseline justify-between gap-3">
                                <p class="text-sm text-sand-300">Montant de votre inscription</p>
                                <p class="font-display text-3xl font-bold">
                                    {{ number_format($this->tarifCalcule() ?? 0, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-sand-400">
                                Aucun prélèvement n'est effectué à cette étape. Vous recevrez d'abord un code
                                d'inscription, puis vous choisirez votre moment pour payer.
                            </p>
                        </div>

                        <label class="mt-6 flex cursor-pointer gap-3 rounded-xl border border-sand-200 bg-white p-4">
                            <input type="checkbox" wire:model.live="accepte_conditions" class="mt-0.5 size-4 shrink-0 rounded accent-brand-600">
                            <span class="text-sm leading-relaxed text-sand-700">
                                Je confirme que les informations renseignées sont exactes et j'autorise le CERFI à
                                les utiliser pour l'organisation du forum.
                            </span>
                        </label>

                        @error('accepte_conditions')
                            <p class="mt-2 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    @endif

                    {{-- Navigation --}}
                    <div class="mt-9 flex flex-wrap items-center gap-3 border-t border-sand-200 pt-6">
                        @if ($etape > 1)
                            <x-site.btn type="button" variante="contour" wire:click="etapePrecedente">Retour</x-site.btn>
                        @endif

                        @if ($etape < 3)
                            <x-site.btn type="button" variante="vert" wire:click="etapeSuivante" class="ml-auto">
                                Continuer
                            </x-site.btn>
                        @else
                            <x-site.btn type="submit" class="ml-auto" wire:loading.attr="disabled" wire:target="submit">
                                <span wire:loading.remove wire:target="submit">Valider mon inscription</span>
                                <span wire:loading wire:target="submit">Enregistrement…</span>
                            </x-site.btn>
                        @endif
                    </div>
                </div>
            </form>

            <p class="mt-6 text-center text-xs text-sand-500">
                Une question ? Écrivez à
                <a href="mailto:{{ config('folem.contact.email') }}" class="font-medium text-brand-700 hover:underline">{{ config('folem.contact.email') }}</a>
            </p>
        </div>
    @endif
</div>
