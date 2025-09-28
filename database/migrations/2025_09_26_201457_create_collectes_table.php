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
       Schema::create('collectes', function (Blueprint $table) {
     $table->id();
    $table->decimal('montant', 10, 2);
    $table->enum('methode_paiement', ['carte','paypal','virement']);
    $table->enum('statut', ['en_attente','validé','échoué'])->default('en_attente');
    $table->unsignedBigInteger('campagne_id');
    $table->unsignedBigInteger('utilisateur_id');

    $table->foreign('campagne_id')->references('id')->on('compagnes')->onDelete('cascade');
    $table->foreign('utilisateur_id')->references('id')->on('users')->onDelete('cascade');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectes');
    }
};
