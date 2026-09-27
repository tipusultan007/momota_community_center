<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory, BelongsToTenant, BelongsToHall;

    protected $fillable = [
        'hall_id',
        'name',
        'total_stock',
        'available_stock',
        'description'
    ];

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
