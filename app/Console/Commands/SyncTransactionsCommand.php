<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\Commission;

class SyncTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all existing incomes, expenses, salaries, and commissions to the unified transactions table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting transaction synchronization...');

        // Mute model events so we don't trigger observers again
        Income::withoutEvents(function () {
            $incomes = Income::all();
            $bar = $this->output->createProgressBar(count($incomes));
            foreach ($incomes as $income) {
                // Re-trigger the logic of observer manually
                $income->transaction()->updateOrCreate(
                    [],
                    [
                        'hall_id' => $income->hall_id,
                        'type' => 'income',
                        'amount' => $income->amount,
                        'date' => $income->date ?? $income->created_at,
                        'description' => $income->description,
                    ]
                );
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Incomes synced.');
        });

        Expense::withoutEvents(function () {
            $expenses = Expense::all();
            $bar = $this->output->createProgressBar(count($expenses));
            foreach ($expenses as $expense) {
                $expense->transaction()->updateOrCreate(
                    [],
                    [
                        'hall_id' => $expense->hall_id,
                        'type' => 'expense',
                        'amount' => $expense->amount,
                        'date' => $expense->date ?? $expense->created_at,
                        'description' => $expense->description,
                    ]
                );
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Expenses synced.');
        });

        Salary::withoutEvents(function () {
            $salaries = Salary::all();
            $bar = $this->output->createProgressBar(count($salaries));
            foreach ($salaries as $salary) {
                $salary->transaction()->updateOrCreate(
                    [],
                    [
                        'hall_id' => null,
                        'type' => 'salary',
                        'amount' => $salary->amount,
                        'date' => $salary->payment_date ?? $salary->created_at,
                        'description' => 'Salary payment for ' . $salary->month . '/' . $salary->year . ($salary->notes ? ' - ' . $salary->notes : ''),
                    ]
                );
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Salaries synced.');
        });

        Commission::withoutEvents(function () {
            $commissions = Commission::with('booking')->get();
            $bar = $this->output->createProgressBar(count($commissions));
            foreach ($commissions as $commission) {
                $commission->transaction()->updateOrCreate(
                    [],
                    [
                        'hall_id' => $commission->booking ? $commission->booking->hall_id : null,
                        'type' => 'commission',
                        'amount' => $commission->net_payout ?? $commission->amount,
                        'date' => $commission->updated_at ?? $commission->created_at,
                        'description' => 'Commission payout' . ($commission->notes ? ' - ' . $commission->notes : ''),
                    ]
                );
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Commissions synced.');
        });

        $this->info('Transaction synchronization complete!');
    }
}
