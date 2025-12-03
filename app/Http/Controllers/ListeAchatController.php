<?php

namespace App\Http\Controllers;

use App\Models\ListeAchat;
use App\Models\BouteilleCatalogue;
use Illuminate\Http\Request;

class ListeAchatController extends Controller
{
    /**
     * Affiche la liste d'achat de l'utilisateur courant.
     */
    public function index()
    {
        $items = auth()->user()
        ->listeAchat()
        ->with('bouteilleCatalogue')
        ->orderBy('achete')
        ->orderBy('date_ajout', 'desc')
        ->get();

        return view('liste_achat.index', compact('items'));
    }

    /**
     * Ajoute une bouteille à la liste d'achat
     */
    public function store(Request $request)
    {
        $request->validate([
            'bouteille_catalogue_id' => 'required|exists:bouteille_catalogue,id',
            'quantite' => 'nullable|integer|min:1'
        ]);

        $user = auth()->user();
        $bottleId = $request->bouteille_catalogue_id;
        $qty = $request->quantite ?? 1;

        // Vérifier si déjà existant
        $item = ListeAchat::where('user_id', $user->id)
            ->where('bouteille_catalogue_id', $bottleId)
            ->first();

        if ($item) {
            $item->increment('quantite', $qty);

            return response()->json([
                'success' => true,
                'message' => 'Quantité augmentée dans votre liste d’achat.'
            ]);
        }

        // Sinon créer l'entrée
        ListeAchat::create([
            'user_id' => $user->id,
            'bouteille_catalogue_id' => $bottleId,
            'quantite' => $qty,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bouteille ajoutée à votre liste d’achat.'
        ]);
    }

    /**
     * Modifier quantité ou statut acheté
     */
    public function update(Request $request, ListeAchat $item)
    {
        $item->update($request->only(['quantite', 'achete']));

        return back()->with('success', 'Liste mise à jour.');
    }

    /**
     * Supprimer un item
     */
    public function destroy(ListeAchat $item)
    {
        $item->delete();

        return back()->with('success', 'Élément supprimé de votre liste d’achat.');
    }
}
