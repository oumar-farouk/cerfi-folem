<x-admin-layout :title="'Programme — '.$edition->nom"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Programme' => null]">

    <x-admin.card :padding="false"
                  titre="Sessions du programme"
                  description="Les sessions sont regroupées par jour et triées par heure sur le site public.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.editions.programmes.create', $edition)" icone="plus">Ajouter une session</x-admin.button>
        </x-slot:actions>

        @if ($programmes->isEmpty())
            <x-admin.empty icone="horloge"
                           titre="Programme vide"
                           texte="Ajoutez les sessions du forum : plénières, panels, ateliers, pauses et cérémonies.">
                <x-slot:action>
                    <x-admin.button :href="route('admin.editions.programmes.create', $edition)" icone="plus">Ajouter une session</x-admin.button>
                </x-slot:action>
            </x-admin.empty>
        @else
            @foreach ($programmes->groupBy(fn ($p) => $p->date?->toDateString()) as $jour => $sessions)
                <div class="border-b border-gray-100 last:border-0 dark:border-gray-800">
                    <div class="bg-gray-50 px-5 py-2.5 dark:bg-white/[0.02]">
                        <p class="text-theme-xs font-semibold uppercase tracking-wide text-gray-500">
                            {{ \Illuminate\Support\Carbon::parse($jour)->translatedFormat('l d F Y') }}
                        </p>
                    </div>

                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($sessions as $programme)
                            <li class="flex flex-wrap items-start gap-4 px-5 py-4">
                                <div class="w-24 shrink-0">
                                    <p class="text-theme-sm font-semibold text-brand-700 dark:text-brand-300">
                                        {{ \Illuminate\Support\Str::of($programme->heure_debut)->substr(0, 5) }}
                                    </p>
                                    @if ($programme->heure_fin)
                                        <p class="text-theme-xs text-gray-400">
                                            à {{ \Illuminate\Support\Str::of($programme->heure_fin)->substr(0, 5) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $programme->titre }}</p>
                                        @if ($programme->type)
                                            <x-admin.badge type="marque">{{ $programme->type }}</x-admin.badge>
                                        @endif
                                    </div>

                                    @if ($programme->salle)
                                        <p class="mt-0.5 text-theme-xs text-gray-500">{{ $programme->salle }}</p>
                                    @endif

                                    @if ($programme->intervenants->isNotEmpty())
                                        <p class="mt-1.5 text-theme-xs text-gray-500">
                                            {{ $programme->intervenants->pluck('nom')->implode(', ') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <x-admin.button variante="discret" taille="petit" icone="crayon"
                                                    :href="route('admin.programmes.edit', $programme)">Modifier</x-admin.button>
                                    <x-admin.confirm :action="route('admin.programmes.destroy', $programme)"
                                                     message="Supprimer cette session du programme ?" />
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @endif
    </x-admin.card>
</x-admin-layout>
