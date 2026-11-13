<x-admin-layout title="Journal d'activité" :fil="['Journal d\'activité' => null]">

    @unless ($installe)
        <x-admin.card>
            <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">Le journal n'est pas encore installé.</p>
            <p class="mt-1 text-theme-xs text-gray-500">Installez le paquet puis lancez la migration :</p>
            <pre class="mt-3 overflow-x-auto rounded-lg bg-gray-50 p-3 text-theme-xs text-gray-700 dark:bg-white/[0.03] dark:text-gray-300">composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate</pre>
        </x-admin.card>
    @else

        {{-- Filtres --}}
        <form method="GET" class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <x-admin.field label="Utilisateur" nom="utilisateur">
                    <x-admin.select nom="utilisateur" onchange="this.form.submit()">
                        <option value="">Tous les utilisateurs</option>
                        @foreach ($utilisateurs as $u)
                            <option value="{{ $u->id }}" @selected((int) request('utilisateur') === $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </x-admin.select>
                </x-admin.field>

                <x-admin.field label="Du" nom="du">
                    <x-admin.input nom="du" type="date" :value="request('du')" />
                </x-admin.field>

                <x-admin.field label="Au" nom="au">
                    <x-admin.input nom="au" type="date" :value="request('au')" />
                </x-admin.field>

                <x-admin.field label="Recherche" nom="q">
                    <div class="flex gap-2">
                        <x-admin.input nom="q" :value="request('q')" placeholder="Action" />
                        <x-admin.button type="submit" variante="secondaire" icone="recherche">Filtrer</x-admin.button>
                    </div>
                </x-admin.field>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <p class="text-theme-xs text-gray-500">
                    {{ $activites->total() }} événement(s) correspondent aux filtres.
                </p>
                @if (request()->hasAny(['utilisateur', 'du', 'au', 'q']))
                    <x-admin.button variante="discret" taille="petit" :href="route('admin.journal.index')">
                        Réinitialiser
                    </x-admin.button>
                @endif
            </div>
        </form>

        <x-admin.card :padding="false">
            @if ($activites->isEmpty())
                <x-admin.empty titre="Aucune activité"
                               texte="Aucun événement ne correspond aux filtres sélectionnés." />
            @else
                <x-admin.table :entetes="['Date', 'Utilisateur', 'Action', 'Objet', 'Détails']">
                    @foreach ($activites as $activite)
                        <tr class="align-top hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-4 text-theme-xs text-gray-400">
                                {{ $activite->created_at->format('d/m/Y') }}<br>{{ $activite->created_at->format('H:i') }}
                            </td>
                            <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">
                                {{ $activite->causer?->name ?? 'Système' }}
                            </td>
                            <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $activite->description }}
                            </td>
                            <td class="px-5 py-4 text-theme-xs text-gray-500">
                                @if ($activite->subject_type)
                                    {{ class_basename($activite->subject_type) }}
                                    <span class="font-mono text-gray-400">#{{ $activite->subject_id }}</span>
                                    @unless ($activite->subject)
                                        <span class="mt-1 block">
                                            <x-admin.badge type="erreur">Supprimé</x-admin.badge>
                                        </span>
                                    @endunless
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-4 text-theme-xs text-gray-500">
                                @forelse ($activite->properties as $cle => $valeur)
                                    <span class="block">
                                        <span class="text-gray-400">{{ $cle }} :</span>
                                        {{ is_scalar($valeur) ? $valeur : json_encode($valeur, JSON_UNESCAPED_UNICODE) }}
                                    </span>
                                @empty
                                    —
                                @endforelse
                            </td>
                        </tr>
                    @endforeach
                </x-admin.table>

                <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">
                    {{ $activites->withQueryString()->links() }}
                </div>
            @endif
        </x-admin.card>
    @endunless
</x-admin-layout>
