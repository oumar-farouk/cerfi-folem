<x-admin-layout title="Mon compte" :fil="['Mon compte' => null]">
    <div class="grid max-w-4xl grid-cols-1 gap-6">

        <x-admin.card titre="Informations personnelles"
                      description="Nom et adresse e-mail utilisés pour la connexion au back-office.">
            <livewire:profile.update-profile-information-form />
        </x-admin.card>

        <x-admin.card titre="Mot de passe"
                      description="Choisissez un mot de passe long et unique, différent de vos autres comptes.">
            <livewire:profile.update-password-form />
        </x-admin.card>

        <x-admin.card titre="Suppression du compte"
                      description="Cette action est irréversible : toutes les données personnelles du compte sont effacées.">
            <livewire:profile.delete-user-form />
        </x-admin.card>
    </div>
</x-admin-layout>
