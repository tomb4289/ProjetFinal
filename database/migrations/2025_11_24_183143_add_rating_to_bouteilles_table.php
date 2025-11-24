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
        Schema::table('bouteilles', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('note_degustation')->comment('Note de 0 à 10 étoiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bouteilles', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
