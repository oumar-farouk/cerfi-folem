<x-site.section id="retrouver-dossier" fond="creme">
    <div class="mx-auto max-w-3xl rounded-2xl border border-sand-200 bg-white p-8 shadow-sm sm:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-accent-500">Déjà inscrit</p>
        <h2 class="mt-3 font-display text-2xl font-bold text-brand-900 sm:text-3xl">
            Payer ou télécharger votre récépissé
        </h2>
        <p class="mt-4 text-sm leading-relaxed text-sand-600">
            Saisissez le code reçu à la fin de votre inscription. S'il reste à payer, vous êtes redirigé vers
            le paiement mobile money. Si le paiement est déjà confirmé, le récépissé se télécharge directement.
        </p>

        <form method="POST" action="{{ route('inscription.rechercher') }}" class="mt-7">
            @csrf

            <label for="code-inscription" class="block text-sm font-medium text-sand-700">
                Code d'inscription
            </label>

            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input type="text"
                       id="code-inscription"
                       name="code"
                       value="{{ old('code') }}"
                       required
                       autocomplete="off"
                       spellcheck="false"
                       maxlength="20"
                       placeholder="FLM-7K3P9Q"
                       @class([
                           'w-full rounded-lg border bg-white px-4 py-3.5 font-mono text-base uppercase tracking-wider text-sand-900 placeholder:normal-case placeholder:tracking-normal placeholder:text-sand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/20',
                           'border-error-400' => $errors->has('code'),
                           'border-sand-300 focus:border-brand-500' => ! $errors->has('code'),
                       ])
                       @if ($errors->has('code')) aria-invalid="true" aria-describedby="erreur-code" @endif>

                <x-site.btn type="submit" variante="vert" class="shrink-0 sm:w-auto">Valider</x-site.btn>
            </div>

            @error('code')
                <p id="erreur-code" class="mt-2 text-sm text-error-600">{{ $message }}</p>
            @enderror
        </form>

        <p class="mt-6 text-xs text-sand-500">
            Code perdu ? Écrivez à
            <a href="mailto:{{ config('folem.contact.email') }}" class="font-medium text-brand-700 hover:underline">{{ config('folem.contact.email') }}</a>
            en précisant vos nom, prénom et numéro de téléphone.
        </p>
    </div>
</x-site.section>
