<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * The "boot" method of the model.
     * In Non-SaaS mode, all data belongs to the single organization.
     */
    protected static function bootBelongsToTenant(): void
    {
        // No-op in Non-SaaS mode
    }

    /**
     * Scope a query to only include models belonging to the organization.
     */
    public function scopeForTenant(Builder $query): Builder
    {
        return $query;
    }
}
