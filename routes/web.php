<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Site;
use App\Http\Controllers\WebhookController;
use App\Livewire\Registration\RegisterForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
*/

Route::get('/', Site\AccueilController::class)->name('accueil');

Route::get('/editions/{edition}', [Site\EditionController::class, 'show'])->name('editions.show');

Route::get('/editions/{edition}/inscription', RegisterForm::class)->name('inscription.form');

/*
|--------------------------------------------------------------------------
| Parcours « code d'inscription » : payer, retrouver son récépissé
|--------------------------------------------------------------------------
| Ces routes exposent un dossier à partir d'un simple code : elles sont donc
| limitées en fréquence pour empêcher l'énumération des codes existants.
*/

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/mon-inscription', [ReceiptController::class, 'rechercher'])->name('inscription.rechercher');
    Route::get('/paiement/{code}', [PaymentController::class, 'initier'])->name('paiement.initier');
    Route::get('/recu/{code}', [ReceiptController::class, 'telecharger'])->name('recu.telecharger');
});

Route::get('/paiement/succes', [PaymentController::class, 'succes'])->name('paiement.succes');
Route::get('/paiement/annule', [PaymentController::class, 'annule'])->name('paiement.annule');

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
*/

Route::get('/dashboard', function () {
    return auth()->user()->hasAnyRole(['super-admin', 'gestionnaire', 'moderateur'])
        ? redirect()->route('admin.dashboard')
        : redirect()->route('accueil');
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
*/

Route::middleware(['auth', 'role:super-admin|gestionnaire'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::resource('editions', Admin\EditionController::class)->except('show');
        Route::resource('editions.programmes', Admin\ProgrammeController::class)->shallow()->except('show');
        Route::resource('editions.intervenants', Admin\IntervenantController::class)->shallow()->except('show');
        Route::resource('editions.partenaires', Admin\PartenaireController::class)->shallow()->except('show');

        // Galerie photo d'une édition
        Route::get('editions/{edition}/galerie', [Admin\GalerieController::class, 'index'])->name('editions.galerie.index');
        Route::post('editions/{edition}/galerie', [Admin\GalerieController::class, 'store'])->name('editions.galerie.store');
        Route::delete('editions/{edition}/galerie/{media}', [Admin\GalerieController::class, 'destroy'])->name('editions.galerie.destroy');

        // Grille tarifaire région x profil
        Route::get('editions/{edition}/prix', [Admin\PrixController::class, 'index'])->name('editions.prix.index');
        Route::post('editions/{edition}/prix', [Admin\PrixController::class, 'store'])->name('editions.prix.store');

        // Paramétrage global, partagé entre les éditions
        Route::resource('regions', Admin\RegionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('profils', Admin\ProfilController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('inscriptions', [Admin\InscriptionController::class, 'index'])->name('inscriptions.index');
        Route::get('inscriptions/export', [Admin\InscriptionController::class, 'export'])->name('inscriptions.export');
    });

require __DIR__.'/auth.php';
