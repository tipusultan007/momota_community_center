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
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('কনভেনশন হল ম্যানেজমেন্ট');
            $table->string('slug')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('logo_path')->nullable();
            $table->integer('capacity')->default(500);
            $table->decimal('price_per_slot', 12, 2)->default(30000);
            $table->decimal('default_server_rate', 10, 2)->default(500);
            $table->text('invoice_conditions')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
