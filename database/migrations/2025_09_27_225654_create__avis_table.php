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
       Schema::create('avis', function (Blueprint $table) {
        $table->id();
        $table->tinyInteger('note'); // 1 à 5
        $table->text('commentaire')->nullable();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('reclamation_id')->nullable()->constrained('reclamation')->onDelete('cascade'); // 🔗 relation
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
