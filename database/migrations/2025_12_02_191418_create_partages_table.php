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
        Schema::create('partages', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers la bouteille
            $table->foreignId('bouteille_id')
                ->constrained('bouteilles')
                ->onDelete('cascade');
            
            // Token unique pour le lien partageable
            $table->string('token_unique')->unique();
            
            // Date d'expiration du partage (optionnel)
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partages');
    }
};
