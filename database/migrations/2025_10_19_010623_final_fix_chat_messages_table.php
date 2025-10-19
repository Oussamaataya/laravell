<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer toutes les tables liées aux messages pour éviter les conflits
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('DROP TABLE IF EXISTS chat_message_reads;');
        DB::statement('DROP TABLE IF EXISTS chat_messages;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Recréer chat_messages
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_room_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->enum('type', ['text', 'image', 'file', 'system'])->default('text');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->unsignedBigInteger('reply_to')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Ajouter les clés étrangères après création
            $table->foreign('chat_room_id')->references('id')->on('chat_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reply_to')->references('id')->on('chat_messages')->onDelete('set null');
            
            $table->index(['chat_room_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('is_deleted');
        });
        
        // Recréer chat_message_reads
        Schema::create('chat_message_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_message_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();
            
            $table->foreign('chat_message_id')->references('id')->on('chat_messages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['chat_message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_message_reads');
        Schema::dropIfExists('chat_messages');
    }
};
