<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use BelongsToTenant, BelongsToHall;

    protected $fillable = [
        'hall_id',
        'transactionable_type',
        'transactionable_id',
        'type',
        'amount',
        'date',
        'description',
    ];

    public function transactionable()
    {
        return $this->morphTo();
    }
}
