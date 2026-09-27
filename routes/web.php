<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantSettingsController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::middleware(['tenant', 'hall'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');
        
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        // Subscription Routes (Must be accessible even if expired via controller logic)
        Route::get('subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/history', [\App\Http\Controllers\SubscriptionController::class, 'history'])->name('subscriptions.history');
        Route::post('subscriptions/checkout', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
        Route::get('subscriptions/expired', [\App\Http\Controllers\SubscriptionController::class, 'expired'])->name('subscriptions.expired');

        Route::middleware('subscription')->group(function () {
            
            // Hall management disabled - system is dedicated to single convention hall
            Route::any('halls/{any?}', fn() => redirect()->route('dashboard'))->where('any', '.*');

            Route::middleware('can:manage-bookings')->group(function() {
                Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
                Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
                Route::get('bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');
                Route::resource('bookings', BookingController::class);
                Route::get('bookings/{booking}/invoice', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice');
                Route::post('bookings/{booking}/add-payment', [BookingController::class, 'addPayment'])->name('bookings.add-payment');
                Route::post('bookings/{booking}/pay-servers', [BookingController::class, 'payServers'])->name('bookings.pay-servers');
                Route::post('booking-items/{item}/assign-asset', [BookingController::class, 'assignAsset'])->name('bookings.assign-asset');
                Route::post('asset-assignments/{assignment}/return', [BookingController::class, 'returnAsset'])->name('bookings.return-asset');
                Route::resource('customers', CustomerController::class);
            });

            Route::middleware('can:manage-employees')->group(function() {
                Route::resource('employees', EmployeeController::class);
                Route::post('employees/{employee}/pay-salary', [EmployeeController::class, 'paySalary'])->name('employees.pay-salary');
                Route::resource('users', \App\Http\Controllers\UserController::class);
            });

            Route::middleware('can:manage-financials')->group(function() {
                Route::resource('vendors', VendorController::class);
                Route::resource('assets', AssetController::class);
                Route::resource('income-categories', IncomeCategoryController::class);
                Route::resource('expense-categories', ExpenseCategoryController::class);
                Route::resource('incomes', IncomeController::class);
                Route::resource('expenses', ExpenseController::class);
                Route::get('accounting', [AccountingController::class, 'index'])->name('accounting.index');
                Route::get('transactions/export', [\App\Http\Controllers\TransactionController::class, 'exportPdf'])->name('transactions.export');
                Route::get('transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
                Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
                Route::get('reports/pdf', [\App\Http\Controllers\ReportController::class, 'pdf'])->name('reports.pdf');
            });

            Route::middleware('can:manage-financials')->group(function() {
                Route::get('/commissions', [App\Http\Controllers\CommissionController::class, 'index'])->name('commissions.index');
                Route::post('/commissions/{commission}/collect', [App\Http\Controllers\CommissionController::class, 'collect'])->name('commissions.collect');
                Route::post('/commissions/{commission}/pay-vendor', [App\Http\Controllers\CommissionController::class, 'payVendor'])->name('commissions.pay-vendor');
            });

            // Tenant Settings
            Route::middleware('can:manage-settings')->group(function() {
                Route::get('settings', [TenantSettingsController::class, 'index'])->name('settings.index');
                Route::post('settings', [TenantSettingsController::class, 'update'])->name('settings.update');
            });
            
        });
    });
});

require __DIR__.'/auth.php';

// Old Admin Routes Redirects (Unified to Single-Tenant)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn() => redirect()->route('login'))->name('login');
    Route::get('/dashboard', fn() => redirect()->route('dashboard'))->name('dashboard');
    Route::any('/logout', function(\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::guard('admin')->logout();
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
    Route::any('/{any?}', fn() => redirect()->route('dashboard'))->where('any', '.*');
});

// Storage file serving route (for shared hosting like Hostinger where storage:link / symlinks are restricted)
Route::get('/storage/{path}', function ($path) {
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->where('path', '.*');

// Helper route to trigger storage:link on hosting without SSH
Route::get('/run-storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link created successfully! ' . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $target = storage_path('app/public');
        $link = public_path('storage');
        if (file_exists($link)) {
            return 'Storage link or directory already exists at: ' . $link;
        }
        if (@symlink($target, $link)) {
            return 'Manual PHP symlink created successfully!';
        }
        return 'Symlink could not be created (' . $e->getMessage() . ').<br>No problem! The direct `/storage/{path}` fallback route is active and serving files automatically.';
    }
});
