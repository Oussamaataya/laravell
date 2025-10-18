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
        Schema::table('collectes', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('statut');
            $table->string('stripe_payment_intent')->nullable()->after('stripe_session_id');
            $table->text('message')->nullable()->after('stripe_payment_intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'stripe_payment_intent', 'message']);
        });
    }
};
