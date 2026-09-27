<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToHall;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use BelongsToTenant, BelongsToHall;

    protected $fillable = [
        'hall_id',
        'amount',
        'category',
        'expense_category_id',
        'description',
        'date',
    ];

    protected static function booted()
    {
        static::saving(function ($expense) {
            if (empty($expense->category) && $expense->expenseCategory) {
                $expense->category = $expense->expenseCategory->name;
            }
        });
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
