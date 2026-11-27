<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One-time sign-in codes for the apps (email now, SMS once a provider is chosen).
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 10);                 // email | sms | whatsapp
            $table->string('destination', 255);            // lower-cased email or E.164 phone
            $table->string('code_hash', 64);               // HMAC-SHA256, never the code itself
            $table->string('purpose', 20)->default('login');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['destination', 'purpose', 'consumed_at']);
            $table->index('expires_at');
        });

        // The code a family gets after paying on the website, used once in the
        // Cavalcare app to open their account with the booking already filled in.
        Schema::create('app_link_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_booking_id')->constrained('pre_bookings')->cascadeOnDelete();
            $table->string('code_hash', 64)->unique();      // HMAC-SHA256 of the normalised code
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['pre_booking_id', 'redeemed_at', 'revoked_at']);
        });

        // Which app account a website booking belongs to, once linked.
        Schema::table('pre_bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pre_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::dropIfExists('app_link_codes');
        Schema::dropIfExists('otp_codes');
    }
};
