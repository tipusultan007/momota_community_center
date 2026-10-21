<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \App\Models\Income::observe(\App\Observers\IncomeObserver::class);
        \App\Models\Expense::observe(\App\Observers\ExpenseObserver::class);
        \App\Models\Salary::observe(\App\Observers\SalaryObserver::class);
        \App\Models\Commission::observe(\App\Observers\CommissionObserver::class);
        \App\Models\Loan::observe(\App\Observers\LoanObserver::class);
        \App\Models\LoanPayment::observe(\App\Observers\LoanPaymentObserver::class);

        // Super Admin has all abilities
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Global business context for UI (available across all blade views & components)
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('businessSetting', \App\Models\BusinessSetting::get());
        });
    }
}
