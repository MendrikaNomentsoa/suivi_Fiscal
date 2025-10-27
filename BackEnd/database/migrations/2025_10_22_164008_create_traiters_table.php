<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute la migration.
     */
    public function up(): void
    {
        Schema::create('traiters', function (Blueprint $table) {
            //  Les deux clés étrangères
            $table->unsignedBigInteger('id_Agent');
            $table->unsignedBigInteger('id_Litige');

            // Clés étrangères vers les tables principales
            $table->foreign('id_Agent')
                  ->references('id_Agent')
                  ->on('agents')
                  ->onDelete('cascade');

            $table->foreign('id_Litige')
                  ->references('id_Litige')
                  ->on('litiges')
                  ->onDelete('cascade');

            // ✅ Clé primaire composite (empêche les doublons)
            $table->primary(['id_Agent', 'id_Litige']);

            // 🕒 Timestamps pour suivre quand la relation est créée ou mise à jour
            $table->timestamps();
        });
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('traiter');
    }
};
