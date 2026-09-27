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
            $table->foreignId('decoration_vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('sound_vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('generator_vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropForeign(['decoration_vendor_id']);
            $table->dropForeign(['sound_vendor_id']);
            $table->dropForeign(['generator_vendor_id']);
            $table->dropColumn(['decoration_vendor_id', 'sound_vendor_id', 'generator_vendor_id']);
        });
    }
};
