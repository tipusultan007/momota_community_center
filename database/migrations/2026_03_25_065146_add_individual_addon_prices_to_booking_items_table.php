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
        Schema::table('booking_items', function (Blueprint $table) {
            $table->decimal('ac_price', 12, 2)->default(0)->after('is_ac');
            $table->decimal('sound_price', 12, 2)->default(0)->after('extra_sound');
            $table->decimal('generator_price', 12, 2)->default(0)->after('extra_generator');
            $table->decimal('decoration_price', 12, 2)->default(0)->after('extra_decoration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['ac_price', 'sound_price', 'generator_price', 'decoration_price']);
        });
    }
};
