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
        Schema::create('declarations', function (Blueprint $table) {
            $table->id('id_declaration'); // id primaire

            // Clé étrangère vers type_impots
            $table->foreignId('id_type_impot')
                  ->constrained('type_impots', 'id_type_impot') // nom exact de la table et de la colonne
                  ->onDelete('cascade');

            // Clé étrangère vers contribuables
            $table->foreignId('id_contribuable')
                  ->constrained('contribuables', 'id_contribuable') // nom exact de la table et colonne
                  ->onDelete('cascade');

            // Autres colonnes spécifiques à la déclaration
            $table->decimal('montant', 15, 2)->nullable(); // montant déclaré
            $table->date('date_declaration')->nullable();
            $table->string('statut')->default('en attente'); // en attente / validée / rejetée

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};
