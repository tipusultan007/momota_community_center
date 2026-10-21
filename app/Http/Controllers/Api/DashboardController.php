<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Stats for the current month with index-friendly date ranges
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $today = now()->toDateString();

        $totalIncome = Income::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalExpense = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalBookings = Booking::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $monthBookingValue = (float) Booking::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_amount');

        $monthBookingAdvance = (float) Booking::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('advance_amount');

        $collectionRate = $monthBookingValue > 0
            ? (int) round(($monthBookingAdvance / $monthBookingValue) * 100)
            : 0;

        $totalDue = (float) (Booking::whereIn('status', ['confirmed', 'pending'])
            ->whereColumn('advance_amount', '<', 'total_amount')
            ->selectRaw('COALESCE(SUM(total_amount - advance_amount), 0) as due')
            ->value('due') ?? 0);

        $todayEvents = BookingItem::whereDate('event_date', $today)
            ->whereHas('booking', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->count();

        $upcomingEvents = BookingItem::whereDate('event_date', '>=', $today)
            ->whereHas('booking', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->count();

        // Upcoming bookings ordered by their nearest future event date.
        $upcomingBookings = Booking::with(['customer', 'items.assetAssignments.asset', 'incomes'])
            ->where('status', '!=', 'cancelled')
            ->whereHas('items', fn ($query) => $query->whereDate('event_date', '>=', $today))
            ->withMin([
                'items as next_event_date' => fn ($query) => $query->whereDate('event_date', '>=', $today),
            ], 'event_date')
            ->orderBy('next_event_date')
            ->limit(5)
            ->get();

        // Keep the recent bookings feed for older mobile clients.
        $recentBookings = Booking::with(['customer', 'items.assetAssignments.asset', 'incomes'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $loanTotalBorrowed = (float) \App\Models\Loan::sum('amount');
        $loanTotalRepaid = (float) \App\Models\Loan::sum('paid_amount');
        $totalLoanDue = max(0, $loanTotalBorrowed - $loanTotalRepaid);
        $unsettledLoansCount = (int) \App\Models\Loan::whereIn('status', ['pending', 'partial'])->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'total_income' => (float) $totalIncome,
                    'total_expense' => (float) $totalExpense,
                    'total_bookings' => (int) $totalBookings,
                    'total_staff' => (int) Employee::count(),
                    'active_staff' => (int) Employee::where('is_active', true)->count(),
                    'total_customers' => (int) Customer::count(),
                    'today_events' => (int) $todayEvents,
                    'upcoming_events' => (int) $upcomingEvents,
                    'total_due' => $totalDue,
                    'total_loan_due' => (float) $totalLoanDue,
                    'loan_total_borrowed' => (float) $loanTotalBorrowed,
                    'loan_total_repaid' => (float) $loanTotalRepaid,
                    'unsettled_loans_count' => (int) $unsettledLoansCount,
                    'month_booking_value' => $monthBookingValue,
                    'collection_rate' => $collectionRate,
                ],
                'upcoming_bookings' => $upcomingBookings,
                'recent_bookings' => $recentBookings,
            ],
        ]);
    }
}
