<?php

namespace App\Http\Controllers;

use App\Models\ListeAchat;
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
        ->get();

        return view('liste_achat.index', compact('items'));
    }
}
