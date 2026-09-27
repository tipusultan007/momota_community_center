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
            $table->string('address')->nullable()->after('name');
            $table->string('phone')->nullable()->after('address');
        });

        // Initialize existing halls with tenant-level settings if they exist
        $tenants = \DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            $settings = json_decode($tenant->settings, true);
            \DB::table('halls')->where('tenant_id', $tenant->id)->update([
                'address' => $settings['address'] ?? null,
                'phone' => $settings['phone'] ?? null,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            //
        });
    }
};
