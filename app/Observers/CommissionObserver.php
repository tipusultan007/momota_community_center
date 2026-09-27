<?php

namespace App\Observers;

use App\Models\Commission;
use App\Models\Transaction;

class CommissionObserver
{
    /**
     * Handle the Commission "created" event.
     */
    public function created(Commission $commission): void
    {
        $this->syncTransaction($commission);
    }

    /**
     * Handle the Commission "updated" event.
     */
    public function updated(Commission $commission): void
    {
        $this->syncTransaction($commission);
    }

    /**
     * Handle the Commission "deleted" event.
     */
    public function deleted(Commission $commission): void
    {
        $commission->transaction()->delete();
    }
    
    private function syncTransaction(Commission $commission): void
    {
        Transaction::updateOrCreate(
            [
                'transactionable_id' => $commission->id,
                'transactionable_type' => Commission::class,
            ],
            [
                'hall_id' => $commission->booking ? $commission->booking->hall_id : null,
                'type' => 'commission',
                'amount' => $commission->net_payout ?? $commission->amount,
                'date' => $commission->updated_at ?? $commission->created_at,
                'description' => 'Commission payout' . ($commission->notes ? ' - ' . $commission->notes : ''),
            ]
        );
    }
}
