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
        Schema::table('halls', function (Blueprint $table) {
            $table->decimal('default_utility_rate', 10, 2)->default(0)->after('default_server_rate');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->decimal('utility_meter_start', 10, 2)->nullable();
            $table->decimal('utility_meter_end', 10, 2)->nullable();
            $table->decimal('utility_unit_price', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            $table->dropColumn('default_utility_rate');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['utility_meter_start', 'utility_meter_end', 'utility_unit_price']);
        });
    }
};
