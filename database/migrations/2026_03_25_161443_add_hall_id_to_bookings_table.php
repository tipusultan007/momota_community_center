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
            $table->foreignId('hall_id')->nullable()->after('tenant_id')->constrained()->onDelete('cascade');
        });

        // Data Migration: Assign existing bookings to the first hall
        $tenants = \DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            $firstHall = \DB::table('halls')->where('tenant_id', $tenant->id)->first();
            if ($firstHall) {
                \DB::table('bookings')->where('tenant_id', $tenant->id)->whereNull('hall_id')->update(['hall_id' => $firstHall->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
