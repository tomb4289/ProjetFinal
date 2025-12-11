<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\BouteilleCatalogue;
use App\Models\Pays;
use App\Models\TypeVin;
use App\Models\Region;
use App\Models\Bouteille;
use Illuminate\Validation\Rule;


/**
 * Contrôleur pour la gestion des celliers.
 * 
 * Ce contrôleur gère toutes les opérations CRUD (Create, Read, Update, Delete)
 * liées aux celliers des utilisateurs authentifiés.
 */
class CellierController extends Controller
{
    /**
     * Critères de tri autorisés pour les bouteilles d'un cellier.
     */
    private array $allowedBottleSorts = [
        'nom'        => 'nom',
        'pays'       => 'pays',
        'type'       => 'type',
        'quantite'   => 'quantite',
        'millesime'  => 'millesime',
        'region'     => 'region',
        'format'     => 'format',
        'prix'       => 'prix',
        'date_ajout' => 'created_at',
    ];

    /**
     * 
     * Filtres possibles : nom, type, pays, millesime.
     */
    public function search(Request $request, Cellier $cellier)
    {
        $this->authorizeCellier($cellier);

        // On part des bouteilles liées à CE cellier uniquement
        $query = $cellier->bouteilles();

        // Filtres (recherche partielle)
        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('pays')) {
            $query->where('pays', 'like', '%' . $request->pays . '%');
        }

        if ($request->filled('millesime')) {
            $query->where('millesime', 'like', '%' . $request->millesime . '%');
        }

        if ($request->filled('region')) {
            // Filtrer par région via la relation avec le catalogue
            // On cherche les bouteilles qui ont un code_saq ou un nom correspondant à une bouteille du catalogue avec cette région
            $query->whereExists(function ($subquery) use ($request) {
                $subquery->select(DB::raw(1))
                    ->from('bouteille_catalogue')
                    ->where(function ($q) {
                        $q->whereColumn('bouteille_catalogue.code_saQ', 'bouteilles.code_saq')
                            ->orWhereColumn('bouteille_catalogue.nom', 'bouteilles.nom');
                    })
                    ->join('regions', 'bouteille_catalogue.id_region', '=', 'regions.id')
                    ->where('regions.nom', $request->region);
            });
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }


        $sort = $request->query('sort', 'nom');
        $direction = $request->query('direction', 'asc');

        if (!array_key_exists($sort, $this->allowedBottleSorts)) {
            $sort = 'nom';
        }

        $direction = strtolower($direction);
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $sortColumn = $this->allowedBottleSorts[$sort];

        $bouteilles = $query->orderBy($sortColumn, $direction)->get();

        // Nombre total de bouteilles dans le cellier (sans filtres)
        $totalBottlesCount = $cellier->bouteilles()->count();

