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
        Schema::create('contribuable_type_impot', function (Blueprint $table) {
            $table->id(); // clé primaire auto-incrémentée

            // Clé étrangère vers contribuables
            $table->unsignedBigInteger('id_contribuable');
            $table->foreign('id_contribuable')
                ->references('id_contribuable')
                ->on('contribuables')
                ->onDelete('cascade');

            // Clé étrangère vers type_impots
            $table->unsignedBigInteger('id_type_impot');
            $table->foreign('id_type_impot')
                ->references('id_type_impot')
                ->on('type_impots')
                ->onDelete('cascade');

            $table->timestamps();

            // ✅ Empêcher les doublons (un contribuable ne peut pas être lié deux fois au même type d'impôt)
            $table->unique(['id_contribuable', 'id_type_impot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribuable_type_impot');
    }
};
