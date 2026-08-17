<x-admin-layout :title="'Intervenants — '.$edition->nom"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Intervenants' => null]">

    <x-admin.card :padding="false"
                  titre="Intervenants de l'édition"
                  description="Les intervenants apparaissent sur la page d'accueil et peuvent être rattachés aux sessions du programme.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.editions.intervenants.create', $edition)" icone="plus">Ajouter</x-admin.button>
        </x-slot:actions>

        @if ($intervenants->isEmpty())
            <x-admin.empty icone="utilisateurs"
                           titre="Aucun intervenant"
                           texte="Renseignez les personnalités qui prendront la parole pendant le forum.">
                <x-slot:action>
                    <x-admin.button :href="route('admin.editions.intervenants.create', $edition)" icone="plus">Ajouter un intervenant</x-admin.button>
                </x-slot:action>
            </x-admin.empty>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($intervenants as $intervenant)
                    <li class="flex flex-wrap items-center gap-4 px-5 py-4">
                        @if ($intervenant->photo)
                            <img src="{{ Storage::url($intervenant->photo) }}" alt=""
                                 class="size-12 shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-brand-50 text-theme-sm font-bold text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                                {{ \Illuminate\Support\Str::of($intervenant->nom)->substr(0, 2)->upper() }}
                            </span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $intervenant->nom }}</p>
                            <p class="text-theme-xs text-gray-500">
                                {{ $intervenant->fonction }}@if ($intervenant->fonction && $intervenant->structure) &middot; @endif{{ $intervenant->structure }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <x-admin.button variante="discret" taille="petit" icone="crayon"
                                            :href="route('admin.intervenants.edit', $intervenant)">Modifier</x-admin.button>
                            <x-admin.confirm :action="route('admin.intervenants.destroy', $intervenant)"
                                             :message="'Supprimer '.$intervenant->nom.' ?'" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-admin.card>
</x-admin-layout>
