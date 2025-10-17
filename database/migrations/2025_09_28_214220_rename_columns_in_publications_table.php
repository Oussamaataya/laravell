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
        Schema::table('publications', function (Blueprint $table) {
            // Rename English columns to French
            $table->renameColumn('title', 'titre');
            $table->renameColumn('content', 'contenu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            // Rename French columns back to English
            $table->renameColumn('titre', 'title');
            $table->renameColumn('contenu', 'content');
        });
    }
};
