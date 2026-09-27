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
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('hall_id')->nullable()->after('tenant_id')->constrained()->onDelete('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('hall_id')->nullable()->after('tenant_id')->constrained()->onDelete('cascade');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('hall_id')->nullable()->after('tenant_id')->constrained()->onDelete('cascade');
        });

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->foreignId('hall_id')->nullable()->after('asset_id')->constrained()->onDelete('cascade');
        });
        
        // Data Migration: Assign existing data to the first hall of each tenant
        $tenants = \DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            $firstHall = \DB::table('halls')->where('tenant_id', $tenant->id)->first();
            if ($firstHall) {
                \DB::table('incomes')->where('tenant_id', $tenant->id)->whereNull('hall_id')->update(['hall_id' => $firstHall->id]);
                \DB::table('expenses')->where('tenant_id', $tenant->id)->whereNull('hall_id')->update(['hall_id' => $firstHall->id]);
                \DB::table('assets')->where('tenant_id', $tenant->id)->whereNull('hall_id')->update(['hall_id' => $firstHall->id]);
                \DB::table('asset_assignments')->whereNull('hall_id')->update(['hall_id' => $firstHall->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_and_asset_tables', function (Blueprint $table) {
            //
        });
    }
};
