<x-admin-layout title="Utilisateurs" :fil="['Paramétrage' => null, 'Utilisateurs' => null]">

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
            <x-admin.card titre="Ajouter un utilisateur"
                          description="Les utilisateurs peuvent se connecter et gérer leurs comptes.">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                    @csrf

                    <x-admin.field label="Nom" nom="name" requis>
                        <x-admin.input nom="name" :value="old('name')" required placeholder="Nom et prénom" />
                    </x-admin.field>

                    <x-admin.field label="Email" nom="email" requis>
                        <x-admin.input nom="email" :value="old('email')" required placeholder="Email" />
                    </x-admin.field>

                    <x-admin.field label="Mot de passe" nom="password" requis>
                        <x-admin.input type="password" nom="password" required placeholder="Mot de passe" />
                    </x-admin.field>

                    <x-admin.field label="Confirmer le mot de passe" nom="password_confirmation" requis>
                        <x-admin.input type="password" nom="password_confirmation" required placeholder="Confirmer le mot de passe" />
                    </x-admin.field>

                    <x-admin.button type="submit" class="w-full" icone="plus">Ajouter</x-admin.button>
                </form>
            </x-admin.card>
        </div>

        <div class="xl:col-span-2">
            <x-admin.card :padding="false" :titre="'Utilisateurs enregistrés ('.$users->count().')'">
                @if ($users->isEmpty())
                    <x-admin.empty icone="carte" titre="Aucun utilisateur" texte="Ajoutez au moins un utilisateur pour pouvoir les gérer." />
                @else
                    <x-admin.table :entetes="['Nom', 'Email','Mot de passe', 'Actions']">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" id="user-{{ $user->id }}">
                                    @csrf @method('PUT')
                                </form>

                                <td class="px-5 py-3">
                                    <label class="sr-only" for="user-nom-{{ $user->id }}">Nom de l'utilisateur</label>
                                    <input type="text" name="name" value="{{ $user->name }}" form="user-{{ $user->id }}"
                                           id="user-name-{{ $user->id }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3">
                                    <label class="sr-only" for="user-email-{{ $user->id }}">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}" form="user-{{ $user->id }}"
                                           id="user-email-{{ $user->id }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3">
                                    <label class="sr-only" for="user-password-{{ $user->id }}">Mot de passe</label>
                                    <input type="password" name="password" form="user-{{ $user->id }}"
                                           id="user-password-{{ $user->id }}" required
                                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="submit" form="user-{{ $user->id }}"
                                                class="rounded-lg px-2.5 py-1.5 text-theme-xs font-medium text-brand-700 hover:bg-brand-50 dark:text-brand-300 dark:hover:bg-brand-500/10">
                                            Enregistrer
                                        </button>
                                        <x-admin.confirm :action="route('admin.users.destroy', $user)"
                                                         :message="'Supprimer l\'utilisateur '.$user->nom.' ?'" />
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
