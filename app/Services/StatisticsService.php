<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cellier;
use App\Models\Bouteille;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Retourne toutes les statistiques nécessaires au tableau de bord admin.
     *
     * @param  \Carbon\Carbon|null  $start
     * @param  \Carbon\Carbon|null  $end
     * @return array
     */
    public function getAllStatistics(?Carbon $start = null, ?Carbon $end = null): array
    {
        // Si aucune période n’est fournie, on prend les 30 derniers jours
        if (!$start || !$end) {
            $end   = $end ?? Carbon::now();
            $start = $start ?? $end->copy()->subDays(30);
        }

        return [
            'global' => $this->getGlobalStats(),
            'period' => $this->getPeriodStats($start, $end),
            'values' => $this->getValueStats($start, $end),
        ];
    }

    /**
     * Statistiques globales (non filtrées par période).
     *
     * - Nombre total d'usagers
     * - Nombre total de celliers
     * - Nombre total de bouteilles (en unités via quantite)
     * - Moyenne de celliers par usager
     * - Moyenne de bouteilles par cellier
     * - Moyenne de bouteilles par usager
     */
    public function getGlobalStats(): array
    {
        $totalUsers   = User::count();
        $totalCellars = Cellier::count();

        // On considère que "nombre de bouteilles" = somme des quantités
        $totalBottleUnits = (int) Bouteille::sum('quantite');

        $avgCellarsPerUser   = $totalUsers   > 0 ? round($totalCellars / $totalUsers, 2) : 0;
        $avgBottlesPerCellar = $totalCellars > 0 ? round($totalBottleUnits / $totalCellars, 2) : 0;
        $avgBottlesPerUser   = $totalUsers   > 0 ? round($totalBottleUnits / $totalUsers, 2) : 0;

        return [
            'total_users'             => $totalUsers,
            'total_cellars'           => $totalCellars,
            'total_bottles_units'     => $totalBottleUnits,
            'avg_cellars_per_user'    => $avgCellarsPerUser,
            'avg_bottles_per_cellar'  => $avgBottlesPerCellar,
            'avg_bottles_per_user'    => $avgBottlesPerUser,
        ];
    }

    /**
     * Statistiques filtrées sur une période.
     *
     * - Nombre de bouteilles ajoutées dans la période (somme des quantités)
     * - Nombre de nouveaux usagers
     * - Nombre de bouteilles partagées (via table partages)
     */
    public function getPeriodStats(Carbon $start, Carbon $end): array
    {
        // Bouteilles ajoutées dans la période (somme des quantités)
        $bottlesAdded = (int) Bouteille::whereBetween('created_at', [$start, $end])
            ->sum('quantite');

        // Nouveaux usagers créés dans la période
        $newUsers = (int) User::whereBetween('created_at', [$start, $end])->count();

        // Bouteilles partagées = nombre de lignes dans la table 'partages'
        // (on passe par DB directement pour ne pas dépendre d’un éventuel modèle Partage)
        $bottlesShared = (int) DB::table('partages')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return [
            'start'           => $start->toDateString(),
            'end'             => $end->toDateString(),
            'bottles_added'   => $bottlesAdded,
            'bottles_shared'  => $bottlesShared,
            'new_users'       => $newUsers,
        ];
    }

    /**
     * Statistiques de valeur des bouteilles.
     *
     * - Valeur totale de toutes les bouteilles (prix * quantite)
     * - Valeur totale par usager
     * - Valeur totale par cellier
     */
    public function getValueStats(?Carbon $start = null, ?Carbon $end = null): array
    {
        // Valeur globale (tous usagers, tous celliers)
        $bottlesQuery = Bouteille::query();

        if ($start && $end) {
            $bottlesQuery->whereBetween('created_at', [$start, $end]);
        }

        $totalValueRaw = $bottlesQuery
            ->select(DB::raw('SUM(prix * quantite) as total'))
            ->value('total');

        $totalValue = $totalValueRaw ? (float) $totalValueRaw : 0.0;

        // Valeur par usager (join bouteilles -> celliers -> users)
        $perUserQuery = Bouteille::join('celliers', 'celliers.id', '=', 'bouteilles.cellier_id')
            ->join('users', 'users.id', '=', 'celliers.user_id');

        if ($start && $end) {
            $perUserQuery->whereBetween('bouteilles.created_at', [$start, $end]);
        }

        $perUser = $perUserQuery
            ->select(
                'users.id as user_id',
                'users.name',
                DB::raw('SUM(bouteilles.prix * bouteilles.quantite) as total_value')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_value')
            ->get()
            ->map(function ($row) {
                return [
                    'user_id'     => $row->user_id,
                    'name'        => $row->name,
                    'total_value' => (float) $row->total_value,
                ];
            });

        // Valeur par cellier (join bouteilles -> celliers)
        $perCellarQuery = Bouteille::join('celliers', 'celliers.id', '=', 'bouteilles.cellier_id');

        if ($start && $end) {
            $perCellarQuery->whereBetween('bouteilles.created_at', [$start, $end]);
        }

        $perCellar = $perCellarQuery
            ->select(
                'celliers.id as cellier_id',
                'celliers.nom',
                DB::raw('SUM(bouteilles.prix * bouteilles.quantite) as total_value')
            )
            ->groupBy('celliers.id', 'celliers.nom')
            ->orderByDesc('total_value')
            ->get()
            ->map(function ($row) {
                return [
                    'cellier_id'  => $row->cellier_id,
                    'nom'         => $row->nom,
                    'total_value' => (float) $row->total_value,
                ];
            });

        return [
            'total_value' => $totalValue,
            'per_user'    => $perUser,
            'per_cellar'  => $perCellar,
        ];
    }
}
