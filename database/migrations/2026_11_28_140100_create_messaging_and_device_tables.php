<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Messages, safety alerts, phones for push, and the in-app inbox (backend spec 4.1, 4.7). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kind', 12);                      // care_team | coordinator | support
            $table->foreignUuid('patient_id')->nullable()->constrained('patients')->cascadeOnDelete();
            $table->foreignUuid('care_booking_id')->nullable()->constrained('care_bookings')->nullOnDelete();
            $table->string('title');
            $table->string('status', 10)->default('open');   // open | closed
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['kind', 'last_message_at']);
        });

        Schema::create('thread_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('thread_id')->constrained('threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_in_thread', 14);            // family | professional | coordinator | support
            $table->boolean('read_only')->default(false);    // Viewers in the family circle
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('muted')->default(false);
            $table->timestamps();
            $table->unique(['thread_id', 'user_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable()->unique();  // phone-made id: safe retries
            $table->foreignUuid('thread_id')->constrained('threads')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_kind', 14);               // family | professional | coordinator | support | system
            $table->text('text')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('system_event', 40)->nullable();  // "checked_in", "report_ready"...
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->index(['thread_id', 'sent_at']);
        });

        // "Feel unsafe?" from CavalPro: to the safety team only, never the family.
        Schema::create('safety_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('professional_id')->constrained('professionals');
            $table->foreignUuid('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->boolean('call_me')->default(false);
            $table->text('note')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('app', 10);                       // cavalcare | cavalpro
            $table->string('platform', 10);                  // ios | android
            $table->text('fcm_token');
            $table->string('fcm_token_hash', 64)->unique();  // lookup without indexing the long token
            $table->string('app_version', 20)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'app']);
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('channel', 10);                   // push | email
            $table->string('category', 20);                  // visits | reports | messages | bookings
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'channel', 'category']);
        });

        // Laravel's database notifications: the in-app inbox and badge counts.
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['notifications', 'notification_preferences', 'devices', 'safety_alerts', 'messages', 'thread_participants', 'threads'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
