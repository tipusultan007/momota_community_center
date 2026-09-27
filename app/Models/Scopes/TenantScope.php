<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * In Non-SaaS mode, all data belongs to the single organization.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // No-op in Non-SaaS mode
    }
}
