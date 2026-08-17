<x-site.section id="infos" fond="clair" surtitre="Bon à savoir" titre="Infos pratiques">

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-1">
            @if ($edition)
                <div class="rounded-2xl border border-sand-200 bg-white p-6">
                    <h3 class="font-display text-lg font-bold text-brand-900">Dates</h3>
                    <p class="mt-2 text-sm leading-relaxed text-sand-600">
                        Du {{ $edition->date_debut?->translatedFormat('l j F Y') }}<br>
                        au {{ $edition->date_fin?->translatedFormat('l j F Y') }}
                    </p>
                </div>
            @endif

            <div class="rounded-2xl border border-sand-200 bg-white p-6">
                <h3 class="font-display text-lg font-bold text-brand-900">Lieu</h3>
                <p class="mt-2 text-sm leading-relaxed text-sand-600">
                    {{ $edition?->lieu ?: 'Ouagadougou, Burkina Faso' }}<br>
                    <span class="text-sand-500">{{ config('folem.contact.adresse') }}</span>
                </p>
            </div>

            <div class="rounded-2xl border border-sand-200 bg-white p-6">
                <h3 class="font-display text-lg font-bold text-brand-900">Contact</h3>
                <address class="mt-2 space-y-1 text-sm not-italic text-sand-600">
                    <p>
                        <a href="mailto:{{ config('folem.contact.email') }}" class="text-brand-700 hover:underline">
                            {{ config('folem.contact.email') }}
                        </a>
                    </p>
                    @foreach (config('folem.contact.telephones') as $telephone)
                        <p><a href="tel:{{ str_replace(' ', '', $telephone) }}" class="hover:text-brand-700">{{ $telephone }}</a></p>
                    @endforeach
                </address>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-sand-200 bg-white lg:col-span-2">
            <iframe src="{{ config('folem.contact.carte_url') }}"
                    title="Localisation du CERFI à Ouagadougou"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="h-full min-h-[22rem] w-full border-0"></iframe>
        </div>
    </div>
</x-site.section>
