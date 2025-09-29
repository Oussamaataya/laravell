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
        Schema::create('reclamations', function (Blueprint $table) {
           $table->id();
    $table->string('sujet');
    $table->text('description');
    $table->enum('statut', ['en_attente', 'en_cours', 'traitee'])->default('en_attente');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_reclamation');
    }
};
