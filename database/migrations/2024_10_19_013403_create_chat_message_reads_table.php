<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['chat_message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_message_reads');
    }
};
