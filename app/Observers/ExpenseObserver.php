<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Transaction;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->syncTransaction($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $this->syncTransaction($expense);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $expense->transaction()->delete();
    }
    
    private function syncTransaction(Expense $expense): void
    {
        Transaction::updateOrCreate(
            [
                'transactionable_id' => $expense->id,
                'transactionable_type' => Expense::class,
            ],
            [
                'hall_id' => $expense->hall_id,
                'type' => 'expense',
                'amount' => $expense->amount,
                'date' => $expense->date ?? $expense->created_at,
                'description' => $expense->description,
            ]
        );
    }
}
