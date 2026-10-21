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
        try {
            if (!Schema::hasTable('business_settings')) {
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
        } catch (\Throwable $e) {
            // Table already exists or driver quirk - continue gracefully
        }

        try {
            if (Schema::hasTable('business_settings')) {
                Schema::table('business_settings', function (Blueprint $table) {
                    if (!Schema::hasColumn('business_settings', 'company_name')) {
                        $table->string('company_name')->default('কনভেনশন হল ম্যানেজমেন্ট');
                    }
                    if (!Schema::hasColumn('business_settings', 'slug')) {
                        $table->string('slug')->nullable();
                    }
                    if (!Schema::hasColumn('business_settings', 'phone')) {
                        $table->string('phone')->nullable();
                    }
                    if (!Schema::hasColumn('business_settings', 'email')) {
                        $table->string('email')->nullable();
                    }
                    if (!Schema::hasColumn('business_settings', 'address')) {
                        $table->string('address', 500)->nullable();
                    }
                    if (!Schema::hasColumn('business_settings', 'logo_path')) {
                        $table->string('logo_path')->nullable();
                    }
                    if (!Schema::hasColumn('business_settings', 'capacity')) {
                        $table->integer('capacity')->default(500);
                    }
                    if (!Schema::hasColumn('business_settings', 'price_per_slot')) {
                        $table->decimal('price_per_slot', 12, 2)->default(30000);
                    }
                    if (!Schema::hasColumn('business_settings', 'default_server_rate')) {
                        $table->decimal('default_server_rate', 10, 2)->default(500);
                    }
                    if (!Schema::hasColumn('business_settings', 'invoice_conditions')) {
                        $table->text('invoice_conditions')->nullable();
                    }
                    if (!Schema::hasColumn('business_settings', 'settings')) {
                        $table->json('settings')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            // Safe to ignore if columns already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
