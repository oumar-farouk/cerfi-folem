<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Dates et durées affichées en français sur tout le site.
        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

        // Derrière un proxy en production, on force la génération d'URL en https
        // pour que les callbacks LigdiCash et les assets ne repassent pas en http.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
