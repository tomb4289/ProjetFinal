<?php 

namespace App\Http\Controllers;

use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\BouteilleCatalogue;
use App\Models\Bouteille;

/**
 * Contrôleur pour la gestion des celliers.
 * 
 * Ce contrôleur gère toutes les opérations CRUD (Create, Read, Update, Delete)
 * liées aux celliers des utilisateurs authentifiés.
 */
class CellierController extends Controller
{
    /**
     * Critères de tri autorisés pour les bouteilles d'un cellier.
     * Clé = valeur reçue dans l'URL (?sort=nom), valeur = colonne réelle en BD.
     */
    private array $allowedBottleSorts = [
        'nom'      => 'nom',
        'pays'     => 'pays',
        'type'     => 'type',
        'quantite' => 'quantite',
        'format'   => 'format',
        'prix'     => 'prix',
        'date_ajout' => 'created_at'
    ];

    /**
     * Affiche la liste de tous les celliers de l'utilisateur connecté.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $celliers = $user->celliers()
            ->orderBy('nom')
            ->get();

        return view('cellar.index', compact('celliers'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau cellier.
     * 
     * @return View La vue du formulaire de création
     */
    public function create(): View
    {
        return view('cellar.create');
    }

    /**
     * Enregistre un nouveau cellier dans la base de données.
     * 
     * Valide les données du formulaire et crée un nouveau cellier
     * associé à l'utilisateur connecté.
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @return RedirectResponse Redirection vers la liste des celliers avec un message de succès
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $request->user()->celliers()->create([
            'nom' => $validated['nom'],
        ]);

        return redirect()
            ->route('cellar.index')
            ->with('success', 'Le cellier a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un cellier spécifique avec ses bouteilles,
     * en appliquant éventuellement un tri sur les bouteilles.
     * 
     * Vérifie que le cellier appartient bien à l'utilisateur connecté
     * avant d'afficher les détails.
     * 
     * @param Cellier $cellier Le cellier à afficher
     * @return View La vue contenant les détails du cellier
     */
    public function show(Cellier $cellier): View
    {
        $this->authorizeCellier($cellier);


        // 1. Récupérer les paramètres de tri depuis la requête
        $sort = request()->query('sort', 'nom');           
        $direction = request()->query('direction', 'asc'); 

        // 2. Validation du critère de tri (colonne)
        if (!array_key_exists($sort, $this->allowedBottleSorts)) {
            $sort = 'nom';
        }

        // 3. Validation du sens de tri
        $direction = strtolower($direction);
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        // 4. Récupérer la vraie colonne SQL à partir de la config
        $sortColumn = $this->allowedBottleSorts[$sort];

        // 5. Charger les bouteilles triées via la relation Eloquent
        $cellier->load(['bouteilles' => function ($query) use ($sortColumn, $direction) {
            $query->orderBy($sortColumn, $direction);
        }]);

        

        // On envoie aussi sort/direction à la vue 
        return view('celliers.show', [
            'cellier'   => $cellier,
            'sort'      => $sort,
            'direction' => $direction,
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un cellier existant.
     * 
     * @param Cellier $cellier Le cellier à modifier
     * @return View La vue du formulaire d'édition
     */
    public function edit(Cellier $cellier): View
    {
        $this->authorizeCellier($cellier);

        return view('cellar.update', compact('cellier'));
    }

    /**
     * Met à jour un cellier existant dans la base de données.
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @param Cellier $cellier Le cellier à modifier
     * @return RedirectResponse Redirection vers la liste des celliers avec un message de succès
     */
    public function update(Request $request, Cellier $cellier): RedirectResponse
    {
        $this->authorizeCellier($cellier);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            // 'description' => 'nullable|string',
        ]);

        $cellier->update([
            'nom' => $validated['nom'],
            // 'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('cellar.index')
            ->with('success', 'Le cellier a été mis à jour.');
    }

    /**
     * Supprime un cellier de la base de données.
     * 
     * @param Cellier $cellier Le cellier à supprimer
     * @return RedirectResponse Redirection vers la liste des celliers avec un message de succès
     */
    public function destroy(Cellier $cellier): RedirectResponse
    {
        $this->authorizeCellier($cellier);

        $cellier->delete();

        return redirect()
            ->route('cellar.index')
            ->with('success', 'Le cellier a été supprimé.');
    }

    /**
     * Supprime une bouteille dans un cellier (pas dans le catalogue).
     * @param Cellier $cellier Le cellier contenant la bouteille
     * @param Bouteille $bouteille La bouteille à supprimer
     */
    public function deleteBottle(Cellier $cellier, Bouteille $bouteille): RedirectResponse
    {
        // Vérifie que le cellier appartient au user
        $this->authorizeCellier($cellier);

        // Vérifie que la bouteille appartient à CE cellier
        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        // Supprimer la bouteille (dans le cellier seulement)
        $bouteille->delete();

        return redirect()
            ->route('cellar.show', $cellier)
            ->with('success', 'La bouteille a été supprimée du cellier.');
    }

    /**
     * Affiche le formulaire d'édition d'une bouteille manuelle dans un cellier.
     * 
     * @param Cellier $cellier Le cellier contenant la bouteille
     * @param Bouteille $bouteille La bouteille à modifier
     * @return View La vue du formulaire d'édition de la bouteille
     */
    public function editBottle(Cellier $cellier, Bouteille $bouteille): View
    {
        $this->authorizeCellier($cellier);

        // Vérifie que la bouteille appartient bien au cellier
        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        // Interdit la modification d’une bouteille du catalogue
        if ($bouteille->code_saq !== null) {
            abort(403, 'Cette bouteille vient du catalogue SAQ et ne peut pas être modifiée.');
        }

        return view('bouteilles.modifier-bouteille', compact('cellier', 'bouteille'));
    }

    /**
     * Met à jour une bouteille manuelle dans un cellier.
     */
    public function updateBottle(Request $request, Cellier $cellier, Bouteille $bouteille): RedirectResponse
    {
        $this->authorizeCellier($cellier);

        // Vérifie que la bouteille appartient bien au cellier
        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        // Interdit la modification d’une bouteille provenant du catalogue
        if ($bouteille->code_saq !== null) {
            abort(403, 'Impossible de modifier une bouteille provenant du catalogue SAQ.');
        }

        // Validation des champs
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'quantite' => 'required|integer|min:0',
            'format' => 'nullable|string|max:25',
            'pays' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        // Mise à jour de la bouteille
        $bouteille->update($validated);

        return redirect()
            ->route('cellar.show', $cellier->id)
            ->with('success', 'La bouteille a été mise à jour avec succès.');
    }

    // Ajout de bouteille du catalogue au cellier via API
    public function ajoutBouteilleApi(Request $request)
    {
        // 1. Trouver la bouteille dans le catalogue
        $catalogBottle = BouteilleCatalogue::find($request->bottle_id);

        // Vérifier si la bouteille du catalogue existe
        if (!$catalogBottle) {
            return response()->json([
                'success' => false,
                'message' => 'Bouteille du catalogue non trouvée'
            ], 404);
        }

        // 2. Vérifier si la bouteille existe déjà dans le cellier
        $bottleExist = Bouteille::where('cellier_id', $request->cellar_id)
            ->where('nom', $catalogBottle->nom)
            ->first();

        // 3. Si elle existe, augmenter la quantité
        if ($bottleExist) {
            $bottleExist->quantite += $request->quantity;
            $bottleExist->save();

            // Retourner une réponse JSON indiquant que la quantité a été augmentée
            return response()->json([
                'success' => true,
                'message' => 'Quantité augmentée',
                'data' => $bottleExist
            ]);
        }

        // 4. Sinon, créer une nouvelle entrée dans le cellier
        $new = new Bouteille();
        $new->cellier_id = $request->cellar_id;
        $new->nom = $catalogBottle->nom;
        $new->pays = $catalogBottle->pays->nom;
        $new->format = $catalogBottle->format;
        $new->quantite = $request->quantity;
        $new->prix = $catalogBottle->prix;

        // Enregistrer la nouvelle bouteille dans le cellier
        $new->save();

        // Retourner une réponse JSON indiquant que la bouteille a été ajoutée
        return response()->json([
            'success' => true,
            'message' => 'Bouteille ajoutée avec succès',
            'data' => $new
        ]);
    }

    /**
     * Vérifie que le cellier appartient bien à l'utilisateur connecté.
     * 
     * @param Cellier $cellier Le cellier à vérifier
     */
    protected function authorizeCellier(Cellier $cellier): void
    {
        if ($cellier->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
