<?php

namespace App\Http\Controllers;

use App\Models\BookingItem;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingBookings = BookingItem::with(['booking.customer', 'hall'])
            ->whereHas('booking.customer')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(10)
            ->get();

        // Stats for current month
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $totalIncome = Income::whereBetween('date', [$monthStart, $monthEnd])->sum('amount');
        $totalExpense = Expense::whereBetween('date', [$monthStart, $monthEnd])->sum('amount');
        $totalBookingsCount = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $totalBookingsValue = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');

        return view('dashboard', [
            'title' => 'ড্যাশবোর্ড',
            'upcomingBookings' => $upcomingBookings,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalBookingsCount' => $totalBookingsCount,
            'totalBookingsValue' => $totalBookingsValue
        ]);
    }
}
