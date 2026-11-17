<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Site;
use App\Http\Controllers\WebhookController;
use App\Livewire\Registration\RegisterForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// routes/web.php — à ajouter, idéalement retirée avant la mise en production
// Route::get('/test-notification/{code}', function (string $code) {
//     $inscription = \App\Models\Inscription::with('participant')
//         ->where('code_inscription', $code)
//         ->firstOrFail();
//     $inscription->participant->notify(new \App\Notifications\InscriptionEnregistree($inscription));
//     $inscription->participant->notify(new \App\Notifications\PaiementRecu($inscription->fresh()));
//     return "Notification envoyée à {$inscription->participant->email} / {$inscription->participant->telephone} pour le code {$code}.";
// }); // évite qu'un inconnu spamme des SMS via cette URL

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
*/

Route::get('/', Site\AccueilController::class)->name('accueil');

Route::get('/editions/{edition}', [Site\EditionController::class, 'show'])->name('editions.show');

Route::get('/editions/{edition}/inscription', RegisterForm::class)->name('inscription.form');

// Générateur d'affiche « J'y serai » (traitement 100 % navigateur, rien n'est stocké)
Route::get('/editions/{edition}/jyserai', Site\JySeraiController::class)->name('jyserai');

/*
|--------------------------------------------------------------------------
| Parcours « code d'inscription » : payer, retrouver son récépissé
|--------------------------------------------------------------------------
| Ces routes exposent un dossier à partir d'un simple code : elles sont donc
| limitées en fréquence pour empêcher l'énumération des codes existants.
*/

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/mon-inscription', [ReceiptController::class, 'rechercher'])->name('inscription.rechercher');
    Route::get('/payer/{code}', [PaymentController::class, 'initier'])->name('paiement.initier');
    Route::get('/recu/{code}', [ReceiptController::class, 'telecharger'])->name('recu.telecharger');
});

Route::get('/paiement/succes', [PaymentController::class, 'succes'])->name('paiement.succes');
Route::get('/paiement/annule', [PaymentController::class, 'annule'])->name('paiement.annule');
// Vérification du récépissé via scan du QR code (contrôle d'accès jour J)
Route::get('/verification/{code}/{hash}', [\App\Http\Controllers\VerificationController::class, 'show'])->name('verification.show');

/*
|--------------------------------------------------------------------------
| Webhooks (exclus de la vérification CSRF, voir bootstrap/app.php)
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/ligdicash', [WebhookController::class, 'ligdicash'])
    ->middleware('throttle:60,1')
    ->name('webhooks.ligdicash');

/*
|--------------------------------------------------------------------------
| Redirection du lien « Dashboard » hérité de Breeze
|--------------------------------------------------------------------------
| Chaque profil arrive sur son écran de travail :
|  - équipe de pilotage      -> tableau de bord
|  - agent d'accueil (jour J) -> écran de pointage
|  - autre                    -> site public
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasAnyRole(['super-admin', 'gestionnaire', 'moderateur', 'comptable'])) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('agent-accueil')) {
        return redirect()->route('admin.pointage.index');
    }

    return redirect()->route('accueil');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Compte de l'utilisateur connecté
|--------------------------------------------------------------------------
| Écran fourni par Breeze (nom, e-mail, mot de passe). La route n'était pas
| déclarée dans la version précédente alors que la vue existait.
*/

Route::view('/profile', 'profile')->middleware(['auth'])->name('profile');

