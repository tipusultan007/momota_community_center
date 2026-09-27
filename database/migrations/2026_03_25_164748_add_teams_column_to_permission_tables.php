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
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teamsKey = $columnNames['team_foreign_key'] ?? 'team_id';

        // Add team_id to roles
        if (!Schema::hasColumn($tableNames['roles'], $teamsKey)) {
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamsKey) {
                $table->unsignedBigInteger($teamsKey)->nullable()->after('id');
                $table->index($teamsKey);
            });

            $indexes = collect(Schema::getIndexes($tableNames['roles']))->pluck('name')->toArray();
            
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamsKey, $indexes) {
                if (in_array('roles_name_guard_name_unique', $indexes)) {
                    $table->dropUnique(['name', 'guard_name']);
                }
                $table->unique([$teamsKey, 'name', 'guard_name']);
            });
        }

        // Add team_id to model_has_roles
        if (!Schema::hasColumn($tableNames['model_has_roles'], $teamsKey)) {
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($teamsKey) {
                $table->unsignedBigInteger($teamsKey)->nullable()->after(config('permission.column_names.role_pivot_key') ?? 'role_id');
                $table->index($teamsKey);
                $table->dropPrimary();
                $table->primary([$teamsKey, config('permission.column_names.role_pivot_key') ?? 'role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            });
        }

        // Add team_id to model_has_permissions
        if (!Schema::hasColumn($tableNames['model_has_permissions'], $teamsKey)) {
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($teamsKey) {
                $table->unsignedBigInteger($teamsKey)->nullable()->after(config('permission.column_names.permission_pivot_key') ?? 'permission_id');
                $table->index($teamsKey);
                $table->dropPrimary();
                $table->primary([$teamsKey, config('permission.column_names.permission_pivot_key') ?? 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teamsKey = $columnNames['team_foreign_key'] ?? 'team_id';

        if (Schema::hasColumn($tableNames['roles'], $teamsKey)) {
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamsKey) {
                try {
                    $table->dropUnique([$teamsKey, 'name', 'guard_name']);
                } catch (\Exception $e) {}
                $table->dropColumn($teamsKey);
                $table->unique(['name', 'guard_name']);
            });
        }

        if (Schema::hasColumn($tableNames['model_has_roles'], $teamsKey)) {
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($teamsKey) {
                $table->dropPrimary();
                $table->dropColumn($teamsKey);
                $table->primary([config('permission.column_names.role_pivot_key') ?? 'role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            });
        }

        if (Schema::hasColumn($tableNames['model_has_permissions'], $teamsKey)) {
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($teamsKey) {
                $table->dropPrimary();
                $table->dropColumn($teamsKey);
                $table->primary([config('permission.column_names.permission_pivot_key') ?? 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            });
        }
    }
};
