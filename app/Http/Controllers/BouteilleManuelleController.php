<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Bouteille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BouteilleManuelleController extends Controller
{
    /**
     * Affiche le formulaire d'ajout manuel d'une bouteille
     */
    public function create(Cellier $cellier)
    {
        return view('bouteilles.ajout-manuelle', [
            'cellier' => $cellier,
        ]);
    }

    /**
     * Traite l'ajout manuel d'une bouteille.
     */
    public function store(Request $request, Cellier $cellier)
    {
        // 1) Validation des champs
        $validated = $request->validate([
            'nom'      => 'required|string|max:255',
            'pays'     => 'nullable|string|max:255',
            'format'   => 'nullable|string|max:50',
            'quantite' => 'required|integer|min:1',
            'prix'     => ['required', 'numeric', 'between:0,9999.99'],
        ]);

        // 2) Prix forcé en décimal 
        $prixDecimal = number_format($validated['prix'], 2, '.', '');

        // 3) Création de la bouteille liée à ce cellier
        Bouteille::create([
            'cellier_id' => $cellier->id,
            'nom'        => $validated['nom'],
            'pays'       => $validated['pays'] ?? null,
            'format'     => $validated['format'] ?? null,
            'quantite'   => $validated['quantite'],
            'prix'       => $prixDecimal,
        ]);

        // 4) Redirection vers la vue principale du cellier
        return redirect()
            ->route('cellar.show', $cellier)
            ->route('cellar.index')
            ->with('success', 'Bouteille ajoutée manuellement avec succès.');
    }

    /**
     * API pour augmenter / diminuer la quantité d’une bouteille.
     * 
     * Reçoit un champ "direction" : "up" ou "down".
     */
    public function updateQuantite(Request $request, Cellier $cellier, Bouteille $bouteille)
    {
        // 1) Vérifier que l’utilisateur est bien propriétaire du cellier
        if ($cellier->user_id !== Auth::id()) {
            abort(403);
        }

        // 2) Vérifier que la bouteille appartient bien à ce cellier
        if ($bouteille->cellier_id !== $cellier->id) {
            abort(404);
        }

        // 3) Validation de la direction
        $validated = $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $delta = $validated['direction'] === 'up' ? 1 : -1;

        // 4) Calcul de la nouvelle quantité (minimum 0)
        $nouvelleQuantite = max(0, $bouteille->quantite + $delta);

        $bouteille->quantite = $nouvelleQuantite;
        $bouteille->save();

        // 5) Réponse JSON 
        return response()->json([
            'success'  => true,
            'quantite' => $bouteille->quantite,
        ]);
    }
}
