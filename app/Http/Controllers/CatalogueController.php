<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BouteilleCatalogue;

class CatalogueController extends Controller
{
    public function index()
    {
        // Récupère les 10 dernières bouteilles importées avec leurs relations
        $bouteilles = BouteilleCatalogue::with(['pays', 'typeVin'])
            ->orderBy('date_import', 'desc')
            ->paginate(10);


        return view('bouteilles.catalogue', compact('bouteilles'));
    }

    public function search(Request $request)
    {
        $query = BouteilleCatalogue::with(['pays', 'typeVin']);

        if ($request->search) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->pays) {
            $query->where('pays_id', $request->pays);
        }

        if ($request->type) {
            $query->where('type_vin_id', $request->type);
        }

        $bouteilles = $query->paginate(10);

        return response()->json([
            'html' => view('bouteilles._catalogue_list', compact('bouteilles'))->render()
        ]);
    }
}
