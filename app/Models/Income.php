<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use BelongsToTenant, BelongsToHall;

    protected $fillable = [
        'hall_id',
        'amount',
        'category', // Keep for backward compatibility or future removal
        'income_category_id',
        'booking_id',
        'description',
        'date',
    ];

    protected static function booted()
    {
        static::saving(function ($income) {
            if (empty($income->category) && $income->incomeCategory) {
                $income->category = $income->incomeCategory->name;
            }
        });
    }

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
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
