<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
    use BelongsToTenant, InteractsWithMedia;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'designation',
        'salary_amount',
        'join_date',
        'is_active',
        'status',
    ];

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
}