        return response()->json([
            'html' => view('celliers._bouteilles_list', [
                'cellier'    => $cellier,
                'bouteilles' => $bouteilles,
                'totalBottlesCount' => $totalBottlesCount, // Nombre total de bouteilles dans le cellier
            ])->render(),
        ]);
    }

    /**
     * Affiche la liste de tous les celliers de l'utilisateur connecté.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();



        $celliers = $user->celliers()
            ->withCount('bouteilles')
            ->withSum('bouteilles as total_bouteilles', 'quantite')
            ->orderBy('nom')
            ->get();

        // Vérifier si on doit afficher le tip pour un nouvel utilisateur
        $showWelcomeTip = false;
        $welcomeTipCellierId = null;

        if ($celliers->count() === 1) {
            $cellier = $celliers->first();
            if ($cellier->nom === 'Mon cellier' && $cellier->bouteilles_count === 0) {
                $showWelcomeTip = true;
                $welcomeTipCellierId = $cellier->id;
            }
        }

        $celliersCount = $celliers->count();
        $canCreateMore = $celliersCount < 6;

        return view('cellar.index', [
            'celliers' => $celliers,
            'showWelcomeTip' => $showWelcomeTip,
            'welcomeTipCellierId' => $welcomeTipCellierId,
            'celliersCount' => $celliersCount,
            'canCreateMore' => $canCreateMore,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau cellier.
     * 
     * @return View|RedirectResponse La vue du formulaire de création ou redirection si limite atteinte
     */
    public function create()
    {
        $user = Auth::user();
        $celliersCount = $user->celliers()->count();

        // Limite de 6 celliers par utilisateur
        if ($celliersCount >= 6) {
            $message = 'Vous avez atteint la limite maximale de 6 celliers. Veuillez supprimer un cellier existant avant d\'en créer un nouveau.';

            return redirect()
                ->route('cellar.index')
                ->with('error', $message);
        }

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:20',
                Rule::unique('celliers')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }),
            ],
        ], [
            'nom.unique' => 'Vous avez déjà un cellier avec ce nom.',
            'nom.max' => 'Le nom du cellier ne doit pas dépasser 20 caractères.',
        ]);


        $user = $request->user();
        $celliersCount = $user->celliers()->count();

        // Limite de 6 celliers par utilisateur
        if ($celliersCount >= 6) {
            $message = 'Vous avez atteint la limite maximale de 6 celliers. Veuillez supprimer un cellier existant avant d\'en créer un nouveau.';

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $message);
        }

        $user->celliers()->create([
            'nom' => $validated['nom'],
        ]);

        $message = 'Le cellier a été créé avec succès.';

        // Si la requête est AJAX, retourner une réponse JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()
            ->route('cellar.index')
            ->with('success', $message);
    }

    /**
     * Affiche les détails d'un cellier spécifique avec ses bouteilles,
     * en appliquant tri ET recherche (backend).
     * 
     * @param Request $request
     * @param Cellier $cellier Le cellier à afficher
     * @return View La vue contenant les détails du cellier
     */

    public function show(Request $request, Cellier $cellier): View
    {
        $this->authorizeCellier($cellier);

        // Bouteilles du cellier
        $bouteilles = $cellier->bouteilles()
            ->orderBy('nom')
            ->get();

        $cellier->setRelation('bouteilles', $bouteilles);

        // liste complète des pays / types / millésimes / régions
        $pays = Pays::orderBy('nom')->get();

        $types = TypeVin::orderBy('nom')->get();

        $regions = Region::orderBy('nom')->get();

        $millesimes = BouteilleCatalogue::select('millesime')
            ->whereNotNull('millesime')
            ->distinct()
            ->orderBy('millesime', 'desc')
            ->get();

        // titre sous le nom du cellier
        $uniqueBottlesCount = $bouteilles->count();
        if ($uniqueBottlesCount === 0) {
            $undertitle = 'Aucune bouteille';
        } elseif ($uniqueBottlesCount === 1) {
            $undertitle = '1 bouteille unique';
        } else {
            $undertitle = $uniqueBottlesCount . ' bouteilles uniques';
        }

        return view('celliers.show', [
            'cellier'     => $cellier,
            'undertitle'  => $undertitle,
            'pays'        => $pays,
            'types'       => $types,
            'regions'     => $regions,
            'millesimes'  => $millesimes,
        ]);
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
            'nom' => [
                'required',
                'string',
                'max:20',
                Rule::unique('celliers')
                    ->ignore($cellier->id)
                    ->where(function ($query) use ($request) {
                        return $query->where('user_id', $request->user()->id);
                    }),
            ],
        ], [
            'nom.unique' => 'Vous avez déjà un cellier avec ce nom.',
            'nom.max' => 'Le nom du cellier ne doit pas dépasser 20 caractères.',
        ]);

        $cellier->update([
            'nom' => $validated['nom'],
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
    public function destroy(Cellier $cellier)
    {
        $this->authorizeCellier($cellier);

        $cellier->delete();

        $message = 'Le cellier a été supprimé.';

        // Si la requête est AJAX, retourner une réponse JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()
            ->route('cellar.index')
            ->with('success', $message);
    }

    /**
     * Supprime une bouteille dans un cellier (pas dans le catalogue).
     * @param Cellier $cellier Le cellier contenant la bouteille
     * @param Bouteille $bouteille La bouteille à supprimer
     */
    public function deleteBottle(Cellier $cellier, Bouteille $bouteille)
    {
        // Vérifie que le cellier appartient au user
        $this->authorizeCellier($cellier);

        // Vérifie que la bouteille appartient à CE cellier
        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        // Supprimer la bouteille (dans le cellier seulement)
        $bouteille->delete();

        // Si la requête est AJAX, retourner une réponse JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'La bouteille a été supprimée du cellier.'
            ]);
        }

        return redirect()
            ->route('cellar.show', $cellier)
            ->with('success', 'La bouteille a été supprimée du cellier.');
    }

    /**
     * Affiche le formulaire d'édition d'une bouteille manuelle dans un cellier.
     */
    public function editBottle(Cellier $cellier, Bouteille $bouteille): View
    {
        $this->authorizeCellier($cellier);

        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

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

        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        if ($bouteille->code_saq !== null) {
            abort(403, 'Impossible de modifier une bouteille provenant du catalogue SAQ.');
        }

        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'quantite'    => 'required|integer|min:0',
            'format'      => 'nullable|string|max:25',
            'pays'        => 'nullable|string|max:100',
            'millesime'  => 'nullable|string|max:10',
            'type'        => 'nullable|string|max:100',
            'prix'        => 'nullable|numeric|min:0',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $bouteille->update($validated);

        return redirect()
            ->route('cellar.show', $cellier->id)
            ->with('success', 'La bouteille a été mise à jour avec succès.');
    }

    /**
     * Affiche toutes les informations détaillées d'une bouteille.
     */
    public function showBottle(Cellier $cellier, Bouteille $bouteille): View
    {
        $this->authorizeCellier($cellier);

        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        // Chercher d'abord par code_saq si disponible, sinon par nom
        $bouteilleCatalogue = null;
        if ($bouteille->code_saq) {
            $bouteilleCatalogue = BouteilleCatalogue::where('code_saQ', $bouteille->code_saq)
                ->with(['typeVin', 'pays', 'region'])
                ->first();
        }

        // Si pas trouvé par code_saq, chercher par nom
        if (!$bouteilleCatalogue) {
            $bouteilleCatalogue = BouteilleCatalogue::where('nom', $bouteille->nom)
                ->with(['typeVin', 'pays', 'region'])
                ->first();
        }

        $donnees = [
            'nom'              => $bouteille->nom,
            'pays'             => $bouteille->pays ?: null,
            'prix'             => $bouteille->prix,
            'quantite'         => $bouteille->quantite,
            'date_ajout'       => $bouteille->created_at,
            'format'           => $bouteille->format,
            'type'             => $bouteille->type, // Read from manual bottle
            'millesime'        => $bouteille->millesime, // Read from manual bottle
            'image'            => null,
            'note_degustation' => $bouteille->note_degustation,
            'rating'           => $bouteille->rating,
            'code_saq'         => $bouteille->code_saq, // Include code_saq from bouteille
        ];

        if ($bouteilleCatalogue) {
            // Override with catalogue data if available (catalogue takes precedence)
            $donnees['type']      = $bouteilleCatalogue->typeVin ? $bouteilleCatalogue->typeVin->nom : $donnees['type'];
            $donnees['millesime'] = $bouteilleCatalogue->millesime ?? $donnees['millesime'];
            $donnees['image']     = $bouteilleCatalogue->image;
            $donnees['url_saq']   = $bouteilleCatalogue->url_saq;
            $donnees['code_saq']  = $bouteilleCatalogue->code_saQ; // Use catalogue code_saq if found

            // Use catalogue volume/format if available
            if ($bouteilleCatalogue->volume) {
                $donnees['format'] = $bouteilleCatalogue->volume;
            }

            // Use catalogue pays if bouteille doesn't have one, or always prefer catalogue if available
            if ($bouteilleCatalogue->pays) {
                $donnees['pays'] = $bouteilleCatalogue->pays->nom;
            }

            // Always set region from catalogue if available
            if ($bouteilleCatalogue->region) {
                $donnees['region'] = $bouteilleCatalogue->region->nom;
            }
        }

        return view('bouteilles.details', [
            'cellier'   => $cellier,
            'bouteille' => $bouteille,
            'donnees'   => $donnees,
        ]);
    }

    public function editNote(Cellier $cellier, Bouteille $bouteille): View
    {
        $this->authorizeCellier($cellier);

        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        return view('bouteilles.edit-note', compact('cellier', 'bouteille'));
    }

    public function updateNote(Request $request, Cellier $cellier, Bouteille $bouteille): RedirectResponse
    {
        $this->authorizeCellier($cellier);

        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        $validated = $request->validate([
            'note_degustation' => 'nullable|string|max:5000',
            'rating'           => 'nullable|integer|min:0|max:5',
        ]);

        // Convertir rating vide ou 0 en null pour supprimer l'évaluation
        $rating = isset($validated['rating']) && $validated['rating'] > 0 ? $validated['rating'] : null;
        $noteDegustation = !empty($validated['note_degustation']) ? $validated['note_degustation'] : null;

        $bouteille->update([
            'note_degustation' => $noteDegustation,
            'rating'           => $rating,
        ]);

        return redirect()
            ->route('bouteilles.show', [$cellier, $bouteille])
            ->with('success', 'Notes de dégustation mises à jour avec succès.');
    }

    public function deleteNote(Cellier $cellier, Bouteille $bouteille)
    {
        $this->authorizeCellier($cellier);

        if ($bouteille->cellier_id !== $cellier->id) {
            abort(403);
        }

        $bouteille->update([
            'note_degustation' => null,
            'rating'           => null,
        ]);

        $message = 'La note de dégustation a été supprimée avec succès.';

        // Si la requête est AJAX, retourner une réponse JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()
            ->route('bouteilles.show', [$cellier, $bouteille])
            ->with('success', $message);
    }

    // Ajout de bouteille du catalogue au cellier via API
    public function ajoutBouteilleApi(Request $request)
    {
        $catalogBottle = BouteilleCatalogue::find($request->bottle_id);

        if (!$catalogBottle) {
            return response()->json([
                'success' => false,
                'message' => 'Bouteille du catalogue non trouvée'
            ], 404);
        }

        $bottleExist = Bouteille::where('cellier_id', $request->cellar_id)
            ->where('nom', $catalogBottle->nom)
            ->first();

        if ($bottleExist) {
            $bottleExist->quantite += $request->quantity;
            // Mettre à jour le code_saq si ce n'est pas déjà défini (pour les anciennes bouteilles)
            if (empty($bottleExist->code_saq) && !empty($catalogBottle->code_saQ)) {
                $bottleExist->code_saq = $catalogBottle->code_saQ;
            }
            $bottleExist->save();

            return response()->json([
                'success' => true,
                'message' => 'Quantité augmentée',
                'data'    => $bottleExist
            ]);
        }

        $new = new Bouteille();
        $new->cellier_id = $request->cellar_id;
        $new->nom        = $catalogBottle->nom;
        $new->pays       = $catalogBottle->pays->nom;
        $new->format     = $catalogBottle->format;
        $new->quantite   = $request->quantity;
        $new->prix       = $catalogBottle->prix;
        $new->code_saq   = $catalogBottle->code_saQ; // Stocker le code SAQ pour identifier les bouteilles importées
        $new->save();

        return response()->json([
            'success' => true,
            'message' => 'Bouteille ajoutée avec succès',
            'data'    => $new
        ]);
    }

    /**
     * Vérifie que le cellier appartient bien à l'utilisateur connecté.
     */
    protected function authorizeCellier(Cellier $cellier): void
    {
        if ($cellier->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
