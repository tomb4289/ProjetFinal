<?php

namespace App\Http\Controllers;

use App\Models\BouteilleCatalogue;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur pour la page d'accueil.
 * 
 * Gère l'affichage de la page de landing avec le formulaire d'authentification
 * et les bouteilles du catalogue pour les tests.
 * 
 * @package App\Http\Controllers
 */
class AccueilController extends Controller
{
    /**
     * Affiche la page d'accueil avec les dernières bouteilles importées du catalogue.
     * 
     * Récupère les 10 dernières bouteilles importées du catalogue SAQ
     * avec leurs relations (pays et type de vin) pour les afficher sur la page d'accueil.
     * 
     * @return View La vue de la page d'accueil avec les bouteilles du catalogue
     */
    public function index(): View
    {
        // Récupère les 10 dernières bouteilles importées avec leurs relations
        $bouteilles = BouteilleCatalogue::with(['pays', 'typeVin'])
            ->orderBy('date_import', 'desc')
            ->limit(10)
            ->get();

        return view('welcome', compact('bouteilles'));
    }
}
