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
        // Vérifie si la table n'existe pas déjà
        if (!Schema::hasTable('declarations')) {
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

                // Statut de paiement
                $table->enum('statut_paiement', ['paye', 'non_paye'])->default('non_paye');

                // Montant déclaré
                $table->decimal('montant', 15, 2)->nullable();

                // Date de déclaration
                $table->date('date_declaration')->nullable();

                // Statut général
                $table->enum('statut', ['brouillon', 'valide', 'validee'])->default('brouillon');

                // Données JSON supplémentaires
                $table->json('data')->nullable();

                $table->timestamps();
            });
        } else {
            // Si la table existe déjà, ajoute uniquement les colonnes manquantes
            Schema::table('declarations', function (Blueprint $table) {
                if (!Schema::hasColumn('declarations', 'statut_paiement')) {
                    $table->enum('statut_paiement', ['paye', 'non_paye'])->default('non_paye')->after('id_contribuable');
                }

                if (!Schema::hasColumn('declarations', 'data')) {
                    $table->json('data')->nullable()->after('statut');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};
