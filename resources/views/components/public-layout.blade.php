@props([
    'title' => null,
    'description' => null,
    'edition' => null,
    'ancres' => true,
])

@php
    $titrePage = $title ? $title.' · FOLEM' : 'FOLEM · Forum du Leadership et de l\'Entrepreneuriat Musulmans';
    $meta = $description
        ?? "Le Forum du Leadership et de l'Entrepreneuriat Musulmans réunit à Ouagadougou les entrepreneurs et cadres musulmans du Burkina Faso, à l'initiative du CERFI.";
@endphp

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $titrePage }}</title>
    <meta name="description" content="{{ $meta }}">

    <meta property="og:title" content="{{ $titrePage }}">
    <meta property="og:description" content="{{ $meta }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('img/brand/folem-logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('img/brand/folem-icone.png') }}" type="image/png">
    <link rel="canonical" href="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-sand-50 font-sans text-sand-900 antialiased">

    <a href="#contenu"
       class="sr-only rounded-lg bg-brand-600 px-4 py-2 text-white focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-99999">
        Aller au contenu principal
    </a>

    @include('partials.navigation', ['ancres' => $ancres, 'edition' => $edition])

    <main id="contenu">
        {{ $slot }}
    </main>

    @include('partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
