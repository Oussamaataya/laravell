<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_seen')->nullable();
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->json('permissions')->nullable(); // Permissions spécifiques
            $table->timestamps();
            
            $table->unique(['chat_room_id', 'user_id']);
            $table->index(['user_id', 'is_banned']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_participants');
    }
};
