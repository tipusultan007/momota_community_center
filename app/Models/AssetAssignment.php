<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetAssignment extends Model
{
    use HasFactory, BelongsToTenant, BelongsToHall;

    protected $fillable = [
        'hall_id',
        'booking_item_id',
        'asset_id',
        'quantity_out',
        'quantity_in',
        'damaged_quantity',
        'notes'
    ];

    public function bookingItem()
    {
        return $this->belongsTo(BookingItem::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
