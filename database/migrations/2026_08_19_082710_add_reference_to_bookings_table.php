<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('reference', 30)->nullable()->after('id');
        });

        /*
        |--------------------------------------------------------------------------
        | Generate references for existing bookings
        |--------------------------------------------------------------------------
        */

        $bookings = DB::table('bookings')
            ->whereNull('reference')
            ->orWhere('reference', '')
            ->orderBy('id')
            ->get();

        foreach ($bookings as $booking) {
            do {
                $reference = 'BK-' . strtoupper(Str::random(8));
            } while (
                DB::table('bookings')
                    ->where('reference', $reference)
                    ->exists()
            );

            DB::table('bookings')
                ->where('id', $booking->id)
                ->update([
                    'reference' => $reference,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Make reference required and unique
        |--------------------------------------------------------------------------
        */

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('reference', 30)
                ->nullable(false)
                ->unique()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_reference_unique');
            $table->dropColumn('reference');
        });
    }
};