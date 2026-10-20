<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
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
