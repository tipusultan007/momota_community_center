<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Data retrieval
        $incomeQuery = Income::whereBetween('date', [$startDate, $endDate]);
        $totalIncome = $incomeQuery->sum('amount');
        $incomeByCategory = $incomeQuery->with('incomeCategory')
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->get();

        $expenseQuery = Expense::whereBetween('date', [$startDate, $endDate]);
        $totalExpense = $expenseQuery->sum('amount');
        $expenseByCategory = $expenseQuery->with('expenseCategory')
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->get();

        $bookingsCount = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $bookingsTotalValue = Booking::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $netProfit = $totalIncome - $totalExpense;

        return view('reports.index', compact(
            'totalIncome', 'totalExpense', 'netProfit', 
            'incomeByCategory', 'expenseByCategory', 
            'bookingsCount', 'bookingsTotalValue',
            'startDate', 'endDate'
        ));
    }

    public function pdf(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Data retrieval
        $incomeQuery = Income::whereBetween('date', [$startDate, $endDate]);
        $totalIncome = $incomeQuery->sum('amount');
        $incomeByCategory = $incomeQuery->with('incomeCategory')
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->get();

        $expenseQuery = Expense::whereBetween('date', [$startDate, $endDate]);
        $totalExpense = $expenseQuery->sum('amount');
        $expenseByCategory = $expenseQuery->with('expenseCategory')
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->get();

        $bookingsCount = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $bookingsTotalValue = Booking::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $netProfit = $totalIncome - $totalExpense;

        // Fetch Hall Details
        $tenant = \App\Models\BusinessSetting::get();

        $toBengali = fn($number) => $this->toBengali($number);

        $pdf = Pdf::loadView('reports.pdf', compact(
            'totalIncome', 'totalExpense', 'netProfit', 
            'incomeByCategory', 'expenseByCategory', 
            'bookingsCount', 'bookingsTotalValue',
            'startDate', 'endDate', 'tenant', 'toBengali'
        ), [], [
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'default_font' => 'hind_siliguri'
        ]);

        return $pdf->download('Final-Report-' . $startDate->format('d-M-Y') . '.pdf');
    }

    private function toBengali($number) {
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        return str_replace($en, $bn, $number);
    }
}
