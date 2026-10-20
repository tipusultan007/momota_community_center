<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that previously carried a hall_id column.
     *
     * @var array<int, string>
     */
    private array $hallTables = [
        'asset_assignments',
        'assets',
        'booking_items',
        'bookings',
        'commissions',
        'expenses',
        'incomes',
        'loans',
        'transactions',
    ];

    /**
     * Composite indexes that referenced hall_id.
     *
     * @var array<string, string>
     */
    private array $hallIndexes = [
        'incomes' => 'incomes_hall_id_date_index',
        'expenses' => 'expenses_hall_id_date_index',
        'transactions' => 'transactions_hall_id_date_index',
        'booking_items' => 'booking_items_hall_date_slot_index',
    ];

    public function up(): void
    {
        // 1. Give business_settings the venue fields that used to live on halls.
        if (Schema::hasTable('business_settings')) {
            Schema::table('business_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('business_settings', 'capacity')) {
                    $table->integer('capacity')->default(500)->after('logo_path');
                }
                if (!Schema::hasColumn('business_settings', 'price_per_slot')) {
                    $table->decimal('price_per_slot', 12, 2)->default(30000)->after('capacity');
                }
                if (!Schema::hasColumn('business_settings', 'default_server_rate')) {
                    $table->decimal('default_server_rate', 10, 2)->default(500)->after('price_per_slot');
                }
            });
        }

        // 2. Copy the existing hall profile into the single business settings row.
        if (Schema::hasTable('halls') && Schema::hasTable('business_settings')) {
            $hall = DB::table('halls')->orderBy('id')->first();

            if ($hall) {
                $business = DB::table('business_settings')->orderBy('id')->first();

                if ($business) {
                    DB::table('business_settings')
                        ->where('id', $business->id)
                        ->update([
                            'capacity' => $hall->capacity ?? 500,
                            'price_per_slot' => $hall->price_per_slot ?? 30000,
                            'default_server_rate' => $hall->default_server_rate ?? 500,
                        ]);
                } else {
                    DB::table('business_settings')->insert([
                        'company_name' => $hall->name ?? 'মমতা কমিউনিটি সেন্টার',
                        'address' => $hall->address ?? null,
                        'phone' => $hall->phone ?? null,
                        'capacity' => $hall->capacity ?? 500,
                        'price_per_slot' => $hall->price_per_slot ?? 30000,
                        'default_server_rate' => $hall->default_server_rate ?? 500,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Drop hall indexes and foreign keys before removing the columns.
        foreach ($this->hallIndexes as $table => $index) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'hall_id')) {
                try {
                    Schema::table($table, function (Blueprint $blueprint) use ($index) {
                        $blueprint->dropIndex($index);
                    });
                } catch (\Throwable $e) {
                    // Index may not exist on this installation.
                }
            }
        }

        foreach ($this->hallTables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'hall_id')) {
                continue;
            }

            if (DB::getDriverName() === 'mysql') {
                // Look up the real constraint names; older installs do not always
                // follow Laravel's default naming convention.
                $foreignKeys = DB::select(
                    'select CONSTRAINT_NAME as name from information_schema.KEY_COLUMN_USAGE
                     where TABLE_SCHEMA = database() and TABLE_NAME = ? and COLUMN_NAME = ?
                     and REFERENCED_TABLE_NAME is not null',
                    [$table, 'hall_id']
                );

                foreach ($foreignKeys as $foreignKey) {
                    try {
                        Schema::table($table, function (Blueprint $blueprint) use ($foreignKey) {
                            $blueprint->dropForeign($foreignKey->name);
                        });
                    } catch (\Throwable $e) {
                        // Ignore constraints that were already removed.
                    }
                }
            } else {
                try {
                    Schema::table($table, function (Blueprint $blueprint) use ($table) {
                        $blueprint->dropForeign($table . '_hall_id_foreign');
                    });
                } catch (\Throwable $e) {
                    // SQLite and some legacy installations have no named foreign key.
                }
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('hall_id');
            });
        }

        // 4. Halls are gone: there is only one venue.
        Schema::dropIfExists('halls');

        // 5. Remove the obsolete permission if the tables exist.
        try {
            if (Schema::hasTable('permissions')) {
                $permissionIds = DB::table('permissions')->where('name', 'manage-halls')->pluck('id');

                if ($permissionIds->isNotEmpty()) {
                    if (Schema::hasTable('role_has_permissions')) {
                        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                    }
                    if (Schema::hasTable('model_has_permissions')) {
                        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                    }
                    DB::table('permissions')->whereIn('id', $permissionIds)->delete();
                }
            }
        } catch (\Throwable $e) {
            // Permission tables are optional in some installations.
        }
    }

    public function down(): void
    {
        // This migration is intentionally irreversible: halls no longer exist
        // as a product concept. Historical hall data is preserved in backups.
    }
};
