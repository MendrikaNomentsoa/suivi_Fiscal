<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute la migration (crée la table notifier).
     */
    public function up(): void
    {
        Schema::create('notifier', function (Blueprint $table) {
            $table->unsignedBigInteger('id_notif');         // FK vers notifications
            $table->unsignedBigInteger('id_Agent');         // FK vers agents
            $table->unsignedBigInteger('id_Contribuable');  // FK vers contribuables
            $table->text('message')->nullable();            // Message personnalisé
            $table->timestamps();

            // Clés étrangères
            $table->foreign('id_notif')
                  ->references('id_notif')
                  ->on('notifications')
                  ->onDelete('cascade');

            $table->foreign('id_Agent')
                  ->references('id_Agent')
                  ->on('agents')
                  ->onDelete('cascade');

            $table->foreign('id_Contribuable')
                  ->references('id_Contribuable')
                  ->on('contribuables')
                  ->onDelete('cascade');

            // Clé primaire composite (évite les doublons)
            $table->primary(['id_notif', 'id_Agent', 'id_Contribuable']);
        });
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifier');
    }
};
