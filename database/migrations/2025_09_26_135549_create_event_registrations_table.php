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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('status')->default('confirmed'); // confirmed, cancelled, waiting_list
            $table->text('notes')->nullable();
            $table->datetime('registered_at')->useCurrent();
            $table->datetime('cancelled_at')->nullable();
            
            $table->timestamps();
            
            // Éviter les doublons
            $table->unique(['event_id', 'user_id']);
            
            // Index
            $table->index(['event_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
