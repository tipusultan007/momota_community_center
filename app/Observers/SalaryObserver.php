<?php

namespace App\Observers;

use App\Models\Salary;
use App\Models\Transaction;

class SalaryObserver
{
    /**
     * Handle the Salary "created" event.
     */
    public function created(Salary $salary): void
    {
        $this->syncTransaction($salary);
    }

    /**
     * Handle the Salary "updated" event.
     */
    public function updated(Salary $salary): void
    {
        $this->syncTransaction($salary);
    }

    /**
     * Handle the Salary "deleted" event.
     */
    public function deleted(Salary $salary): void
    {
        $salary->transaction()->delete();
    }
    
    private function syncTransaction(Salary $salary): void
    {
        Transaction::updateOrCreate(
            [
                'transactionable_id' => $salary->id,
                'transactionable_type' => Salary::class,
            ],
            [
                'hall_id' => null, // Salary doesn't belong to a hall
                'type' => 'salary',
                'amount' => $salary->amount,
                'date' => $salary->payment_date ?? $salary->created_at,
                'description' => 'Salary payment for ' . $salary->month . '/' . $salary->year . ($salary->notes ? ' - ' . $salary->notes : ''),
            ]
        );
    }
}
