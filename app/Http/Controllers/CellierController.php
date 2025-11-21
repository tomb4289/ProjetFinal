<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
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
     * PV-13 : Affiche les détails d'un cellier spécifique avec ses bouteilles.
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

        // On charge les bouteilles liées pour la vue principale
        $cellier->load('bouteilles');

        // On utilise ta vue PV-13
        return view('celliers.show', compact('cellier'));
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
