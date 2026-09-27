<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\Transaction;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        $this->syncTransaction($income);
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        $this->syncTransaction($income);
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        $income->transaction()->delete();
    }
    
    private function syncTransaction(Income $income): void
    {
        Transaction::updateOrCreate(
            [
                'transactionable_id' => $income->id,
                'transactionable_type' => Income::class,
            ],
            [
                'hall_id' => $income->hall_id,
                'type' => 'income',
                'amount' => $income->amount,
                'date' => $income->date ?? $income->created_at,
                'description' => $income->description,
            ]
        );
    }
}
