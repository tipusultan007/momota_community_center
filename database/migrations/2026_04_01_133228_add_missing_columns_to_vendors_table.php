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
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'address')) {
                $table->text('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('vendors', 'phone')) {
                $table->string('phone')->nullable()->after('type');
            }
            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('vendors', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('commission_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone', 'email', 'is_active']);
        });
    }
};
