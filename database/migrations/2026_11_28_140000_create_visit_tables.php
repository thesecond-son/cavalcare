<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Visits and everything recorded during them (backend spec 4.6, 13.3).
 *
 * Rules for every visit record (tasks, vitals, medicines, observations,
 * concerns, notes): client_id is made on the phone and unique, so re-sending
 * is harmless (offline sync); recorded_at is the phone's time, synced_at the
 * server's; after the report is submitted rows never change (amendments).
 */
return new class extends Migration
{
    public function up(): void
    {
        // What the caregiver does at each visit; coordinators edit it per patient.
        Schema::create('patient_care_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->unique()->constrained('patients')->cascadeOnDelete();
            $table->json('tasks');                         // [{id, title, kind, due_time?}]
            $table->json('vitals_required')->nullable();   // ["bp","pulse",...]
            $table->json('carer_allowed_vitals')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // One reusable private link per patient: shows the rotating check-in code (spec 13.3).
        Schema::create('check_in_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('code', 16)->unique();           // the short public part of the URL
            $table->text('secret');                         // encrypted; HMAC key for the rotating code
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->string('channel', 12)->nullable();      // whatsapp | sms | copied
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['patient_id', 'revoked_at']);
        });

        Schema::create('visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('care_booking_id')->constrained('care_bookings')->cascadeOnDelete();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('professional_id')->nullable()->constrained('professionals')->nullOnDelete();
            $table->timestamp('scheduled_start');
            $table->timestamp('scheduled_end');
            $table->string('state', 14)->default('scheduled');  // scheduled|en_route|checked_in|checked_out|missed|cancelled
            $table->string('status', 10)->default('stable');    // stable|attention|urgent (server-computed only)
            $table->timestamp('en_route_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('report_submitted_at')->nullable();
            $table->unsignedBigInteger('family_price_kobo');    // snapshot
            $table->unsignedBigInteger('pay_kobo');             // snapshot
            $table->string('pay_state', 10)->default('on_hold'); // on_hold | ready | paid
            $table->string('pay_hold_reason')->nullable();
            $table->timestamps();
            $table->index(['professional_id', 'scheduled_start']);
            $table->index(['patient_id', 'scheduled_start']);
            $table->index(['state', 'scheduled_start']);
        });

        Schema::create('visit_check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('visit_id')->unique()->constrained('visits')->cascadeOnDelete();
            $table->uuid('client_id')->nullable()->unique();
            $table->timestamp('at');                             // phone's time
            $table->string('method', 8);                        // qr | manual
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedInteger('distance_m')->nullable();  // from the patient's home
            $table->string('reason', 20)->nullable();           // phone_off|no_smartphone|wont_scan|no_link|other
            $table->string('reason_note')->nullable();
            $table->boolean('code_valid')->nullable();          // for qr: checked against the time it was scanned
            $table->foreignId('confirmation_asked_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmation_asked_at')->nullable();
            $table->string('confirmation_answer', 6)->nullable(); // yes | no
            $table->foreignId('confirmation_answered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmation_answered_at')->nullable();
            $table->timestamp('coordinator_called_at')->nullable();
            $table->boolean('disputed')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution_note')->nullable();
            $table->timestamps();
        });

        Schema::create('visit_check_outs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('visit_id')->unique()->constrained('visits')->cascadeOnDelete();
            $table->timestamp('at');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedInteger('distance_m')->nullable();
            $table->timestamps();
        });

        $record = function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->unique();
            $table->foreignUuid('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->timestamp('recorded_at');
            $table->timestamp('synced_at');
            $table->boolean('clock_drift_flag')->default(false);
            $table->foreignUuid('recorded_by')->constrained('professionals');
        };

        Schema::create('task_events', function (Blueprint $table) use ($record) {
            $record($table);
            $table->string('task_id', 64);
            $table->string('task_title')->nullable();
            $table->string('state', 10);                         // done | not_done
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('vital_readings', function (Blueprint $table) use ($record) {
            $record($table);
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->string('type', 12);                          // bp|pulse|sugar|spo2|temp|resp|pain
            $table->string('value', 20);                         // "132/84"
            $table->string('unit', 12)->nullable();
            $table->string('context', 12)->nullable();           // fasting | after_food
            $table->boolean('out_of_range')->default(false);     // never sent to the family app
            $table->uuid('recheck_of')->nullable();
            $table->timestamps();
            $table->index(['patient_id', 'type', 'recorded_at']);
        });

        Schema::create('medication_events', function (Blueprint $table) use ($record) {
            $record($table);
            $table->foreignUuid('medication_id')->nullable()->constrained('medications')->nullOnDelete();
            $table->string('medication_name');
            $table->string('outcome', 14);                       // given|assisted|refused|not_available
            $table->string('ate_with', 40)->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('observations', function (Blueprint $table) use ($record) {
            $record($table);
            $table->string('mood', 20)->nullable();
            $table->string('mobility', 20)->nullable();
            $table->string('appetite', 20)->nullable();
            $table->unsignedTinyInteger('water_glasses')->nullable();
            $table->unsignedTinyInteger('pain_score')->nullable();
            $table->json('skin')->nullable();
            $table->timestamps();
        });

        Schema::create('concerns', function (Blueprint $table) use ($record) {
            $record($table);
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->string('kind', 16);                          // swelling|fall|breathing|chest_pain|confusion|wound|other
            $table->string('where_on_body')->nullable();
            $table->text('description')->nullable();
            $table->text('action_taken')->nullable();
            $table->string('severity', 10);                      // attention | urgent
            $table->json('photo_paths')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamps();
            $table->index(['resolved_at', 'severity']);
        });

        Schema::create('care_notes', function (Blueprint $table) use ($record) {
            $record($table);
            $table->text('text');
            $table->timestamps();
        });

        Schema::create('amendments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->unique();
            $table->foreignUuid('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('record_type', 30);
            $table->uuid('record_id');
            $table->string('field', 40);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('reason');
            $table->foreignUuid('by')->constrained('professionals');
            $table->timestamp('at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['amendments', 'care_notes', 'concerns', 'observations', 'medication_events', 'vital_readings',
                  'task_events', 'visit_check_outs', 'visit_check_ins', 'visits', 'check_in_links', 'patient_care_plans'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
