<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'employee_id',
        'amount',
        'payment_date',
        'month',
        'year',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
