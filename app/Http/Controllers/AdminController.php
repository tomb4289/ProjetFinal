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

        $query = User::query()->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
        ]);
    }

    /**
     * Détails d'un usager.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

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
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return redirect()
            ->route('admin.users.index')
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

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usager supprimé.');
    }
}
