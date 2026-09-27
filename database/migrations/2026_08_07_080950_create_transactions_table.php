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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('hall_id')->nullable()->constrained('halls')->nullOnDelete();
            
            $table->morphs('transactionable'); // transactionable_id, transactionable_type
            
            $table->enum('type', ['income', 'expense', 'salary', 'commission']);
            $table->decimal('amount', 15, 2);
            $table->dateTime('date');
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
