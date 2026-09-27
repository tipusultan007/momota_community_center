<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'capacity',
        'description',
        'price_per_slot',
        'default_server_rate',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_slot' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
