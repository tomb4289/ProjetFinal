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
        Schema::table('bouteille_catalogue', function (Blueprint $table) {
            $table->foreignId('id_region')->nullable()->after('id_pays')->constrained('regions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bouteille_catalogue', function (Blueprint $table) {
            $table->dropForeign(['id_region']);
            $table->dropColumn('id_region');
        });
    }
};
