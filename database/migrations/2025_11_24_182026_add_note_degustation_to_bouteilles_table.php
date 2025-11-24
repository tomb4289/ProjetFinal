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
            $table->text('note_degustation')->nullable()->after('prix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bouteilles', function (Blueprint $table) {
            $table->dropColumn('note_degustation');
        });
    }
};
