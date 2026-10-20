<?php

namespace App\Observers;

use App\Models\LoanPayment;
use App\Models\Transaction;

class LoanPaymentObserver
{
    /**
     * Handle the LoanPayment "created" event.
     */
    public function created(LoanPayment $payment): void
    {
        $this->syncTransaction($payment);
    }

    /**
     * Handle the LoanPayment "updated" event.
     */
    public function updated(LoanPayment $payment): void
    {
        $this->syncTransaction($payment);
    }

    /**
     * Handle the LoanPayment "deleted" event.
     */
    public function deleted(LoanPayment $payment): void
    {
        $payment->transaction()->delete();
    }

    private function syncTransaction(LoanPayment $payment): void
    {
        $payment->loadMissing('loan');
        $loan = $payment->loan;

        Transaction::updateOrCreate(
            [
                'transactionable_id' => $payment->id,
                'transactionable_type' => LoanPayment::class,
            ],
            [
                'type' => 'loan_repayment',
                'amount' => $payment->amount,
                'date' => $payment->payment_date ?? $payment->created_at,
                'description' => '[ধার পরিশোধ] ' . ($loan?->person_name ?? 'ঋণ পরিশোধ') . ($payment->notes ? ' - ' . $payment->notes : ''),
            ]
        );
    }
}
