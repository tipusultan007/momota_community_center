<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use BelongsToTenant, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'nid',
        'address',
    ];
}
