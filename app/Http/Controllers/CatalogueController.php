<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BouteilleCatalogue;
use App\Models\Pays;
use App\Models\TypeVin;

class CatalogueController extends Controller
{
    public function index()
    {

        $bouteilles = BouteilleCatalogue::with(['pays', 'typeVin'])
            ->orderBy('nom')
            ->paginate(10);

        $pays = Pays::orderBy('nom')->get();
        $types = TypeVin::orderBy('nom')->get();
        $millesimes = BouteilleCatalogue::select('millesime')
            ->whereNotNull('millesime')
            ->distinct()
            ->orderBy('millesime', 'desc')
            ->get();

        $count = $bouteilles->total();

        return view('bouteilles.catalogue', compact('bouteilles', 'pays', 'types', 'millesimes', 'count'));
    }

    public function search(Request $request)
    {
        $query = BouteilleCatalogue::with(['pays', 'typeVin']);

        if ($request->search) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->pays) {
            $query->where('id_pays', $request->pays);
        }

        if ($request->type) {
            $query->where('id_type_vin', $request->type);
        }

        if ($request->millesime) {
            $query->where('millesime', $request->millesime);
        }

        if ($request->prix_min && $request->prix_max) {
            $query->whereBetween('prix', [$request->prix_min, $request->prix_max]);
        } elseif ($request->prix_min) {
            $query->where('prix', '>=', $request->prix_min);
        } elseif ($request->prix_max) {
            $query->where('prix', '<=', $request->prix_max);
        }

        $sortBy = $request->sort_by;
        $sortDirection = $request->sort_direction;

        if ($sortBy && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        }



        $bouteilles = $query->paginate(10);


        $count = $bouteilles->total();

        return response()->json([
            'html' => view('bouteilles._catalogue_list', compact('bouteilles', 'count'))->render()
        ]);
    }

    /**
     * Affiche les détails d'une bouteille du catalogue.
     * 
     * @param BouteilleCatalogue $bouteilleCatalogue La bouteille du catalogue à afficher
     * @return \Illuminate\View\View La vue contenant les détails de la bouteille
     */
    public function show(BouteilleCatalogue $bouteilleCatalogue)
    {
        // Charger les relations nécessaires
        $bouteilleCatalogue->load(['pays', 'typeVin']);

        // Préparer les données à afficher
        $donnees = [
            'nom' => $bouteilleCatalogue->nom,
            'pays' => $bouteilleCatalogue->pays ? $bouteilleCatalogue->pays->nom : null,
            'prix' => $bouteilleCatalogue->prix,
            'format' => $bouteilleCatalogue->volume,
            'type' => $bouteilleCatalogue->typeVin ? $bouteilleCatalogue->typeVin->nom : null,
            'millesime' => $bouteilleCatalogue->millesime,
            'image' => $bouteilleCatalogue->image,
            'region' => $bouteilleCatalogue->region,
            'code_saq' => $bouteilleCatalogue->code_saQ,
        ];

        return view('bouteilles.details', [
            'bouteilleCatalogue' => $bouteilleCatalogue,
            'donnees' => $donnees,
        ]);
    }

    // Suggestions de recherche pour l'autocomplétion
    public function suggest(Request $request)
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->search;

        // Si la requête est trop courte, retourner une réponse vide
        if (!$search) {
            return response()->json([]);
        }

        // Rechercher les bouteilles correspondant au terme
        $results = BouteilleCatalogue::where('nom', 'like', '%' . $search . '%')
            ->limit(10)
            ->get(['id', 'nom']);

        return response()->json($results);
    }
}
