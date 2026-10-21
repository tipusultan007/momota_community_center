<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('loans')) {
            Schema::create('loans', function (Blueprint $table) {
                $table->id();
                $table->string('person_name');
                $table->string('phone')->nullable();
                $table->decimal('amount', 15, 2);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->date('date');
                $table->date('due_date')->nullable();
                $table->string('purpose')->nullable();
                $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('date');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('loan_payments')) {
            Schema::create('loan_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->string('payment_method')->default('cash');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('payment_date');
            });
        }

        if (Schema::hasTable('transactions')) {
            try {
                DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `type` ENUM('income', 'expense', 'salary', 'commission', 'loan_taken', 'loan_repayment') NOT NULL");
            } catch (\Exception $e) {
                // Keep resilient if non-mysql or permission issue
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
        Schema::dropIfExists('loans');

        if (Schema::hasTable('transactions')) {
            try {
                DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `type` ENUM('income', 'expense', 'salary', 'commission') NOT NULL");
            } catch (\Exception $e) {
                // Keep resilient
            }
        }
    }
};
