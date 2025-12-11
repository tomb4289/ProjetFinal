<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Models\BouteilleCatalogue;
use Illuminate\Http\Request;

class SignalementController extends Controller
{
    /**
     * Affiche la liste des signalements (pour l'admin)
     */
    public function index()
    {
        $signalements = Signalement::query()
            ->orderBy('is_read', 'asc')
            ->latest()
            ->paginate(20);

        $nonLus = Signalement::where('is_read', false)->count();

        return view('signalements.index', compact('signalements', 'nonLus'));
    }

    public function markAsRead(Signalement $signalement)
    {
        $signalement->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Signalement marqué comme lu.');
    }

    // Afficher un signalement spécifique
    public function show(Signalement $signalement)
    {
        return view('signalements.show', compact('signalement'));
    }

    /**
     * Affiche le formulaire de création d'un signalement
     */
    public function create(BouteilleCatalogue $bouteille)
    {
        return view('signalements.create', [
            'bouteille' => $bouteille
        ]);
    }

    /**
     * Valide et enregistre un signalement
     */
    public function store(Request $request, BouteilleCatalogue $bouteille)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150|min:3',
            'description' => 'required|string|max:2000|min:10',
        ]);

        Signalement::create([
            'bouteille_catalogue_id' => $bouteille->id,
            'nom' => $validated['nom'],
            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('catalogue.show', $bouteille->id)
            ->with('success', 'Votre signalement a été envoyé avec succès.');
    }

    /**
     * Supprime un signalement
     */
    public function destroy(Signalement $signalement)
    {
        $signalement->delete();

        return redirect()
            ->back()
            ->with('success', 'Le signalement a été supprimé.');
    }
}
