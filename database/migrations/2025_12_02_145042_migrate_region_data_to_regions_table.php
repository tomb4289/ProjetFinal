<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Region;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Récupérer toutes les régions uniques de bouteille_catalogue
        $regions = DB::table('bouteille_catalogue')
            ->select('region')
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->get();

        // Créer les régions dans la table regions
        foreach ($regions as $regionData) {
            Region::firstOrCreate(
                ['nom' => $regionData->region],
                ['date_creation' => now()]
            );
        }

        // Mettre à jour bouteille_catalogue avec les id_region
        $bouteilles = DB::table('bouteille_catalogue')
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->get();

        foreach ($bouteilles as $bouteille) {
            $region = Region::where('nom', $bouteille->region)->first();
            if ($region) {
                DB::table('bouteille_catalogue')
                    ->where('id', $bouteille->id)
                    ->update(['id_region' => $region->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Récupérer toutes les bouteilles avec id_region
        $bouteilles = DB::table('bouteille_catalogue')
            ->whereNotNull('id_region')
            ->join('regions', 'bouteille_catalogue.id_region', '=', 'regions.id')
            ->select('bouteille_catalogue.id', 'regions.nom')
            ->get();

        // Restaurer la colonne region
        foreach ($bouteilles as $bouteille) {
            DB::table('bouteille_catalogue')
                ->where('id', $bouteille->id)
                ->update(['region' => $bouteille->nom]);
        }
    }
};
