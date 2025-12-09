<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
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
            return
                back()
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return
            back()
            ->with('success', 'Statut de l’usager mis à jour.');
    }

    /**
     * Supprimer un usager.
     */

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Empêcher de se supprimer soi-même
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // On considère la "suppression" comme une désactivation
        $user->is_active = false;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Compte utilisateur désactivé.');
    }
}
