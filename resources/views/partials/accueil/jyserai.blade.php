@if ($edition)
    <x-site.section id="jyserai" fond="blanc">
        <div class="overflow-hidden rounded-3xl bg-brand-900 motif-mashrabiya text-sand-100">
            <div class="grid grid-cols-1 items-center gap-8 p-8 sm:p-10 lg:grid-cols-[1fr_auto] lg:gap-14 lg:p-12">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-accent-300">Partagez votre venue</p>
                    <h2 class="mt-3 font-display text-3xl font-bold leading-tight text-white sm:text-4xl">
                        Dites-le à vos contacts : « J'y serai »
                    </h2>
                    <p class="mt-5 max-w-xl leading-relaxed text-sand-300">
                        Ajoutez votre photo et votre nom pour créer votre affiche personnalisée, puis partagez-la
                        sur WhatsApp ou vos réseaux. Cela prend moins d'une minute, et votre photo reste sur
                        votre appareil.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <x-site.btn :href="route('jyserai', $edition)" taille="grand">Créer mon affiche</x-site.btn>
                    </div>
                </div>

                {{-- Aperçu décoratif : un cadre en arche, comme sur l'affiche --}}
                <div class="hidden justify-self-center lg:block" aria-hidden="true">
                    <div class="flex h-64 w-48 flex-col items-center justify-end rounded-t-full border-4 border-accent-400 bg-white/10 pb-6">
                        <span class="font-display text-2xl font-bold tracking-wide text-accent-300">J'Y SERAI</span>
                        <span class="mt-1 text-xs uppercase tracking-[0.18em] text-sand-300">{{ $edition->nom }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-site.section>
@endif
