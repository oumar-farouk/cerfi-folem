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

                    <x-admin.field label="Profil" nom="role" requis>
                        <select name="role" id="role" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">— Choisir un profil —</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
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
                    <x-admin.table :entetes="['Nom', 'Email','Profil', 'Mot de passe', 'Actions']">
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
                                    <label class="sr-only" for="user-role-{{ $user->id }}">Profil</label>
                                    <select name="role" form="user-{{ $user->id }}" id="user-role-{{ $user->id }}" required
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-3">
                                    <label class="sr-only" for="user-password-{{ $user->id }}">Mot de passe</label>
                                    <input type="password" name="password" form="user-{{ $user->id }}"
                                           id="user-password-{{ $user->id }}"
                                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm text-gray-800 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                </td>
                              <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- Enregistrer (icône crayon) --}}
                                        <button type="submit" form="user-{{ $user->id }}"
                                                title="Enregistrer"
                                                aria-label="Enregistrer les modifications de {{ $user->name }}"
                                                class="inline-flex items-center justify-center rounded-lg p-2 text-brand-700 hover:bg-brand-50 dark:text-brand-300 dark:hover:bg-brand-500/10">
                                            <span class="sr-only">Enregistrer</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 7.125L16.875 4.5" />
                                            </svg>
                                        </button>

                                        {{-- Supprimer (icône corbeille) --}}
                                        <x-admin.confirm :action="route('admin.users.destroy', $user)"
                                                        :message="'Supprimer l\'utilisateur '.$user->name.' ?'"
                                                        icone="trash" />
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
