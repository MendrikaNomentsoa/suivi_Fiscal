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
        Schema::create('litiges', function (Blueprint $table) {
            $table->id('id_Litige');
            $table->string('sujet');
            $table->text('description');
            $table->date('date_ouverture')->nullable();
            $table->string('status')->default('En cours');

            // Clé étrangère vers contribuables
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
        Schema::dropIfExists('litiges');
    }
};
