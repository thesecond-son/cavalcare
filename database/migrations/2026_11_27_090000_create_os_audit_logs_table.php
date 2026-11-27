<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Append-only audit trail for Caval OS (who did or viewed what, from where).
 * Rows are never updated or deleted by the application.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('os_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);              // e.g. auth.login, booking.viewed
            $table->string('subject_type', 120)->nullable();
            $table->string('subject_id', 64)->nullable();
            $table->json('context')->nullable();       // never health values or full phone numbers
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('at')->useCurrent();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['actor_id', 'at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('os_audit_logs');
    }
};
