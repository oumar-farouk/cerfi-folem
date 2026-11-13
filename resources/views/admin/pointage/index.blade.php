<x-admin-layout>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div class="mx-auto max-w-3xl space-y-6" x-data="pointage()">

        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Pointage</h1>
            <p class="text-sm text-gray-500">
                {{ $edition?->nom ?? 'Aucune édition active' }} · {{ $jour->translatedFormat('l d F Y') }}
            </p>
        </div>

        {{-- Compteurs du jour --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-theme-xs uppercase tracking-wide text-gray-400">Pointés</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white" x-text="pointes"></p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-theme-xs uppercase tracking-wide text-gray-400">Attendus aujourd'hui</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white" x-text="attendus"></p>
            </div>
        </div>

        {{-- Résultat du dernier scan : un seul état visuel dominant --}}
        <div x-show="resultat" x-cloak x-transition
             class="rounded-2xl p-6 text-center text-white"
             :class="{
                'bg-green-600': resultat?.etat === 'ok',
                'bg-amber-500': resultat?.etat === 'deja_pointe',
                'bg-red-600': resultat && ! ['ok', 'deja_pointe'].includes(resultat.etat),
             }"
             role="status" aria-live="polite">
            <p class="text-2xl font-bold" x-text="resultat?.message"></p>
            <template x-if="resultat?.participant">
                <div class="mt-3">
                    <p class="text-xl font-semibold" x-text="resultat.participant"></p>
                    <p class="text-sm opacity-90" x-text="resultat.structure"></p>
                    <p class="mt-1 text-sm opacity-90" x-text="resultat.code + ' · ' + resultat.jours"></p>
                </div>
            </template>
            <template x-if="resultat && ! resultat.participant && resultat.code">
                <p class="mt-2 text-sm opacity-90" x-text="resultat.code"></p>
            </template>
        </div>

        {{-- Scanner caméra --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div id="lecteur" class="mx-auto w-full max-w-sm overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800"></div>
            <div class="mt-4 flex justify-center">
                <button type="button" x-show="! actif" @click="demarrer()"
                        class="rounded-lg bg-brand-500 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-600">
                    Activer la caméra
                </button>
                <button type="button" x-show="actif" x-cloak @click="arreter()"
                        class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">
                    Arrêter la caméra
                </button>
            </div>
            <p class="mt-3 text-center text-theme-xs text-gray-400">
                La caméra exige une connexion sécurisée (HTTPS) et votre autorisation.
            </p>
        </div>

        {{-- Saisie manuelle --}}
        <form class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
              @submit.prevent="envoyer(saisie, 'manuel')">
            <label for="code-manuel" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Saisir le code d'inscription
            </label>
            <div class="flex gap-3">
                <input id="code-manuel" type="text" x-model="saisie" placeholder="FLM-XXXXXX"
                       autocomplete="off" autocapitalize="characters"
                       class="w-full rounded-lg border border-gray-300 px-4 py-3 uppercase tracking-widest dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <button type="submit" :disabled="enCours"
                        class="shrink-0 rounded-lg bg-gray-900 px-5 py-3 text-sm font-semibold text-white disabled:opacity-50 dark:bg-white dark:text-gray-900">
                    Valider
                </button>
            </div>
        </form>

        {{-- Derniers pointages --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <h2 class="border-b border-gray-200 px-4 py-3 text-sm font-semibold text-gray-900 dark:border-gray-800 dark:text-white">
                Derniers pointages
            </h2>
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                <template x-for="ligne in recents" :key="ligne.code">
                    <li class="flex items-center justify-between px-4 py-3 text-sm">
                        <span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="ligne.nom"></span>
                            <span class="ml-2 text-gray-400" x-text="ligne.code"></span>
                        </span>
                        <span class="text-gray-500">
                            <span x-text="ligne.heure"></span>
                            <span x-show="ligne.methode === 'manuel'" class="ml-1 text-theme-xs">(manuel)</span>
                        </span>
                    </li>
                </template>
                <li x-show="recents.length === 0" class="px-4 py-6 text-center text-sm text-gray-400">
                    Aucun pointage pour le moment.
                </li>
            </ul>
        </div>
    </div>

    <script>
        function pointage() {
            // Hors de l'objet réactif : Alpine l'encapsulerait dans un proxy
            // et la bibliothèque de scan ne fonctionnerait plus correctement.
            let lecteur = null;

            return {
                actif: false, verrou: false, enCours: false, saisie: '', resultat: null,
                recents: @js($recents),
                pointes: @js($pointes),
                attendus: @js($attendus),

                async demarrer() {
                    try {
                        lecteur = new Html5Qrcode('lecteur');
                        await lecteur.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 240, height: 240 } },
                            (texte) => this.envoyer(texte, 'scan'),
                        );
                        this.actif = true;
                    } catch (e) {
                        this.resultat = {
                            etat: 'erreur',
                            message: "Caméra indisponible. Vérifiez l'autorisation et le HTTPS, ou saisissez le code.",
                        };
                    }
                },

                async arreter() {
                    try { if (lecteur) await lecteur.stop(); } catch (e) {}
                    this.actif = false;
                },

                async envoyer(valeur, methode) {
                    valeur = (valeur || '').trim();
                    // Le verrou évite que la caméra renvoie le même QR code en boucle.
                    if (! valeur || this.verrou) return;
                    this.verrou = true;
                    this.enCours = true;

                    try {
                        const reponse = await fetch(@js(route('admin.pointage.store')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                            },
                            body: JSON.stringify({ code: valeur, methode }),
                        });
                        const data = await reponse.json();
                        this.resultat = data.etat ? data : { etat: 'erreur', message: data.message || 'Erreur inattendue.' };

                        if (data.ok && data.ligne) {
                            this.recents.unshift(data.ligne);
                            this.recents = this.recents.slice(0, 15);
                            this.pointes++;
                        }
                        if (methode === 'manuel' && data.ok) this.saisie = '';
                        if (navigator.vibrate) navigator.vibrate(data.ok ? 100 : [200, 100, 200]);
                    } catch (e) {
                        this.resultat = { etat: 'erreur', message: 'Connexion impossible. Réessayez.' };
                    } finally {
                        this.enCours = false;
                        setTimeout(() => { this.verrou = false; }, 2000);
                    }
                },
            };
        }
    </script>
</x-admin-layout>
