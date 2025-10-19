<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['text', 'image', 'file', 'system'])->default('text');
            $table->string('file_path')->nullable(); // Pour les fichiers/images
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->foreignId('reply_to')->nullable()->constrained('chat_messages')->onDelete('set null');
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamps();
            
            $table->index(['chat_room_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('is_deleted');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
