<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Signalement;

class AdminController extends Controller
{
    /**
     * Liste des usagers avec recherche + pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('q');

        $query = User::query()
            ->withCount('celliers')   // 👈 nombre de celliers par usager
            ->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();

        $nonLus = Signalement::where('is_read', false)->count();

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
            'nonLus' => $nonLus,
        ]);
    }

    /**
     * Détails d'un usager + ses celliers.
     */
    public function show($id)
    {
        $user = User::with(['celliers'])
            ->withCount('celliers')
            ->findOrFail($id);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Activer / désactiver un usager.
     */
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        // Empêcher de se désactiver soi-même
        if (Auth::id() === $user->id) {
            $message = 'Vous ne pouvez pas désactiver votre propre compte.';

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }

            return back()->with('error', $message);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $message = $user->is_active
            ? 'Le compte a été activé avec succès.'
            : 'Le compte a été désactivé avec succès.';

        // Si la requête est AJAX, retourner une réponse JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $user->is_active
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Supprimer un usager.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Empêcher de se supprimer soi-même
        if (Auth::id() === $user->id) {
            $message = 'Vous ne pouvez pas supprimer votre propre compte.';

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('error', $message);
        }

        // Supprimer l'utilisateur (les celliers et listes d'achat seront supprimés en cascade)
        $user->delete();

        $message = 'Compte utilisateur supprimé avec succès.';

        // Si la requête est AJAX, retourner une réponse JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}
