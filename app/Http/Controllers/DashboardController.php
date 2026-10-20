<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Loan;
use App\Models\Salary;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now        = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd   = $now->copy()->endOfMonth();
        $prevStart  = $now->copy()->subMonth()->startOfMonth();
        $prevEnd    = $now->copy()->subMonth()->endOfMonth();
        $yearStart  = $now->copy()->startOfYear();

        /* ------------------------------------------------------------------ */
        /* 1. BOOKINGS                                                          */
        /* ------------------------------------------------------------------ */
        $totalBookingsCount   = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $totalBookingsValue   = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        $prevBookingsCount    = Booking::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        // Outstanding dues across ALL bookings
        $totalBookingDue = Booking::selectRaw('SUM(total_amount) - SUM(advance_amount) as due')
            ->whereIn('status', ['confirmed', 'pending'])
            ->value('due') ?? 0;

        // Today's events
        $todayEvents = BookingItem::with(['booking.customer'])
            ->whereDate('event_date', today())
            ->count();

        // Upcoming (next 7 days)
        $upcomingBookings = BookingItem::with(['booking.customer'])
            ->whereHas('booking.customer')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(10)
            ->get();

        // Booking collection rate this month
        $monthBookingTotal   = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        $monthBookingAdvance = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('advance_amount');
        $collectionRate      = $monthBookingTotal > 0 ? round(($monthBookingAdvance / $monthBookingTotal) * 100) : 0;

        /* ------------------------------------------------------------------ */
        /* 2. FINANCIALS                                                        */
        /* ------------------------------------------------------------------ */
        $totalIncome    = Income::whereBetween('date', [$monthStart, $monthEnd])->sum('amount');
        $totalExpense   = Expense::whereBetween('date', [$monthStart, $monthEnd])->sum('amount');
        $prevIncome     = Income::whereBetween('date', [$prevStart, $prevEnd])->sum('amount');
        $prevExpense    = Expense::whereBetween('date', [$prevStart, $prevEnd])->sum('amount');
        $netProfit      = $totalIncome - $totalExpense;
        $prevNetProfit  = $prevIncome - $prevExpense;

        // YTD (Year-to-date)
        $ytdIncome  = Income::whereBetween('date', [$yearStart, $now])->sum('amount');
        $ytdExpense = Expense::whereBetween('date', [$yearStart, $now])->sum('amount');
        $ytdProfit  = $ytdIncome - $ytdExpense;

        // Cash position: all-time income – all-time expense
        $allTimeIncome  = Transaction::whereIn('type', ['income', 'loan_taken'])->sum('amount');
        $allTimeExpense = Transaction::whereIn('type', ['expense', 'salary', 'commission', 'loan_repayment'])->sum('amount');
        $cashPosition   = $allTimeIncome - $allTimeExpense;

        /* ------------------------------------------------------------------ */
        /* 3. LOANS (হাওলাত / ঋণ)                                             */
        /* ------------------------------------------------------------------ */
        $totalLoanDue       = Loan::whereIn('status', ['pending', 'partial'])->sum(DB::raw('amount - paid_amount'));
        $unsettledLoansCount = Loan::whereIn('status', ['pending', 'partial'])->count();

        /* ------------------------------------------------------------------ */
        /* 4. SALARY                                                            */
        /* ------------------------------------------------------------------ */
        $activeEmployees        = Employee::where('is_active', true)->count();
        $monthSalaryPaid        = Salary::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount');
        $totalMonthlySalaryDue  = Employee::where('is_active', true)->sum('salary_amount');
        $salaryRemaining        = max(0, $totalMonthlySalaryDue - $monthSalaryPaid);

        /* ------------------------------------------------------------------ */
        /* 5. MONTHLY TREND (last 6 months for sparkline)                      */
        /* ------------------------------------------------------------------ */
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m  = $now->copy()->subMonths($i);
            $ms = $m->copy()->startOfMonth();
            $me = $m->copy()->endOfMonth();
            $monthlyData[] = [
                'label'   => $m->format('M'),
                'income'  => (float) Income::whereBetween('date', [$ms, $me])->sum('amount'),
                'expense' => (float) Expense::whereBetween('date', [$ms, $me])->sum('amount'),
                'bookings' => Booking::whereBetween('created_at', [$ms, $me])->count(),
            ];
        }

        /* ------------------------------------------------------------------ */
        /* 6. INCOME BREAKDOWN (top 5 categories this month)                   */
        /* ------------------------------------------------------------------ */
        $incomeByCategory = Income::with('incomeCategory')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        /* ------------------------------------------------------------------ */
        /* 7. EXPENSE BREAKDOWN (top 5 categories this month)                  */
        /* ------------------------------------------------------------------ */
        $expenseByCategory = Expense::with('expenseCategory')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        /* ------------------------------------------------------------------ */
        /* 8. RECENT TRANSACTIONS (last 8)                                     */
        /* ------------------------------------------------------------------ */
        $recentTransactions = Transaction::with('transactionable')
            ->latest('date')
            ->take(8)
            ->get();

        /* ------------------------------------------------------------------ */
        /* 9. MONTH-OVER-MONTH % CHANGES                                       */
        /* ------------------------------------------------------------------ */
        $incomeChange  = $prevIncome > 0 ? round((($totalIncome - $prevIncome) / $prevIncome) * 100, 1) : null;
        $expenseChange = $prevExpense > 0 ? round((($totalExpense - $prevExpense) / $prevExpense) * 100, 1) : null;
        $bookingChange = $prevBookingsCount > 0 ? round((($totalBookingsCount - $prevBookingsCount) / $prevBookingsCount) * 100, 1) : null;

        return view('dashboard', compact(
            // Bookings
            'totalBookingsCount', 'totalBookingsValue', 'totalBookingDue',
            'todayEvents', 'upcomingBookings', 'collectionRate',
            'bookingChange',
            // Financials
            'totalIncome', 'totalExpense', 'netProfit',
            'prevNetProfit', 'ytdIncome', 'ytdExpense', 'ytdProfit',
            'cashPosition', 'incomeChange', 'expenseChange',
            // Loans
            'totalLoanDue', 'unsettledLoansCount',
            // Salary
            'activeEmployees', 'monthSalaryPaid', 'salaryRemaining',
            // Charts
            'monthlyData', 'incomeByCategory', 'expenseByCategory',
            // Feed
            'recentTransactions'
        ) + ['title' => 'ড্যাশবোর্ড']);
    }
}
