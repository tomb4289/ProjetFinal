<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Bouteille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BouteilleManuelleController extends Controller
{
    /**
     * Affiche le formulaire d'ajout manuel d'une bouteille.
     *
     * @param  \App\Models\Cellier  $cellier  Le cellier dans lequel on ajoute la bouteille
     * @return \Illuminate\View\View
     */
    public function create(Cellier $cellier)
    {
        // Retourne la vue d'ajout manuel en passant l'objet Cellier
        return view('bouteilles.ajout-manuelle', [
            'cellier' => $cellier,
        ]);
    }

    /**
     * Traite l'ajout manuel d'une bouteille.
     *
     * Étapes principales :
     * 1) Validation des données envoyées par le formulaire
     * 2) Normalisation du prix en format décimal (2 décimales)
     * 3) Création de l'enregistrement Bouteille lié au cellier
     * 4) Redirection avec message de succès
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cellier        $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Cellier $cellier)
    {
        // 1) Validation des champs provenant du formulaire
        $validated = $request->validate([
            // Nom obligatoire, chaîne de caractères, longueur maximale 255
            'nom'      => 'required|string|max:255',
            // Pays optionnel
            'pays'     => 'nullable|string|max:255',
            // Format (bouteille, magnum, etc.) optionnel
            'format'   => 'nullable|string|max:50',
            // Quantité obligatoire, entier minimum 1
            'quantite' => 'required|integer|min:1',
            // Prix obligatoire, numérique et limité à 0-9999.99
            'prix'     => ['required', 'numeric', 'between:0,9999.99'],
        ]);

        // 2) Forcer le format du prix en décimal avec 2 chiffres après la virgule
        // number_format retourne une chaîne, mais ici c'est acceptable
        $prixDecimal = number_format($validated['prix'], 2, '.', '');

        // 3) Création de la bouteille associée au cellier
        // Utilise le model Bouteille et la méthode create (remplissage massif)
        Bouteille::create([
            'cellier_id' => $cellier->id,
            'nom'        => $validated['nom'],
            // Si le champ est absent, on enregistre null
            'pays'       => $validated['pays'] ?? null,
            'format'     => $validated['format'] ?? null,
            'quantite'   => $validated['quantite'],
            'prix'       => $prixDecimal,
        ]);

        // 4) Redirection vers l'index des celliers avec message flash de succès
        return redirect()
            // Remarque : la route 'cellar.show' est commentée ; on redirige vers 'cellar.index'
            ->route('cellar.index')
            ->with('success', 'Bouteille ajoutée manuellement avec succès.');
    }

    /**
     * API pour augmenter ou diminuer la quantité d'une bouteille.
     *
     * Reçoit un paramètre 'direction' dans la requête :
     * - 'up'   => incrémente la quantité
     * - 'down' => décrémente la quantité (mais pas en dessous de 1)
     *
     * Vérifie également que la bouteille appartient bien au cellier fourni
     * pour éviter les modifications non autorisées.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cellier        $cellier
     * @param  \App\Models\Bouteille     $bouteille
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuantite(Request $request, Cellier $cellier, Bouteille $bouteille)
    {
        // Sécurité : s'assurer que la bouteille appartient bien au cellier
        if ($bouteille->cellier_id !== $cellier->id) {
            // Retourne une erreur 403 Forbidden si la relation n'est pas correcte
            abort(403);
        }

        // Récupère la direction ('up' ou 'down') depuis la requête
        $direction = $request->input('direction');

        if ($direction === 'up') {
            // Incrémente la quantité
            $bouteille->quantite++;
        } elseif ($direction === 'down') {
            // Décrémente la quantité seulement si > 1 pour garder au moins une unité
            if ($bouteille->quantite > 1) {
                $bouteille->quantite--;
            }
        } else {
            // Direction invalide : réponse JSON avec code 422 Unprocessable Entity
            return response()->json([
                'success' => false,
                'message' => 'Direction invalide',
            ], 422);
        }

        // Sauvegarde des modifications sur le modèle
        $bouteille->save();

        // Retourne la nouvelle quantité au format JSON
        return response()->json([
            'success'  => true,
            'quantite' => $bouteille->quantite,
        ]);
    }
}
