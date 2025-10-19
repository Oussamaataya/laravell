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
            $table->string('event_type')->nullable()->after('image');
            $table->text('ai_description')->nullable()->after('event_type');
            $table->json('ai_hashtags')->nullable()->after('ai_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'ai_description', 'ai_hashtags']);
        });
    }
};
