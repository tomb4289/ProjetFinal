<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\BouteilleManuelleController;

Route::get('/', [AccueilController::class, 'index'])->name('welcome');

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

    // --- Gestion des celliers ---

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
});


Route::patch('/bouteilles/{id}/quantite', function($id, Illuminate\Http\Request $request) {
    $b = \App\Models\Bouteille::findOrFail($id);

    if ($request->quantite < 0) {
        return response()->json([
            'success' => false,
            'message' => 'La quantité ne peut pas être négative.'
        ], 422);
    }

    $b->quantite = $request->quantite;
    $b->save();

    return response()->json([
        'success' => true,
        'message' => 'Quantité mise à jour avec succès.'
    ]);
})->name('quantite.update');
