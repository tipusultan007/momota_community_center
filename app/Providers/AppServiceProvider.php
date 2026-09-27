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
        // Register Transaction Observers
        \App\Models\Income::observe(\App\Observers\IncomeObserver::class);
        \App\Models\Expense::observe(\App\Observers\ExpenseObserver::class);
        \App\Models\Salary::observe(\App\Observers\SalaryObserver::class);
        \App\Models\Commission::observe(\App\Observers\CommissionObserver::class);

        // Super Admin has all abilities
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Global Hall & Business Context for UI
        \Illuminate\Support\Facades\View::composer(['layouts.coreui', 'layouts.tabler'], function ($view) {
            if (auth()->check()) {
                $activeHallId = session('active_hall_id');
                
                $availableHalls = \App\Models\Hall::where('is_active', true)->get();
                $activeHall = \App\Models\Hall::find($activeHallId) ?? $availableHalls->first();

                $view->with('activeHall', $activeHall);
                $view->with('availableHalls', $availableHalls);
                $view->with('businessSetting', \App\Models\BusinessSetting::get());
            } else {
                $view->with('activeHall', null);
                $view->with('availableHalls', collect());
                $view->with('businessSetting', null);
            }
        });
    }
}
