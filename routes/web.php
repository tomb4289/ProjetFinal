<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\BouteilleManuelleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;



// Routes accessibles seulement aux invités (non connectés)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Déconnexion : seulement si connecté
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Routes protégées : seulement accessibles si la session est ouverte
Route::middleware('auth')->group(function () {
    Route::get('/', [CatalogueController::class, 'index'])->name('bouteille.catalogue');

    Route::get('/catalogue/search', [CatalogueController::class, 'search'])
        ->name('catalogue.search');

    // Détails d'une bouteille du catalogue
    Route::get('/catalogue/{bouteilleCatalogue}', [CatalogueController::class, 'show'])
        ->name('catalogue.show');

    // Liste des celliers
    Route::get('/celliers', [CellierController::class, 'index'])->name('cellar.index');

    Route::get('/celliers/create', [CellierController::class, 'create'])->name('cellar.create');

    // Enregistrement d'un nouveau cellier
    Route::post('/celliers/create', [CellierController::class, 'store'])->name('cellar.store');

    // Vue principale d’un cellier 
    Route::get('/celliers/{cellier}', [CellierController::class, 'show'])->name('cellar.show');
    Route::get('/celliers/{cellier}/edit', [CellierController::class, 'edit'])->name('cellar.edit');
    Route::put('/celliers/{cellier}/edit', [CellierController::class, 'update'])->name('cellar.update');
    Route::delete('/celliers/{cellier}', [CellierController::class, 'destroy'])->name('cellar.destroy');

    // Ajout manuel de bouteille 

    Route::get('/celliers/{cellier}/bouteilles/ajout', [BouteilleManuelleController::class, 'create'])
        ->name('bouteilles.manuelles.create');

    Route::post('/celliers/{cellier}/bouteilles/ajout', [BouteilleManuelleController::class, 'store'])
        ->name('bouteilles.manuelles.store');

    Route::post('/api/ajout/cellier', [CellierController::class, 'ajoutBouteilleApi'])->name('api.ajout.cellier');

    // Pour Recuperer les cellier du user
    Route::get('/api/celliers', function () {
        return $user = auth()->user()->celliers()->withCount('bouteilles')->get();
    })->name('api.celliers');

    
    // Recherche des bouteilles dans un cellier.
    Route::get('/celliers/{cellier}/search', [CellierController::class, 'search'])
        ->name('celliers.search');


    /**
     *API de mise à jour rapide de la quantité d’une bouteille manuelle.
     */
    Route::patch(
        '/celliers/{cellier}/bouteilles/{bouteille}/quantite',
        [BouteilleManuelleController::class, 'updateQuantite']
    )->name('bouteilles.manuelles.quantite');
    
    // Suppression de bouteille dans un cellier
    Route::delete('/celliers/{cellier}/bouteilles/{bouteille}', [CellierController::class, 'deleteBottle'])
        ->name('bouteilles.delete');

    Route::get('/celliers/{cellier}/bouteilles/{bouteille}/modifier', [CellierController::class, 'editBottle'])
        ->name('bouteilles.edit');

    Route::put(
        '/celliers/{cellier}/bouteilles/{bouteille}',
        [CellierController::class, 'updateBottle']
    )->name('bouteilles.update');

    // Affichage des détails d'une bouteille
    Route::get(
        '/celliers/{cellier}/bouteilles/{bouteille}',
        [CellierController::class, 'showBottle']
    )->name('bouteilles.show');

    // Gestion des notes de dégustation
    Route::get(
        '/celliers/{cellier}/bouteilles/{bouteille}/note',
        [CellierController::class, 'editNote']
    )->name('bouteilles.note.edit');

    Route::put(
        '/celliers/{cellier}/bouteilles/{bouteille}/note',
        [CellierController::class, 'updateNote']
    )->name('bouteilles.note.update');
});



Route::patch(
    '/celliers/{cellier}/bouteilles/{bouteille}/quantite',
    [BouteilleManuelleController::class, 'updateQuantite']
)->name('bouteilles.quantite.update');


Route::middleware('auth')->group(function () {

    // PROFIL
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profil/update-info', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
    Route::post('/profil/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
