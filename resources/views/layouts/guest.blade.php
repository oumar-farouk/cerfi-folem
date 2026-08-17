<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>Espace organisateur · FOLEM</title>
    <link rel="icon" href="{{ asset('img/brand/folem-icone.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-sand-100 font-sans text-sand-900 antialiased">

    <div class="flex min-h-screen flex-col lg:flex-row">

        {{-- Colonne de marque, masquée sur mobile pour laisser la place au formulaire --}}
        <div class="relative hidden overflow-hidden bg-brand-950 lg:flex lg:w-1/2 lg:flex-col lg:justify-between">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800" aria-hidden="true"></div>
            <div class="absolute inset-0 motif-mashrabiya opacity-70" aria-hidden="true"></div>

            <div class="relative p-12">
                <a href="{{ route('accueil') }}">
                    <img src="{{ asset('img/brand/folem-logo-blanc.png') }}" alt="FOLEM" class="h-14 w-auto">
                </a>
            </div>

            <div class="relative p-12">
                <p class="font-display text-3xl font-bold leading-snug text-white">
                    Espace réservé à l'organisation du forum
                </p>
                <p class="mt-4 max-w-md leading-relaxed text-sand-300">
                    Gestion des éditions, du programme, des intervenants, des tarifs et du suivi des inscriptions.
                </p>

                <div class="mt-10 flex items-center gap-3">
                    <img src="{{ asset('img/brand/cerfi-logo.png') }}" alt="" class="h-12 w-auto rounded-lg bg-white/95 p-1.5">
                    <p class="text-xs text-sand-400">Cercle d'Études, de Recherches et de Formation Islamiques</p>
                </div>
            </div>
        </div>

        {{-- Colonne formulaire --}}
        <div class="flex flex-1 items-center justify-center px-4 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <a href="{{ route('accueil') }}" class="mb-8 flex justify-center lg:hidden">
                    <img src="{{ asset('img/brand/folem-logo.png') }}" alt="FOLEM" class="h-12 w-auto">
                </a>

                <div class="rounded-2xl border border-sand-200 bg-white p-8 shadow-sm">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-sand-500">
                    <a href="{{ route('accueil') }}" class="hover:text-brand-700">Retour au site public</a>
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
