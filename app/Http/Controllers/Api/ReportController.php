<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $hallId = $request->header('X-Hall-Id');
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $bookings = Booking::when($hallId, function ($query) use ($hallId) {
                return $query->where('hall_id', $hallId);
            })
            ->whereHas('items', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('event_date', [$startDate, $endDate]);
            })
            ->get();

        $incomes = Income::when($hallId, function ($query) use ($hallId) {
                return $query->where('hall_id', $hallId);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = Expense::whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_bookings' => $bookings->count(),
                'total_income' => $incomes,
                'total_expense' => $expenses,
                'net_profit' => $incomes - $expenses,
                'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $hallId = $request->header('X-Hall-Id');
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());
        $type = $request->query('type', 'financial');

        $data = $this->getReportData($hallId, $startDate, $endDate, $type);
        $data['tenant'] = \App\Models\BusinessSetting::get();
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        $pdf = Pdf::loadView("reports.{$type}", $data);
        
        return $pdf->download("report_{$type}_{$startDate}.pdf");
    }

    private function getReportData($hallId, $startDate, $endDate, $type)
    {
        if ($type === 'financial') {
            $incomes = Income::when($hallId, function ($query) use ($hallId) {
                    return $query->where('hall_id', $hallId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $expenses = Expense::whereBetween('date', [$startDate, $endDate])
                ->get();

            return [
                'incomes' => $incomes,
                'expenses' => $expenses,
                'total_income' => $incomes->sum('amount'),
                'total_expense' => $expenses->sum('amount'),
            ];
        }

        if ($type === 'bookings') {
            $bookings = Booking::when($hallId, function ($query) use ($hallId) {
                    return $query->where('hall_id', $hallId);
                })
                ->whereHas('items', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('event_date', [$startDate, $endDate]);
                })
                ->with('hall')
                ->get();

            return [
                'bookings' => $bookings,
            ];
        }

        return [];
    }
}
