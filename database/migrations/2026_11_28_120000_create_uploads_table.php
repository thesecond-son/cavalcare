<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Files sent from the apps (credential photos, later health documents).
 * Upload first (POST /uploads), then reference the upload_id. Stored on a
 * private disk; never served publicly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('disk', 20)->default('local');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime', 100);
            $table->unsignedInteger('size_bytes');
            $table->timestamp('attached_at')->nullable();   // set when a record starts using it
            $table->timestamps();

            $table->index(['user_id', 'attached_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
