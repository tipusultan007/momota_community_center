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
            $table->index('date', 'incomes_date_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('date', 'expenses_date_index');
        });

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index('date', 'transactions_date_index');
            });
        }

        if (Schema::hasTable('booking_items')) {
            Schema::table('booking_items', function (Blueprint $table) {
                $table->index(['event_date', 'slot'], 'booking_items_date_slot_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropIndex('incomes_date_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_date_index');
        });

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('transactions_date_index');
            });
        }

        if (Schema::hasTable('booking_items')) {
            Schema::table('booking_items', function (Blueprint $table) {
                $table->dropIndex('booking_items_date_slot_index');
            });
        }
    }
};
