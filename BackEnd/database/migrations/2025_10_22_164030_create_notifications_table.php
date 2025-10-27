<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute la migration (crée la table notifications).
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notif');
            $table->string('design');         // titre ou objet de la notification
            $table->string('statut')->default('non_lu');
            $table->dateTime('date_envoi')->nullable();
            $table->dateTime('date_lecture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
