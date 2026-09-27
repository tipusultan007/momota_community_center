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
            $table->string('server_payout_status', 20)->default('unpaid')->after('status');
            $table->dateTime('server_payout_date')->nullable()->after('server_payout_status');
            $table->foreignId('server_expense_id')->nullable()->constrained('expenses')->nullOnDelete()->after('server_payout_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['server_expense_id']);
            $table->dropColumn(['server_payout_status', 'server_payout_date', 'server_expense_id']);
        });
    }
};
