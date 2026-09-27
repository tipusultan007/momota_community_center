<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use BelongsToTenant, BelongsToHall;

    protected $fillable = [
        'hall_id',
        'customer_id',
        'total_amount',
        'advance_amount',
        'status',
        'server_payout_status',
        'server_payout_date',
        'server_expense_id',
        'notes',
    ];

    protected $appends = [
        'total_server_cost',
        'excluded_server_cost',
        'all_server_cost',
        'hall_income_amount',
    ];

    public function getTenantAttribute(): BusinessSetting
    {
        return BusinessSetting::get();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function serverExpense()
    {
        return $this->belongsTo(Expense::class, 'server_expense_id');
    }

    public function getTotalServerCostAttribute(): float
    {
        return (float) $this->items->where('is_server_included', true)->sum(function ($item) {
            return (float) ($item->server_count * $item->server_rate);
        });
    }

    public function getExcludedServerCostAttribute(): float
    {
        return (float) $this->items->where('is_server_included', false)->sum(function ($item) {
            return (float) ($item->server_count * $item->server_rate);
        });
    }

    public function getAllServerCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) ($item->server_count * $item->server_rate);
        });
    }

    public function getHallIncomeAmountAttribute(): float
    {
        return max(0, (float) ($this->total_amount - $this->total_server_cost));
    }

    protected static function booted()
    {
        static::deleting(function ($booking) {
            // 0. Delete associated server payout expense if exists
            if ($booking->serverExpense) {
                if ($booking->serverExpense->transaction) {
                    $booking->serverExpense->transaction()->delete();
                }
                $booking->serverExpense()->delete();
            }

            // 1. Delete associated incomes and their transactions
            foreach ($booking->incomes as $income) {
                if ($income->transaction) {
                    $income->transaction()->delete();
                }
                $income->delete();
            }
            \App\Models\Income::where('booking_id', $booking->id)->delete();

            // 2. Delete associated commissions
            $booking->commissions()->delete();

            // 3. Delete associated items
            $booking->items()->delete();
        });
    }
}
