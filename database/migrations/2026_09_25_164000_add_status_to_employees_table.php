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
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'status')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('status')->default('active')->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'status')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
