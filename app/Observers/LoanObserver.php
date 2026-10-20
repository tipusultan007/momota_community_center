<?php

namespace App\Observers;

use App\Models\Loan;
use App\Models\Transaction;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     */
    public function created(Loan $loan): void
    {
        $this->syncTransaction($loan);
    }

    /**
     * Handle the Loan "updated" event.
     */
    public function updated(Loan $loan): void
    {
        $this->syncTransaction($loan);
    }

    /**
     * Handle the Loan "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        $loan->transaction()->delete();
    }

    private function syncTransaction(Loan $loan): void
    {
        Transaction::updateOrCreate(
            [
                'transactionable_id' => $loan->id,
                'transactionable_type' => Loan::class,
            ],
            [
                'type' => 'loan_taken',
                'amount' => $loan->amount,
                'date' => $loan->date ?? $loan->created_at,
                'description' => '[ধার গ্রহণ] ' . $loan->person_name . ($loan->purpose ? ' - ' . $loan->purpose : ''),
            ]
        );
    }
}
