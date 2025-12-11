<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StatisticsService;
use Carbon\Carbon;
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
            ->withCount('celliers')   // nombre de celliers par usager
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

    /**
     * Page du tableau de bord des statistiques.
     * (La vue chargera les données via AJAX.)
     */
    public function statistics()
    {
        return view('admin.statistics.index');
    }

    /**
     * Endpoint JSON pour les statistiques (filtrage par période).
     */
    public function statisticsData(
        Request $request,
        StatisticsService $statisticsService
    ) {
        [$start, $end] = $this->resolvePeriod($request);

        $data = $statisticsService->getAllStatistics($start, $end);

        return response()->json($data);
    }

    /**
     * Interprète le filtre de période envoyé depuis le front :
     * - day, week, month, year, custom
     */
    protected function resolvePeriod(Request $request): array
    {
        $period = $request->input('period', 'month');
        $now    = Carbon::now();

        switch ($period) {
            case 'day':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;

            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                break;

            case 'year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                break;

            case 'custom':
                $startInput = $request->input('start_date');
                $endInput   = $request->input('end_date');

                if ($startInput && $endInput) {
                    $start = Carbon::parse($startInput)->startOfDay();
                    $end   = Carbon::parse($endInput)->endOfDay();
                } else {
                    // fallback : mois courant si mauvaise saisie
                    $start = $now->copy()->startOfMonth();
                    $end   = $now->copy()->endOfMonth();
                }
                break;

            case 'month':
            default:
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                break;
        }

        return [$start, $end];
    }
}
