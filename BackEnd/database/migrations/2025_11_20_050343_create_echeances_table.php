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
$table->id('id_echeance');

// Foreign key correcte vers type_impots.id_type_impot
$table->unsignedBigInteger('id_type_impot');
$table->foreign('id_type_impot')
->references('id_type_impot')
->on('type_impots')
->onDelete('cascade');

// Période de déclaration
$table->string('periode'); // ex: "2025-01" ou "2025"

// Date limite légale
$table->date('date_limite');

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
