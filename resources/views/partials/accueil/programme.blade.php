@php
    $jours = $programmes->groupBy(fn ($session) => $session->date?->toDateString());
@endphp

<x-site.section id="programme" fond="clair" surtitre="Déroulé" titre="Programme"
                :intro="$jours->isNotEmpty()
                    ? 'Le déroulé peut encore évoluer à la marge. Les inscrits sont prévenus par courriel de toute modification.'
                    : null">

    @if ($jours->isEmpty())
        <div class="rounded-2xl border border-dashed border-sand-300 bg-white/60 px-6 py-14 text-center">
            <p class="font-display text-xl font-bold text-brand-900">Programme en cours de finalisation</p>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-sand-600">
                Les panels, ateliers et temps forts seront publiés ici dès leur validation par le comité
                scientifique. Inscrivez-vous pour être prévenu en premier.
            </p>
        </div>
    @else
        <div x-data="{ jour: '{{ $jours->keys()->first() }}' }">

            {{-- Onglets par jour --}}
            <div class="flex flex-wrap gap-2" role="tablist" aria-label="Jours du programme">
                @foreach ($jours as $date => $sessions)
                    <button type="button"
                            role="tab"
                            @click="jour = '{{ $date }}'"
                            :aria-selected="jour === '{{ $date }}' ? 'true' : 'false'"
                            :class="jour === '{{ $date }}'
                                ? 'border-brand-600 bg-brand-600 text-white'
                                : 'border-sand-300 bg-white text-sand-700 hover:border-brand-400'"
                            class="rounded-lg border px-5 py-3 text-left transition">
                        <span class="block text-xs uppercase tracking-wide opacity-80">
                            {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l') }}
                        </span>
                        <span class="block text-sm font-semibold">
                            {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('j F') }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Sessions --}}
            @foreach ($jours as $date => $sessions)
                <div x-show="jour === '{{ $date }}'" x-cloak role="tabpanel" class="mt-8">
                    <ol class="relative border-l border-sand-200 pl-6 sm:pl-8">
                        @foreach ($sessions as $session)
                            <li class="relative pb-8 last:pb-0">
                                <span class="absolute -left-[31px] top-1.5 flex size-3.5 rounded-full border-2 border-white bg-accent-500 sm:-left-[39px]"
                                      aria-hidden="true"></span>

                                <div class="rounded-2xl border border-sand-200 bg-white p-5 shadow-sm sm:p-6">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <time class="font-mono text-sm font-semibold text-brand-700">
                                            {{ \Illuminate\Support\Str::of($session->heure_debut)->substr(0, 5) }}@if ($session->heure_fin) – {{ \Illuminate\Support\Str::of($session->heure_fin)->substr(0, 5) }}@endif
                                        </time>

                                        @if ($session->type)
                                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-700">
                                                {{ $session->type }}
                                            </span>
                                        @endif

                                        @if ($session->salle)
                                            <span class="text-xs text-sand-500">{{ $session->salle }}</span>
                                        @endif
                                    </div>

                                    <h3 class="mt-3 font-display text-xl font-bold text-brand-900">{{ $session->titre }}</h3>

                                    @if ($session->description)
                                        <p class="mt-2 text-sm leading-relaxed text-sand-600">{{ $session->description }}</p>
                                    @endif

                                    @if ($session->intervenants->isNotEmpty())
                                        <div class="mt-4 flex flex-wrap items-center gap-2">
                                            @foreach ($session->intervenants as $intervenant)
                                                <span class="inline-flex items-center gap-2 rounded-full bg-sand-100 py-1 pl-1 pr-3 text-xs text-sand-700">
                                                    @if ($intervenant->photo)
                                                        <img src="{{ Storage::url($intervenant->photo) }}" alt="" class="size-6 rounded-full object-cover">
                                                    @else
                                                        <span class="flex size-6 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">
                                                            {{ \Illuminate\Support\Str::of($intervenant->nom)->substr(0, 1)->upper() }}
                                                        </span>
                                                    @endif
                                                    {{ $intervenant->nom }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>
    @endif
</x-site.section>
