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
            $table->id('id_Contribuable');
            $table->string('nom');
            $table->string('prenom');
            $table->string('mail')->unique();
            $table->double('telphone')->nullable();
            $table->string('passWord')->unique();
            $table->date('date_inscription');
            $table->unsignedBigInteger('id_Type');
            $table->foreign('id_Type')
                  ->references('id_Type')
                  ->on('categories')
                  ->onDelete('cascade');
            $table->timestamps();
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
