<?php

namespace App\Http\Controllers;

use App\Models\ListeAchat;
use App\Models\BouteilleCatalogue;
use App\Models\Bouteille;
use Illuminate\Http\Request;
use App\Models\Pays;
use App\Models\TypeVin;
use App\Models\Region;


class ListeAchatController extends Controller
{
    /**
     * Affiche la liste d'achat de l'utilisateur courant.
     */
    public function index()
    {
        $user = auth()->user();

        $items = $user
            ->listeAchat()
            ->with('bouteilleCatalogue')
            ->orderBy('achete')
            ->orderBy('date_ajout', 'desc')
            ->paginate(10);

        $allItems = $user
            ->listeAchat()
            ->with('bouteilleCatalogue')
            ->get();

        // Totaux
        $totalPrice = $allItems->sum(function ($item) {
            if (!$item->bouteilleCatalogue) {
                return 0;
            }
            return (float)($item->bouteilleCatalogue->prix ?? 0) * (int)($item->quantite ?? 0);
        });

        $totalItem = $allItems->sum(fn($item) => (int)($item->quantite ?? 0));
        $avgPrice  = $allItems->count() ? $totalPrice / $allItems->count() : 0;

        // Filtres (pays, type, etc.)
        $pays = Pays::all();
        $types = TypeVin::all();
        $regions = Region::all();
        $millesimes = BouteilleCatalogue::select('millesime')
            ->whereNotNull('millesime')
            ->distinct()
            ->orderBy('millesime', 'desc')
            ->get();

        /*
         * 🔸 NOUVEAU : construire une map pour savoir si une bouteille
         * de la liste d’achat existe déjà dans un cellier de l’utilisateur.
         *
         *  cellerMap[ id_bouteille_catalogue ] = [
         *      'cellier_id'   => ...,
         *      'bouteille_id' => ...   // id dans la table bouteilles
         *  ]
         */

        // 1) Récupérer tous les id de bouteilles catalogue de la liste d'achat
        $catalogueIds = $allItems
            ->pluck('bouteille_catalogue_id')
            ->filter()
            ->unique()
            ->values();

        $cellarMap = [];

        if ($catalogueIds->isNotEmpty()) {

            // 2) Charger ces bouteilles catalogue (avec leur code SAQ / code_saQ)
            $catalogues = BouteilleCatalogue::whereIn('id', $catalogueIds)
                ->get()
                ->keyBy('id');

            // 3) Récupérer tous les codes SAQ de ces bouteilles catalogue
            //    (⚠ adapte ici si ton champ s'appelle 'code_saq' au lieu de 'code_saQ')
            $codesSaq = $catalogues
                ->pluck('code_saQ')   // ou 'code_saq' selon ton modèle
                ->filter()
                ->unique()
                ->values();

            if ($codesSaq->isNotEmpty()) {
                // 4) Chercher les bouteilles dans les celliers de l'utilisateur qui ont ces codes SAQ
                $bouteillesCellier = Bouteille::query()
                    ->whereIn('code_saq', $codesSaq)
                    ->whereHas('cellier', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->get();

                // Indexer par code_saq pour accès rapide
                $bottlesByCode = $bouteillesCellier->keyBy('code_saq');

                // 5) Construire la map catalogue_id => [cellier_id, bouteille_id]
                foreach ($catalogues as $catalogueId => $catalogue) {
                    $code = $catalogue->code_saQ; // ou code_saq
                    if ($code && isset($bottlesByCode[$code])) {
                        $bouteilleCellier = $bottlesByCode[$code];

                        $cellarMap[$catalogueId] = [
                            'cellier_id'   => $bouteilleCellier->cellier_id,
                            'bouteille_id' => $bouteilleCellier->id,
                        ];
                    }
                }
            }
        }

        return view('liste_achat.index', compact(
            'items',
            'totalPrice',
            'totalItem',
            'avgPrice',
            'pays',
            'types',
            'regions',
            'millesimes',
            'cellarMap',   // 🔸 on l’envoie à la vue
        ));
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

    public function transfer(Request $request, ListeAchat $item)
    {
        $request->validate([
            'cellier_id' => 'required|exists:celliers,id',
        ]);

        $user = auth()->user();
        $cellierId = $request->cellier_id;

        // Vérifier que le cellier appartient à l'utilisateur
        $cellier = $user->celliers()->find($cellierId);

        if (!$cellier) {
            return response()->json([
                'success' => false,
                'message' => 'Cellier non trouvé ou vous n\'avez pas accès à ce cellier.',
            ], 403);
        }

        $quantite = $item->quantite;
        // Charger la bouteille du catalogue avec ses relations nécessaires
        $bouteilleCatalogue = $item->bouteilleCatalogue;

        // Charger les relations si elles ne sont pas déjà chargées
        if (!$bouteilleCatalogue->relationLoaded('pays')) {
            $bouteilleCatalogue->load('pays');
        }
        if (!$bouteilleCatalogue->relationLoaded('typeVin')) {
            $bouteilleCatalogue->load('typeVin');
        }

        // Vérifier si la bouteille existe déjà dans ce cellier
        // Rechercher par nom et cellier_id (comme dans ajoutBouteilleApi)
        $bouteilleExistante = Bouteille::where('cellier_id', $cellierId)
            ->where('nom', $bouteilleCatalogue->nom)
            ->first();

        if ($bouteilleExistante) {
            // Augmenter la quantité si la bouteille existe déjà
            $bouteilleExistante->quantite += $quantite;
            // Mettre à jour le code_saq si ce n'est pas déjà défini
            if (empty($bouteilleExistante->code_saq) && !empty($bouteilleCatalogue->code_saQ)) {
                $bouteilleExistante->code_saq = $bouteilleCatalogue->code_saQ;
            }
            $bouteilleExistante->save();
        } else {
            // Créer une nouvelle bouteille dans le cellier
            $nouvelleBouteille = new Bouteille();
            $nouvelleBouteille->cellier_id = $cellierId;
            $nouvelleBouteille->nom = $bouteilleCatalogue->nom;
            $nouvelleBouteille->pays = $bouteilleCatalogue->pays ? $bouteilleCatalogue->pays->nom : null;
            $nouvelleBouteille->format = $bouteilleCatalogue->volume;
            $nouvelleBouteille->quantite = $quantite;
            $nouvelleBouteille->prix = $bouteilleCatalogue->prix;
            $nouvelleBouteille->code_saq = $bouteilleCatalogue->code_saQ;

            // Ajouter type et millésime si disponibles
            if ($bouteilleCatalogue->typeVin) {
                $nouvelleBouteille->type = $bouteilleCatalogue->typeVin->nom;
            }
            if ($bouteilleCatalogue->millesime) {
                $nouvelleBouteille->millesime = $bouteilleCatalogue->millesime;
            }

            $nouvelleBouteille->save();
        }

        // Supprimer de la liste d'achat
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => "L'item a été transféré dans votre cellier.",
        ]);
    }

