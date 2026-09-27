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
        Schema::table('asset_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_assignments', 'tenant_id')) {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                } else {
                    $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');
                }
            }
            if (!Schema::hasColumn('asset_assignments', 'hall_id')) {
                $table->foreignId('hall_id')->after('tenant_id')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['hall_id']);
            $table->dropColumn(['tenant_id', 'hall_id']);
        });
    }
};
