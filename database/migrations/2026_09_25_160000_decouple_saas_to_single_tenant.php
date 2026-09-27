<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Create business_settings table
        if (!Schema::hasTable('business_settings')) {
            Schema::create('business_settings', function (Blueprint $table) {
                $table->id();
                $table->string('company_name')->default('কনভেনশন হল ম্যানেজমেন্ট');
                $table->string('slug')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address', 500)->nullable();
                $table->string('logo_path')->nullable();
                $table->text('invoice_conditions')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        // 2. Transfer existing tenant data into business_settings
        if (Schema::hasTable('tenants')) {
            $existingTenant = DB::table('tenants')->first();
            if ($existingTenant) {
                DB::table('business_settings')->insert([
                    'id' => 1,
                    'company_name' => $existingTenant->name,
                    'slug' => $existingTenant->slug,
                    'phone' => $existingTenant->phone,
                    'address' => $existingTenant->address,
                    'logo_path' => $existingTenant->logo_path,
                    'invoice_conditions' => $existingTenant->invoice_conditions,
                    'settings' => $existingTenant->settings,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. List of all tables having tenant_id
        $tables = [
            'asset_assignments',
            'assets',
            'bookings',
            'commissions',
            'customers',
            'employees',
            'expense_categories',
            'expenses',
            'halls',
            'income_categories',
            'incomes',
            'salaries',
            'sms_logs',
            'transactions',
            'users',
            'vendors',
        ];

        // 4. Drop foreign key constraints and tenant_id columns
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Drop FK
                try {
                    Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                        $table->dropForeign("{$tableName}_tenant_id_foreign");
                    });
                } catch (\Throwable $e) {
                    try {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->dropForeign(['tenant_id']);
                        });
                    } catch (\Throwable $e2) {}
                }

                // Drop Column
                if (Schema::hasColumn($tableName, 'tenant_id')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('tenant_id');
                    });
                }
            }
        }

        // 5. Drop subscription_histories table
        if (Schema::hasTable('subscription_histories')) {
            try {
                Schema::table('subscription_histories', function (Blueprint $table) {
                    $table->dropForeign('subscription_histories_tenant_id_foreign');
                });
            } catch (\Throwable $e) {}

            Schema::dropIfExists('subscription_histories');
        }

        // 6. Drop tenants table
        if (Schema::hasTable('tenants')) {
            Schema::dropIfExists('tenants');
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-reversible architectural decoupling
    }
};
