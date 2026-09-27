<?php

namespace App\Traits;

use App\Models\Scopes\HallScope;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToHall
{
    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    protected static function bootBelongsToHall()
    {
        static::addGlobalScope(new HallScope);

        static::creating(function ($model) {
            if (session()->has('active_hall_id')) {
                $model->hall_id = session()->get('active_hall_id');
            }
        });
    }

    /**
     * Scope a query to only include models belonging to the active hall.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForActiveHall(Builder $query)
    {
        return $query->where('hall_id', session()->get('active_hall_id'));
    }
}
