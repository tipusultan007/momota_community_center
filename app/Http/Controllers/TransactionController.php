<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getTransactionData($request);

        $transactions = $data['query']->orderBy('date', 'desc')->paginate(20);

        return view('transactions.index', array_merge($data, ['transactions' => $transactions]));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getTransactionData($request);
        
        // For PDF, we want all transactions in the period, not paginated, ordered asc for running balance
        $transactions = $data['query']->orderBy('date', 'asc')->get();
        $data['transactions'] = $transactions;

        $pdf = Pdf::loadView('transactions.pdf', $data, [], [
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'default_font' => 'hind_siliguri'
        ]);

        return $pdf->download('Transactions_Report_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    private function getTransactionData(Request $request)
    {
        // Default to current month if no dates provided
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query = Transaction::query()->with('transactionable');

        // Optional filtering by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $query->whereBetween('date', [$startDate, $endDate]);

        // Calculate Opening Balance (all transactions prior to start date)
        // If type filter is applied, do we filter opening balance? Usually opening balance is total, regardless of type filter, 
        // but if filtering by type, opening balance of JUST that type might be weird. Let's calculate overall opening balance.
        $openingIncomes = Transaction::where('date', '<', $startDate)->where('type', 'income')->sum('amount');
        $openingExpenses = Transaction::where('date', '<', $startDate)->whereIn('type', ['expense', 'salary', 'commission'])->sum('amount');
        $openingBalance = $openingIncomes - $openingExpenses;

        // Calculate Period Totals
        $periodIncomesQuery = Transaction::whereBetween('date', [$startDate, $endDate])->where('type', 'income');
        $periodExpensesQuery = Transaction::whereBetween('date', [$startDate, $endDate])->whereIn('type', ['expense', 'salary', 'commission']);

        if ($request->filled('type')) {
            if ($request->type == 'income') {
                $periodExpensesQuery->where('id', -1); // Force empty
            } else {
                $periodIncomesQuery->where('id', -1); // Force empty
                $periodExpensesQuery->where('type', $request->type);
            }
        }

        $periodIncomes = $periodIncomesQuery->sum('amount');
        $periodExpenses = $periodExpensesQuery->sum('amount');

        $closingBalance = $openingBalance + $periodIncomes - $periodExpenses;

        $tenant = \App\Models\BusinessSetting::get();
        $toBengali = fn($number) => str_replace(
            ['0','1','2','3','4','5','6','7','8','9'],
            ['০','১','২','৩','৪','৫','৬','৭','৮','৯'],
            $number
        );

        return [
            'query' => $query,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'periodIncomes' => $periodIncomes,
            'periodExpenses' => $periodExpenses,
            'type' => $request->type,
            'tenant' => $tenant,
            'toBengali' => $toBengali
        ];
    }
}
