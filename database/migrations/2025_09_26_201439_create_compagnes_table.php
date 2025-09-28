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
    Schema::create('compagnes', function (Blueprint $table) {
    $table->id();
    $table->string('nom');
    $table->text('description');
    $table->decimal('montant_objectif', 10, 2);
    $table->decimal('montant_actuel', 10, 2)->default(0);
    $table->date('date_debut');
    $table->date('date_fin');
    $table->enum('statut', ['brouillon','active','terminée','annulée'])->default('brouillon');
    $table->unsignedBigInteger('organisateur_id');
    $table->foreign('organisateur_id')->references('id')->on('users')->onDelete('cascade');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compagnes');
    }
};
