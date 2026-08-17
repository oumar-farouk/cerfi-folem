<x-admin-layout title="Profils" :fil="['Paramétrage' => null, 'Profils' => null]">

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
            <x-admin.card titre="Ajouter un profil"
                          description="Étudiant, jeune entrepreneur, professionnel, institution… Le profil détermine le tarif appliqué.">
                <form method="POST" action="{{ route('admin.profils.store') }}" class="space-y-5">
                    @csrf

                    <x-admin.field label="Nom" nom="nom" requis>
                        <x-admin.input nom="nom" :value="old('nom')" required placeholder="Étudiant" />
                    </x-admin.field>

                    <x-admin.field label="Ordre d'affichage" nom="ordre">
                        <x-admin.input type="number" nom="ordre" :value="old('ordre', 0)" />
                    </x-admin.field>

                    <x-admin.button type="submit" class="w-full" icone="plus">Ajouter</x-admin.button>
                </form>
            </x-admin.card>
        </div>

        <div class="xl:col-span-2">
            <x-admin.card :padding="false" :titre="'Profils enregistrés ('.$profils->count().')'">
                @if ($profils->isEmpty())
                    <x-admin.empty icone="badge" titre="Aucun profil" texte="Ajoutez au moins un profil pour pouvoir définir des tarifs." />
                @else
                    <x-admin.table :entetes="['Nom', 'Ordre', 'Inscriptions', '']">
                        @foreach ($profils as $profil)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <form method="POST" action="{{ route('admin.profils.update', $profil) }}" id="profil-{{ $profil->id }}">
                                    @csrf @method('PUT')
                                </form>

                                <td class="px-5 py-3">
                                    <label class="sr-only" for="profil-nom-{{ $profil->id }}">Nom du profil</label>
                                    <input type="text" name="nom" value="{{ $profil->nom }}" form="profil-{{ $profil->id }}"
                                           id="profil-nom-{{ $profil->id }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3">
                                    <label class="sr-only" for="profil-ordre-{{ $profil->id }}">Ordre</label>
                                    <input type="number" name="ordre" value="{{ $profil->ordre }}" form="profil-{{ $profil->id }}"
                                           id="profil-ordre-{{ $profil->id }}"
                                           class="w-20 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3 text-theme-sm text-gray-500">{{ $profil->inscriptions_count }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="submit" form="profil-{{ $profil->id }}"
                                                class="rounded-lg px-2.5 py-1.5 text-theme-xs font-medium text-brand-700 hover:bg-brand-50 dark:text-brand-300 dark:hover:bg-brand-500/10">
                                            Enregistrer
                                        </button>
                                        <x-admin.confirm :action="route('admin.profils.destroy', $profil)"
                                                         :message="'Supprimer le profil '.$profil->nom.' ?'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-admin.table>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
