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
        Schema::create('contribuables', function (Blueprint $table) {
            $table->id('id_contribuable');                  // Clé primaire
            $table->string('nif')->unique();                // NIF obligatoire et unique
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique()->nullable();  // Peut être null
            $table->string('telephone', 20)->nullable();
            $table->string('password')->nullable();
                    // Mot de passe hashé
            $table->date('date_inscription')->nullable();   // Date d’inscription
            $table->timestamps();

            // Index utile pour les recherches fréquentes par NIF
            $table->index('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribuables');
    }
};
