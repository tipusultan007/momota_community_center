<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teamsKey = $columnNames['team_foreign_key'] ?? 'team_id';

        if (Schema::hasColumn($tableNames['model_has_roles'], $teamsKey)) {
            DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` MODIFY `{$teamsKey}` BIGINT UNSIGNED NOT NULL DEFAULT 0");
        }

        if (Schema::hasColumn($tableNames['model_has_permissions'], $teamsKey)) {
            DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` MODIFY `{$teamsKey}` BIGINT UNSIGNED NOT NULL DEFAULT 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
