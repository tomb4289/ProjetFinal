<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\BouteilleManuelleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ListeAchatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PartageController;
use App\Http\Controllers\SignalementController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', [CatalogueController::class, 'index'])->name('bouteille.catalogue');

    // Signalement de problème sur une bouteille du catalogue
    Route::get('/catalogue/{bouteille}/signaler', [SignalementController::class, 'create'])
        ->name('signalement.create');

    // Enregistrement d'un signalement
    Route::post('/catalogue/{bouteille}/signaler', [SignalementController::class, 'store'])
        ->name('signalement.store');

    // Catalogue de bouteilles
    Route::get('/catalogue/search', [CatalogueController::class, 'search'])
        ->name('catalogue.search');

    // Suggestions de recherche
    Route::get('/catalogue/suggest', [CatalogueController::class, 'suggest']);

    // API pour trouver une bouteille du catalogue par code SAQ
    Route::get('/api/catalogue/by-code-saq/{codeSaq}', [CatalogueController::class, 'findByCodeSaq']);

    // API pour trouver une bouteille du catalogue par nom (fallback)
    Route::get('/api/catalogue/by-name/{nom}', [CatalogueController::class, 'findByName']);

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

    // Pour récupérer les celliers du user
    Route::get('/api/celliers', function () {
        /** @var User $user */
        $user = Auth::user();

        $celliers = $user->celliers()
            ->withCount('bouteilles')
            ->withSum('bouteilles as total_bouteilles', 'quantite')
            ->get();

        return response()->json([
            'celliers' => $celliers,
            'celliersCount' => $celliers->count(),
            'canCreateMore' => $celliers->count() < 6
        ]);
    })->name('api.celliers');

    // Recherche des bouteilles dans un cellier.
    Route::get('/celliers/{cellier}/search', [CellierController::class, 'search'])
        ->name('celliers.search');

    /**
     * API de mise à jour rapide de la quantité d'une bouteille.
     */
    Route::patch(
        '/celliers/{cellier}/bouteilles/{bouteille}/quantite',
        [BouteilleManuelleController::class, 'updateQuantite']
    )->name('bouteilles.quantite.update');

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

    Route::delete(
        '/celliers/{cellier}/bouteilles/{bouteille}/note',
        [CellierController::class, 'deleteNote']
    )->name('bouteilles.note.delete');

    // PROFIL
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profil/update-info', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
    Route::post('/profil/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // LISTE D'ACHAT
    // Voir la liste d'achat
    Route::get('/liste-achat', [ListeAchatController::class, 'index'])
        ->name('listeAchat.index');

    Route::post('/liste-achat', [ListeAchatController::class, 'store'])
        ->name('listeAchat.store');

    Route::put('/liste-achat/{item}', [ListeAchatController::class, 'update'])
        ->name('listeAchat.update');

    Route::patch('/liste-achat/{item}', [ListeAchatController::class, 'update'])
        ->name('listeAchat.update.patch');

    Route::delete('/liste-achat/{item}', [ListeAchatController::class, 'destroy'])
        ->name('listeAchat.destroy');

    Route::delete('/liste-achat', [ListeAchatController::class, 'destroyAll'])
        ->name('listeAchat.destroyAll');

    Route::post('/liste-achat/{item}/transfer', [ListeAchatController::class, 'transfer'])
        ->name('listeAchat.transfer');

    Route::post('/liste-achat/transfer-all', [ListeAchatController::class, 'transferAll'])
        ->name('listeAchat.transferAll');

    Route::get('/liste-achat/search', [ListeAchatController::class, 'search'])
        ->name('listeAchat.search');

    // Suggestions de recherche
    Route::get('/liste-achat/suggest', [ListeAchatController::class, 'suggest']);


    Route::get('/api/listeAchat/stats', function () {

        $user = Auth::user();

        $items = $user->listeAchat()->with('bouteilleCatalogue')->get();

        $totalPrice = $items->sum(function ($item) {
            if (!$item->bouteilleCatalogue) {
                return 0;
            }
            return (float)($item->bouteilleCatalogue->prix ?? 0) * (int)($item->quantite ?? 0);
        });
        $totalItem = $items->sum(fn($item) => (int)($item->quantite ?? 0));
        $avgPrice = $items->count() ? $totalPrice / $items->count() : 0;

        return response()->json([
            'totalItem' => $totalItem,
            'totalPrice' => $totalPrice,
            'averagePrice' => $avgPrice,
        ]);
    });


    // PARTAGE
    // Générer un lien de partage unique pour une bouteille
    // Retourne un JSON avec l'URL de partage et le token
    Route::post('/api/partage/{bouteille}', [PartageController::class, 'store'])
        ->name('partage.store');
});

// Routes publiques pour le partage (accessibles sans authentification)
// Affiche la vue publique d'une bouteille partagée via son token unique
Route::get('/partage/{token}', [PartageController::class, 'show'])
    ->name('partage.show');

// Routes d'administration (réservées aux admins)
Route::middleware(['auth', 'active', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Liste des usagers
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');

        // Détails d'un usager
        Route::get('/users/{id}', [AdminController::class, 'show'])->name('users.show');

        // Activer / désactiver un usager
        Route::post('/users/{id}/toggle-active', [AdminController::class, 'toggleActive'])
            ->name('users.toggle-active');

        // Supprimer un usager
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])
            ->name('users.destroy');

        // ROUTES STATISTIQUES
        Route::get('/statistics', [AdminController::class, 'statistics'])
            ->name('statistics.index');

        Route::get('/statistics/data', [AdminController::class, 'statisticsData'])
            ->name('statistics.data');

        Route::get('/signalements/{signalement}', [SignalementController::class, 'show'])
            ->name('signalements.show');

        // Liste des signalements
        Route::get('/signalements', [SignalementController::class, 'index'])
            ->name('signalements.index');

        // Marquer un signalement comme lu
        Route::patch('/signalements/{signalement}/read', [SignalementController::class, 'markAsRead'])
            ->name('signalements.read');

        // Suppression d'un signalement
        Route::delete('/signalements/{signalement}', [SignalementController::class, 'destroy'])
            ->name('signalements.destroy');

        // Édition d'une bouteille du catalogue
        Route::get('/catalogue/{bouteilleCatalogue}/edit', [AdminController::class, 'editCatalogueBottle'])
            ->name('catalogue.edit');

        Route::put('/catalogue/{bouteilleCatalogue}', [AdminController::class, 'updateCatalogueBottle'])
            ->name('catalogue.update');
            
    });
