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
        Schema::create('echeances', function (Blueprint $table) {
            $table->id('id_Echeance');
            $table->float('montant');
            $table->date('date_limite');
            $table->float('penalite');
            
            // Clé étrangère vers Contribuable
            $table->unsignedBigInteger('id_Contribuable');
            $table->foreign('id_Contribuable')
                  ->references('id_Contribuable')
                  ->on('contribuables')
                  ->onDelete('cascade');

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};
