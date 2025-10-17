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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            
            // Informations de base
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            
            // Dates et heures
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            
            // Localisation
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Participants
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            
            // Prix
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('is_free')->default(false);
            
            // En ligne
            $table->boolean('is_online')->default(false);
            $table->string('meeting_link')->nullable();
            
            // Catégorie et impact écologique
            $table->string('category');
            $table->text('eco_impact')->nullable();
            $table->decimal('carbon_footprint', 8, 2)->nullable();
            $table->integer('sustainability_score')->default(0);
            
            // Médias
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            
            // Organisateur
            $table->string('organizer_name');
            $table->string('organizer_email');
            $table->string('organizer_phone')->nullable();
            
            // Statut et options
            $table->enum('status', ['draft', 'active', 'cancelled', 'completed'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->datetime('registration_deadline')->nullable();
            
            // Informations supplémentaires
            $table->json('requirements')->nullable();
            $table->json('what_to_bring')->nullable();
            $table->text('accessibility_info')->nullable();
            
            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['start_date', 'status']);
            $table->index(['category', 'status']);
            $table->index(['city', 'status']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
