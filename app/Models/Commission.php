<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'vendor_id',
        'booking_id',
        'amount',
        'net_payout',
        'payout_status',
        'status',
        'notes',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
