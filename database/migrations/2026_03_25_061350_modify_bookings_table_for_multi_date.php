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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['hall_id']);
            $table->dropColumn(['hall_id', 'event_date', 'slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('hall_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('event_date')->nullable();
            $table->string('slot')->nullable();
        });
    }
};
