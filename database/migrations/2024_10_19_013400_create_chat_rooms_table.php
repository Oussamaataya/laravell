<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['public', 'private', 'direct'])->default('public');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('room_code', 10)->unique(); // Code unique pour rejoindre
            $table->integer('max_participants')->default(50);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Paramètres avancés
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('room_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
