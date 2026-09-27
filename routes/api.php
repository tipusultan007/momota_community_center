<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AccountingController;
use App\Http\Controllers\Api\HallController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SuperadminController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\SubscriptionController;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');

Route::name('api.')->group(function () {
    Route::middleware(['auth:sanctum', 'hall', 'throttle:300,1'])->group(function () {
        Route::get('/user', [AuthController::class, 'profile']);
        Route::post('/user/profile', [AuthController::class, 'updateProfile']);
        Route::post('/user/password', [AuthController::class, 'changePassword']);
        Route::post('/tenant/settings', [AuthController::class, 'updateTenantSettings']);
        
        Route::get('/subscription', [SubscriptionController::class, 'index']);
        Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout']);
        Route::get('/subscription/history', [SubscriptionController::class, 'history']);
        
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        Route::apiResource('customers', CustomerController::class);
        
        Route::get('/accounting', [AccountingController::class, 'index']);
        Route::post('/accounting/income', [AccountingController::class, 'storeIncome']);
        Route::patch('/accounting/income/{id}', [AccountingController::class, 'updateIncome']);
        Route::delete('/accounting/income/{id}', [AccountingController::class, 'deleteIncome']);
        
        Route::post('/accounting/expense', [AccountingController::class, 'storeExpense']);
        Route::patch('/accounting/expense/{id}', [AccountingController::class, 'updateExpense']);
        Route::delete('/accounting/expense/{id}', [AccountingController::class, 'deleteExpense']);
        
        Route::get('/accounting/categories', [AccountingController::class, 'getCategories']);
        Route::post('/accounting/categories', [AccountingController::class, 'storeCategory']);
        Route::patch('/accounting/categories/{id}', [AccountingController::class, 'updateCategory']);
        Route::delete('/accounting/categories/{id}', [AccountingController::class, 'deleteCategory']);
        
        Route::get('/halls', [HallController::class, 'index']);
        Route::get('/halls/{id}', [HallController::class, 'show']);
        
        Route::apiResource('staff', StaffController::class);
        Route::patch('/staff/{id}/status', [StaffController::class, 'updateStatus']);
        Route::post('/staff/{id}/pay-salary', [StaffController::class, 'paySalary']);
        Route::get('/salaries', [StaffController::class, 'salaries']);
        
        // Superadmin Routes
        Route::get('/superadmin/stats', [SuperadminController::class, 'index']);
        Route::get('/superadmin/logs', [SuperadminController::class, 'systemLogs']);
        
        Route::get('bookings/check-availability', [BookingController::class, 'checkAvailability']);
        Route::apiResource('bookings', BookingController::class);
        Route::get('bookings/{booking}/invoice', [BookingController::class, 'downloadInvoice']);
        Route::put('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
        Route::post('bookings/{booking}/payment', [BookingController::class, 'addPayment']);
        Route::post('bookings/{booking}/pay-servers', [BookingController::class, 'payServers']);
        Route::post('booking-items/{item}/assign-asset', [BookingController::class, 'assignAsset']);
        Route::post('asset-assignments/{assignment}/return', [BookingController::class, 'returnAsset']);
        
        Route::apiResource('assets', \App\Http\Controllers\Api\AssetController::class);
        
        Route::apiResource('users', UserController::class);
        Route::get('/users/meta/roles', [UserController::class, 'getRoles']);
        
        Route::get('/reports/summary', [ReportController::class, 'summary']);
        Route::get('/reports/export', [ReportController::class, 'downloadPdf']);
        
        Route::apiResource('vendors', VendorController::class);
        
        Route::get('/commissions', [CommissionController::class, 'index']);
        Route::post('/commissions/{id}/collect', [CommissionController::class, 'collect']);
        Route::post('/commissions/{id}/pay-vendor', [CommissionController::class, 'payVendor']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
