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
        if (!Schema::hasColumn('declarations', 'statut_paiement')) {
            Schema::table('declarations', function (Blueprint $table) {
                $table->enum('statut_paiement', ['paye', 'non_paye'])
                      ->default('non_paye')
                      ->after('statut');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('declarations', 'statut_paiement')) {
            Schema::table('declarations', function (Blueprint $table) {
                $table->dropColumn('statut_paiement');
            });
        }
    }
};
