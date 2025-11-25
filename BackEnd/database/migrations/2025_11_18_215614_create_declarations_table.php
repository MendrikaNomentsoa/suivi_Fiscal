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
            $table->id('id_declaration'); // clé primaire

            // Clé étrangère vers type_impots
            $table->unsignedBigInteger('id_type_impot');
            $table->foreign('id_type_impot')
                ->references('id_type_impot')
                ->on('type_impots')
                ->onDelete('cascade');

            // Clé étrangère vers contribuables
            $table->unsignedBigInteger('id_contribuable');
            $table->foreign('id_contribuable')
                ->references('id_contribuable')
                ->on('contribuables')
                ->onDelete('cascade');

            // Autres colonnes
            $table->decimal('montant', 15, 2)->nullable(); // montant déclaré
            $table->date('date_declaration')->nullable();

            // ✅ Modifier le statut pour correspondre à votre contrôleur
            $table->enum('statut', ['brouillon', 'valide', 'validee'])->default('brouillon');

            // ✅ AJOUTER LA COLONNE DATA (TRÈS IMPORTANT)
            $table->json('data')->nullable(); // Stocke les données du formulaire en JSON

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
