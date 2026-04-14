<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('bookings')
        ->whereNull('reference')
        ->orWhere('reference', '')
        ->get()
        ->each(function ($booking) {
            DB::table('bookings')
                ->where('id', $booking->id)
                ->update([
                    'reference' => 'CC-' . strtoupper(Str::random(8)),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
