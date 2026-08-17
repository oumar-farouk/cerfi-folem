<x-admin-layout :title="'Partenaires — '.$edition->nom"
                :fil="['Éditions' => route('admin.editions.index'), $edition->nom => route('admin.editions.edit', $edition), 'Partenaires' => null]">

    <x-admin.card :padding="false"
                  titre="Partenaires et sponsors"
                  description="L'ordre d'affichage sur le site public suit le champ « ordre », du plus petit au plus grand.">
        <x-slot:actions>
            <x-admin.button :href="route('admin.editions.partenaires.create', $edition)" icone="plus">Ajouter</x-admin.button>
        </x-slot:actions>

        @if ($partenaires->isEmpty())
            <x-admin.empty icone="poignee"
                           titre="Aucun partenaire"
                           texte="Ajoutez les structures qui soutiennent cette édition du forum.">
                <x-slot:action>
                    <x-admin.button :href="route('admin.editions.partenaires.create', $edition)" icone="plus">Ajouter un partenaire</x-admin.button>
                </x-slot:action>
            </x-admin.empty>
        @else
            <x-admin.table :entetes="['Logo', 'Nom', 'Type', 'Niveau', 'Ordre', '']">
                @foreach ($partenaires as $partenaire)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-5 py-4">
                            <img src="{{ Storage::url($partenaire->logo) }}" alt="" class="h-10 w-24 object-contain">
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $partenaire->nom }}</p>
                            @if ($partenaire->lien_site)
                                <a href="{{ $partenaire->lien_site }}" target="_blank" rel="noopener nofollow"
                                   class="text-theme-xs text-brand-700 hover:underline dark:text-brand-300">{{ $partenaire->lien_site }}</a>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <x-admin.badge :type="$partenaire->type === 'sponsor' ? 'marque' : 'neutre'">
                                {{ ucfirst($partenaire->type) }}
                            </x-admin.badge>
                        </td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $partenaire->niveau ?: '—' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $partenaire->ordre ?? 0 }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <x-admin.button variante="discret" taille="petit" icone="crayon"
                                                :href="route('admin.partenaires.edit', $partenaire)">Modifier</x-admin.button>
                                <x-admin.confirm :action="route('admin.partenaires.destroy', $partenaire)"
                                                 :message="'Retirer '.$partenaire->nom.' des partenaires ?'" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin-layout>
