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
        Schema::table('event_registrations', function (Blueprint $table) {
            // Vérifier et ajouter les colonnes seulement si elles n'existent pas
            if (!Schema::hasColumn('event_registrations', 'ticket_code')) {
                $table->string('ticket_code')->unique()->nullable();
            }
            if (!Schema::hasColumn('event_registrations', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable();
            }
            if (!Schema::hasColumn('event_registrations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable();
            }
            if (!Schema::hasColumn('event_registrations', 'checked_in_by')) {
                $table->foreignId('checked_in_by')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('event_registrations', 'ticket_status')) {
                $table->enum('ticket_status', ['active', 'used', 'cancelled'])->default('active');
            }
        });

        // Ajouter les index séparément
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->index('ticket_code');
            $table->index(['ticket_status', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'ticket_code')) {
                $table->dropIndex(['ticket_code']);
                $table->dropColumn('ticket_code');
            }
            if (Schema::hasColumn('event_registrations', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
            if (Schema::hasColumn('event_registrations', 'checked_in_at')) {
                $table->dropColumn('checked_in_at');
            }
            if (Schema::hasColumn('event_registrations', 'checked_in_by')) {
                $table->dropForeign(['checked_in_by']);
                $table->dropColumn('checked_in_by');
            }
            if (Schema::hasColumn('event_registrations', 'ticket_status')) {
                $table->dropIndex(['ticket_status', 'event_id']);
                $table->dropColumn('ticket_status');
            }
        });
    }
};