/*
|--------------------------------------------------------------------------
| Déconnexion
|--------------------------------------------------------------------------
| Le layout admin utilise un formulaire POST classique plutôt que l'action
| Livewire de Breeze, d'où cette route dédiée.
*/

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('accueil');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Back-office
|--------------------------------------------------------------------------
| Matrice des profils (le super-admin figure dans chaque groupe : le
| middleware `role:` de Spatie ne lui accorde aucun passe-droit implicite).
|
|  Profil          Périmètre
|  ---------------------------------------------------------------------
|  super-admin     Tout, y compris utilisateurs et journal d'activité
|  gestionnaire    Organisation : éditions, programme, intervenants,
|                  partenaires, galerie, tarifs, régions, profils,
|                  inscriptions, paiements, pointage
|  moderateur      Suivi des participants : inscriptions (consultation,
|                  renvoi de récépissé), pointage
|  comptable       Finance : paiements, tarifs (lecture), exports,
|                  inscriptions (consultation)
|  agent-accueil   Jour J uniquement : pointage / scan des QR codes
|
| Les rôles `comptable` et `agent-accueil` sont nouveaux : à créer avant
| usage (voir le message d'accompagnement).
*/

Route::middleware(['auth', 'role:super-admin|gestionnaire|moderateur|comptable|agent-accueil'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | Pilotage — tableau de bord (le contenu des widgets doit aussi
        | s'adapter au rôle côté contrôleur / vue).
        */
        Route::get('/', Admin\DashboardController::class)
            ->middleware('role:super-admin|gestionnaire|moderateur|comptable')
            ->name('dashboard');

        /*
        | Participants — inscriptions
        | L'export est déclaré AVANT la route {inscription} pour ne pas être
        | capturé comme un identifiant.
        */
        Route::middleware('role:super-admin|gestionnaire|moderateur|comptable')->group(function () {
            Route::get('inscriptions', [Admin\InscriptionController::class, 'index'])->name('inscriptions.index');
        });

        Route::get('inscriptions/export', [Admin\InscriptionController::class, 'export'])
            ->middleware('role:super-admin|gestionnaire|comptable')
            ->name('inscriptions.export');

        Route::middleware('role:super-admin|gestionnaire|moderateur|comptable')->group(function () {
            Route::get('inscriptions/{inscription}', [Admin\InscriptionController::class, 'show'])->name('inscriptions.show');
        });

        Route::post('inscriptions/{inscription}/recepisse', [Admin\InscriptionController::class, 'renvoyerRecepisse'])
            ->middleware(['role:super-admin|gestionnaire|moderateur', 'throttle:10,1'])
            ->name('inscriptions.recepisse');

        /*
        | Jour J — pointage / contrôle d'accès (à créer : Admin\PointageController)
        */
        Route::middleware('role:super-admin|gestionnaire|moderateur|agent-accueil')->group(function () {
            Route::get('pointage', [Admin\PointageController::class, 'index'])->name('pointage.index');
            Route::post('pointage', [Admin\PointageController::class, 'store'])
                ->middleware('throttle:120,1')
                ->name('pointage.store');
        });

        /*
        | Éditions — contenu et organisation
        */
        Route::middleware('role:super-admin|gestionnaire')->group(function () {
            Route::resource('editions', Admin\EditionController::class)->except('show');
            Route::resource('editions.programmes', Admin\ProgrammeController::class)->shallow()->except('show');
            Route::resource('editions.intervenants', Admin\IntervenantController::class)->shallow()->except('show');
            Route::resource('editions.partenaires', Admin\PartenaireController::class)->shallow()->except('show');

            // Galerie photo d'une édition
            Route::get('editions/{edition}/galerie', [Admin\GalerieController::class, 'index'])->name('editions.galerie.index');
            Route::post('editions/{edition}/galerie', [Admin\GalerieController::class, 'store'])->name('editions.galerie.store');
            Route::delete('editions/{edition}/galerie/{media}', [Admin\GalerieController::class, 'destroy'])->name('editions.galerie.destroy');
        });

        /*
        | Finance — grille tarifaire et paiements
        | La grille est consultable par le comptable, modifiable seulement par
        | le gestionnaire / super-admin. (à créer : Admin\PaiementController)
        */
        Route::get('editions/{edition}/prix', [Admin\PrixController::class, 'index'])
            ->middleware('role:super-admin|gestionnaire|comptable')
            ->name('editions.prix.index');

        Route::post('editions/{edition}/prix', [Admin\PrixController::class, 'store'])
            ->middleware('role:super-admin|gestionnaire')
            ->name('editions.prix.store');

        // Paiement hors ligne (espèces, virement, chèque) saisi depuis la fiche inscription
        Route::post('inscriptions/{inscription}/paiement-manuel', [Admin\PaiementController::class, 'storeManuel'])
            ->middleware(['role:super-admin|gestionnaire|comptable', 'throttle:20,1'])
            ->name('paiements.manuel.store');

        Route::middleware('role:super-admin|gestionnaire|comptable')->group(function () {
            Route::get('paiements', [Admin\PaiementController::class, 'index'])->name('paiements.index');
            Route::get('paiements/export', [Admin\PaiementController::class, 'export'])->name('paiements.export');
            Route::get('paiements/encaisser', [Admin\PaiementController::class, 'encaisser'])->name('paiements.encaisser');
        });

        /*
        | Paramétrage global, partagé entre les éditions
        */
        Route::middleware('role:super-admin')->group(function () {
            Route::resource('regions', Admin\RegionController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('profils', Admin\ProfilController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        /*
        | Administration — réservé au super-admin
        | (journal : à créer, par ex. avec spatie/laravel-activitylog)
        */
        Route::middleware('role:super-admin')->group(function () {
            Route::resource('users', Admin\UserController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('journal', [Admin\JournalController::class, 'index'])->name('journal.index');
        });
    });

require __DIR__.'/auth.php';
