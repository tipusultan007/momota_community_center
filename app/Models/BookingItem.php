<?php

namespace App\Models;

use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use BelongsToHall;

    protected $fillable = [
        'booking_id',
        'hall_id',
        'event_date',
        'slot',
        'event_type',
        'guest_count',
        'table_count',
        'server_count',
        'server_rate',
        'is_server_included',
        'base_price',
        'is_ac',
        'ac_price',
        'extra_sound',
        'sound_price',
        'extra_generator',
        'generator_price',
        'extra_decoration',
        'decoration_price',
        'sub_total',
        'decoration_vendor_id',
        'sound_vendor_id',
        'generator_vendor_id',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_server_included' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function decorationVendor()
    {
        return $this->belongsTo(Vendor::class, 'decoration_vendor_id');
    }

    public function soundVendor()
    {
        return $this->belongsTo(Vendor::class, 'sound_vendor_id');
    }

    public function generatorVendor()
    {
        return $this->belongsTo(Vendor::class, 'generator_vendor_id');
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
