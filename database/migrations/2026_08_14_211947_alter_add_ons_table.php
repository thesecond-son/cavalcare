<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('add_ons', function (Blueprint $table) {
            // Short description shown under the add-on name on the booking form
            $table->text('description')->nullable()->after('name');

            // Some add-ons have variable pricing (e.g. "from ₦40,000")
            // This flag lets the UI show "from" instead of a fixed price
            $table->boolean('price_from')->default(false)->after('price')
                  ->comment('True = price is a minimum, show "from ₦X"');
        });
    }

    public function down(): void
    {
        Schema::table('add_ons', function (Blueprint $table) {
            $table->dropColumn(['description', 'price_from']);
        });
    }
};