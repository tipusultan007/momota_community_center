<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'person_name',
        'phone',
        'amount',
        'paid_amount',
        'date',
        'due_date',
        'purpose',
        'status',
        'notes',
    ];

    protected $attributes = [
        'status' => 'pending',
        'paid_amount' => 0,
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'date' => 'date',
        'due_date' => 'date',
    ];

    protected $appends = [
        'due_amount',
    ];

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function getDueAmountAttribute()
    {
        return max(0, (float)$this->amount - (float)$this->paid_amount);
    }

    /**
     * Recalculate paid_amount and status from payments
     */
    public function recalculateStatus()
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;

        if ($this->paid_amount >= $this->amount && $this->amount > 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }

        $this->saveQuietly();
    }
}
