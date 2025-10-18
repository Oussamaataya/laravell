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
        // Modifier la colonne methode_paiement pour ajouter 'stripe'
        DB::statement("ALTER TABLE collectes MODIFY COLUMN methode_paiement ENUM('carte', 'paypal', 'virement', 'stripe') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancienne version sans 'stripe'
        DB::statement("ALTER TABLE collectes MODIFY COLUMN methode_paiement ENUM('carte', 'paypal', 'virement') NOT NULL");
    }
};
