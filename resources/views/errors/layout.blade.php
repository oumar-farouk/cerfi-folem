{{-- resources/views/errors/layout.blade.php
     Gabarit commun des pages d'erreur. Volontairement autonome : CSS en ligne,
     aucune dépendance à Vite, à Livewire, à la session ni à la base de données,
     pour rester affichable même quand l'application est en panne. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('titre') · FOLEM</title>
    <link rel="icon" href="{{ asset('img/brand/folem-icone.png') }}" type="image/png">

    <style>
        :root {
            --night: #0F3D2E; --night-2: #14503C; --ochre: #B8901E;
            --clay: #F05A28; --cream: #FBF6EC; --ink: #1B2E22;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; padding: 24px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, var(--night) 0%, var(--night-2) 60%, #1A6650 100%);
            font-family: 'Work Sans', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: var(--ink);
        }
        .carte {
            width: 100%; max-width: 520px; padding: 40px 32px; text-align: center;
            background: var(--cream); border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
        }
        .logo { width: 56px; height: 56px; margin-bottom: 16px; object-fit: contain; }
        .code {
            margin: 0; font-family: 'Amiri', Georgia, serif;
            font-size: 84px; line-height: 1; font-weight: 700; color: var(--ochre);
        }
        h1 { margin: 12px 0 8px; font-family: 'Amiri', Georgia, serif; font-size: 28px; color: var(--night); }
        .message { margin: 0 auto 28px; max-width: 400px; line-height: 1.6; color: rgba(27, 46, 34, .78); }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .btn {
            display: inline-block; padding: 13px 22px; border: 0; border-radius: 8px;
            font-family: inherit; font-size: 15px; font-weight: 600; text-decoration: none; cursor: pointer;
        }
        .btn-primaire { background: var(--clay); color: #fff; }
        .btn-secondaire { background: transparent; color: var(--night); border: 1.5px solid var(--night); }
        .btn:focus-visible { outline: 3px solid var(--ochre); outline-offset: 2px; }
        .pied { margin-top: 24px; font-size: 12px; color: rgba(27, 46, 34, .5); }
    </style>
</head>
<body>
    <main class="carte" role="main">
        <img class="logo" src="{{ asset('img/brand/folem-icone.png') }}" alt="">

        <p class="code">@yield('code')</p>
        <h1>@yield('titre')</h1>
        <p class="message">@yield('message')</p>

        <div class="actions">
            @hasSection('actions')
                @yield('actions')
            @else
                <a href="{{ url('/') }}" class="btn btn-primaire">Retour à l'accueil</a>
                <button type="button" class="btn btn-secondaire" onclick="history.back()">Page précédente</button>
            @endif
        </div>

        <p class="pied">FOLEM · CERFI</p>
    </main>
</body>
</html>
