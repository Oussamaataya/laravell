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
                $table->string('ticket_code')->unique()->nullable()->after('status');
            }
            if (!Schema::hasColumn('event_registrations', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('ticket_code');
            }
            if (!Schema::hasColumn('event_registrations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('qr_code_path');
            }
            if (!Schema::hasColumn('event_registrations', 'checked_in_by')) {
                $table->foreignId('checked_in_by')->nullable()->constrained('users')->after('checked_in_at');
            }
            if (!Schema::hasColumn('event_registrations', 'ticket_status')) {
                $table->enum('ticket_status', ['active', 'used', 'cancelled'])->default('active')->after('checked_in_by');
            }

            // Ajouter des index pour améliorer les performances
            if (!Schema::hasColumn('event_registrations', 'ticket_code')) {
                $table->index('ticket_code');
            }
            $table->index(['ticket_status', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_code',
                'qr_code_path', 
                'checked_in_at',
                'checked_in_by',
                'ticket_status'
            ]);
        });
    }
};
