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
        Schema::table('commentaires', function (Blueprint $table) {
            $table->string('tone')->nullable()->after('contenu');
            $table->boolean('has_bad_words')->default(false)->after('tone');
            $table->json('bad_words')->nullable()->after('has_bad_words');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropColumn(['tone', 'has_bad_words', 'bad_words']);
        });
    }
};
