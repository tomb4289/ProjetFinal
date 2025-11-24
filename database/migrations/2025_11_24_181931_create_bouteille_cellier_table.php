<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bouteille_cellier', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers le cellier
            $table->foreignId('id_cellier')
                ->constrained('celliers')
                ->onDelete('cascade');
            
            // Clé étrangère vers la bouteille du catalogue
            $table->foreignId('id_bouteille_catalogue')
                ->constrained('bouteille_catalogue')
                ->onDelete('cascade');
            
            // Quantité de bouteilles
            $table->unsignedInteger('quantite')->default(1);
            
            // Notes de dégustation (texte libre)
            $table->text('note_degustation')->nullable();
            
            // Date d'ajout au cellier
            $table->dateTime('date_ajout')->useCurrent();
            
            // Date d'ouverture (optionnelle)
            $table->date('date_ouverture')->nullable();
            
            // Indique si la bouteille a été achetée mais n'est pas dans le catalogue
            $table->boolean('achetee_non_listee')->default(false);
            
            // Pas de timestamps selon le modèle
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouteille_cellier');
    }
};
