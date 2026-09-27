<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'logo_path',
        'invoice_conditions',
        'plan',
        'is_active',
        'settings',
        'trial_ends_at',
        'subscription_ends_at',
        'stripe_id',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function halls()
    {
        return $this->hasMany(Hall::class);
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

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }
}
