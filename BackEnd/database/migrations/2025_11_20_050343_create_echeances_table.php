<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances', function (Blueprint $table) {
            $table->id('id_echeance');

            // Liaison déclaration
            $table->unsignedBigInteger('id_declaration');
            $table->foreign('id_declaration')
                ->references('id_declaration')
                ->on('declarations')
                ->onDelete('cascade');

            // Liaison contribuable
            $table->unsignedBigInteger('id_contribuable');
            $table->foreign('id_contribuable')
                ->references('id_contribuable')
                ->on('contribuables')
                ->onDelete('cascade');

            // Liaison type d’impôt
            $table->unsignedBigInteger('id_type_impot');
            $table->foreign('id_type_impot')
                ->references('id_type_impot')
                ->on('type_impots')
                ->onDelete('cascade');

            // Données financières
            $table->float('montant_du');
            $table->float('penalite')->default(0);
            $table->float('interet')->default(0);

            // Dates
            $table->date('date_limite');
            $table->date('date_paiement')->nullable();

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};
