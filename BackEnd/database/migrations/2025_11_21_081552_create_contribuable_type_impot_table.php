<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contribuable_type_impot', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->unsignedBigInteger('id_contribuable');
            $table->unsignedBigInteger('id_type_impot');

            $table->timestamps();

            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['id_contribuable', 'id_type_impot']);

            // Définition des relations
            $table->foreign('id_contribuable')
                  ->references('id_contribuable')
                  ->on('contribuables')
                  ->onDelete('cascade');

            $table->foreign('id_type_impot')
                  ->references('id_type_impot')
                  ->on('type_impots')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribuable_type_impot');
    }
};