    /**
     * Modifier quantité ou statut acheté
     */
    public function update(Request $request, ListeAchat $item)
    {
        // Si la requête contient 'direction', gérer l'incrémentation/décrémentation comme le cellier
        if ($request->has('direction')) {
            $direction = $request->input('direction');

            if ($direction === 'up') {
                $item->quantite++;
            } elseif ($direction === 'down' && $item->quantite > 1) {
                $item->quantite--;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Direction invalide',
                ], 422);
            }

            $item->save();

            return response()->json([
                'success' => true,
                'quantite' => $item->quantite,
            ]);
        }

        // Sinon, mise à jour normale (quantite ou achete)
        $item->update($request->only(['quantite', 'achete']));

        // Si c'est une requête AJAX/JSON, retourner une réponse JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'quantite' => $item->quantite,
                'message' => 'Liste mise à jour.'
            ]);
        }

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

    public function search(Request $request)
    {
        $user = auth()->user();

        $query = ListeAchat::select('liste_achat.*')
            ->join('bouteille_catalogue', 'liste_achat.bouteille_catalogue_id', '=', 'bouteille_catalogue.id')
            ->where('liste_achat.user_id', $user->id)
            ->with('bouteilleCatalogue');

        if ($request->search) {
            $query->where('bouteille_catalogue.nom', 'like', '%' . $request->search . '%');
        }

        if ($request->pays) {
            $query->where('bouteille_catalogue.id_pays', $request->pays);
        }

        if ($request->type) {
            $query->where('bouteille_catalogue.id_type_vin', $request->type);
        }

        if ($request->region) {
            $query->where('bouteille_catalogue.id_region', $request->region);
        }

        if ($request->millesime) {
            $query->where('bouteille_catalogue.millesime', $request->millesime);
        }

        if ($request->prix_min && $request->prix_max) {
            $query->whereBetween('bouteille_catalogue.prix', [$request->prix_min, $request->prix_max]);
        } elseif ($request->prix_min) {
            $query->where('bouteille_catalogue.prix', '>=', $request->prix_min);
        } elseif ($request->prix_max) {
            $query->where('bouteille_catalogue.prix', '<=', $request->prix_max);
        }

        $sortBy = $request->sort_by;
        $sortDirection = $request->sort_direction;

        if ($sortBy && in_array($sortDirection, ['asc', 'desc'])) {
            // Convertir date_import en date_ajout pour la liste d'achat
            if ($sortBy === 'date_import') {
                $sortBy = 'date_ajout';
            }
            
            if (in_array($sortBy, ['nom', 'prix', 'millesime'])) {
                $query->orderBy('bouteille_catalogue.' . $sortBy, $sortDirection);
            } else {
                $query->orderBy('liste_achat.' . $sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('liste_achat.achete', 'asc')
                ->orderBy('liste_achat.date_ajout', 'desc');
        }

        $items = $query->paginate(10);
        $count = $items->total();

        // 🔹 Construire le $cellarMap pour savoir si chaque bouteille est déjà dans un cellier
        $cellarMap = [];

        // Tous les celliers de l’utilisateur
        $cellierIds = $user->celliers()->pluck('id');

        foreach ($items as $item) {
            $b = $item->bouteilleCatalogue;

            if (!$b) {
                continue;
            }

            // Chercher une bouteille dans UN des celliers de l'utilisateur
            $bouteilleCellier = Bouteille::whereIn('cellier_id', $cellierIds)
                ->where('nom', $b->nom)
                ->first();

            if ($bouteilleCellier) {
                // clé = id de bouteille_catalogue (même que $item->bouteille_catalogue_id)
                $cellarMap[$item->bouteille_catalogue_id] = [
                    'cellier_id'   => $bouteilleCellier->cellier_id,
                    'bouteille_id' => $bouteilleCellier->id,
                ];
            }
        }

        return response()->json([
            'html' => view('liste_achat._liste_achat_list', [
                'items'     => $items,
                'count'     => $count,
                'cellarMap' => $cellarMap, 
            ])->render()
        ]);
    }

    // Suggestions de recherche pour l'autocomplétion
    public function suggest(Request $request)
    {
        $search = $request->search;

        if (!$search) {
            return response()->json([]);
        }

        $user = auth()->user();

        $results = $user->listeAchat()
            ->join('bouteille_catalogue', 'liste_achat.bouteille_catalogue_id', '=', 'bouteille_catalogue.id')

            // Filtre
            ->where('bouteille_catalogue.nom', 'like', '%' . $search . '%')

            // Sélection
            ->select('bouteille_catalogue.nom')
            ->distinct()
            ->limit(10)
            ->get();

        return response()->json($results);
    }
}
