<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'type',
        'phone',
        'email',
        'address',
        'commission_rate',
        'is_active',
    ];
}
