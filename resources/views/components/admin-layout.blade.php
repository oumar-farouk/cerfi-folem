@props([
    'title' => 'Administration',
    'fil' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title }} · Administration FOLEM</title>

    <link rel="icon" href="{{ asset('img/brand/folem-icone.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Applique le thème avant le premier rendu pour éviter le flash de fond clair. --}}
    <script>
        (function () {
            const enregistre = localStorage.getItem('theme');
            const systeme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            if ((enregistre || systeme) === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 dark:bg-gray-900 dark:text-gray-200"
      x-data
      x-init="
        $store.theme.init();
        const surveille = () => {
            if (window.innerWidth < 1280) {
                $store.sidebar.setMobileOpen(false);
                $store.sidebar.isExpanded = false;
            } else {
                $store.sidebar.isMobileOpen = false;
                $store.sidebar.isExpanded = true;
            }
        };
        window.addEventListener('resize', surveille);
      ">

    <a href="#contenu-admin"
       class="sr-only rounded-lg bg-brand-600 px-4 py-2 text-white focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-99999">
        Aller au contenu
    </a>

    <div class="min-h-screen xl:flex">

        {{-- Voile d'arrière-plan du tiroir mobile --}}
        <div x-show="$store.sidebar.isMobileOpen"
             @click="$store.sidebar.setMobileOpen(false)"
             x-transition.opacity
             class="fixed inset-0 z-9999 bg-gray-900/50 xl:hidden"
             aria-hidden="true"
             style="display: none"></div>

        @include('admin.partials.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
             :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
             }">

            @include('admin.partials.header', ['titrePage' => $title])

            <main id="contenu-admin" class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

                <x-admin.breadcrumb :title="$title" :fil="$fil" />

                @if (session('success'))
                    <div class="mb-6"><x-admin.alert type="success">{{ session('success') }}</x-admin.alert></div>
                @endif

                @if (session('info'))
                    <div class="mb-6"><x-admin.alert type="info">{{ session('info') }}</x-admin.alert></div>
                @endif

                @if (session('error'))
                    <div class="mb-6"><x-admin.alert type="error">{{ session('error') }}</x-admin.alert></div>
                @endif

                @if ($errors->any())
                    <div class="mb-6">
                        <x-admin.alert type="error" titre="Le formulaire contient des erreurs">
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-admin.alert>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
