<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hallId = $request->header('X-Hall-Id');

        // Stats for the current month
        $totalIncome = Income::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        $totalExpense = Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        $totalBookings = Booking::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Recent Bookings (for the slider)
        $recentBookings = Booking::with(['customer', 'hall', 'items.hall', 'items.assetAssignments.asset', 'incomes'])
            ->when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'total_income' => (float) $totalIncome,
                    'total_expense' => (float) $totalExpense,
                    'total_bookings' => (int) $totalBookings,
                    'total_staff' => (int) Employee::count(),
                ],
                'recent_bookings' => $recentBookings,
            ],
        ]);
    }
}
