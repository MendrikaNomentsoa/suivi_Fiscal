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
        Schema::create('type_impots', function (Blueprint $table) {
            $table->id('id_type_impot'); // clé primaire BIGINT UNSIGNED
            $table->string('nom'); // nom de l'impôt
            $table->string('periodicite'); // mensuelle / annuelle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_impots');
    }
};
