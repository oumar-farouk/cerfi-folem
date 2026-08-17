<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // En-têtes de sécurité sur toutes les réponses du site.
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        /*
        | Le callback LigdiCash est une requête serveur à serveur : elle ne
        | porte pas de jeton CSRF. Le contrôleur revérifie systématiquement
        | le statut auprès de l'API avant de valider quoi que ce soit.
        */
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);

        // Laravel doit reconnaître le protocole d'origine derrière un proxy
        // (Nginx, Cloudflare), sinon les URLs de callback repassent en http.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
