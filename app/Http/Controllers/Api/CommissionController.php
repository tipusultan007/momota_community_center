<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions.
     */
    public function index(Request $request)
    {
        $status = $request->query('status'); // 'pending', 'paid'
        $payoutStatus = $request->query('payout_status'); // 'pending', 'paid'
        $vendorId = $request->query('vendor_id');

        $commissions = Commission::with(['vendor', 'booking.customer'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($payoutStatus, fn($q) => $q->where('payout_status', $payoutStatus))
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->latest()
            ->paginate($request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $commissions,
        ]);
    }

    /**
     * Collect commission (Record as Income).
     */
    public function collect(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);

        if ($commission->status === 'paid') {
            return response()->json(['status' => 'error', 'message' => 'এই কমিশন ইতিমধ্যে সংগ্রহ করা হয়েছে।'], 422);
        }

        return DB::transaction(function () use ($commission) {
            // 1. Mark commission as paid
            $commission->update(['status' => 'paid']);

            // 2. Record Income
            $category = IncomeCategory::where('name', 'LIKE', '%কমিশন%')
                ->first();

            Income::create([
                'hall_id' => $commission->booking?->hall_id, // Get hall from booking
                'amount' => $commission->amount,
                'income_category_id' => $category?->id,
                'category' => 'কমিশন',
                'description' => "ভেন্ডর '{$commission->vendor->name}' থেকে কমিশন সংগ্রহ। বুকিং আইডি: #{$commission->booking_id}",
                'date' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'কমিশন সফলভাবে সংগ্রহ করা হয়েছে।',
                'data' => $commission,
            ]);
        });
    }

    /**
     * Pay vendor (Record as Expense).
     */
    public function payVendor(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);

        if ($commission->payout_status === 'paid') {
            return response()->json(['status' => 'error', 'message' => 'এই পেমেন্ট ইতিমধ্যে পরিশোধ করা হয়েছে।'], 422);
        }

        return DB::transaction(function () use ($commission) {
            // 1. Mark payout as paid
            $commission->update(['payout_status' => 'paid']);

            // 2. Record Expense
            $category = ExpenseCategory::where('name', 'LIKE', '%ভেন্ডর%')
                ->first();

            Expense::create([
                'hall_id' => $commission->booking?->hall_id,
                'amount' => $commission->net_payout,
                'expense_category_id' => $category?->id,
                'category' => 'ভেন্ডর পেমেন্ট',
                'description' => "ভেন্ডর '{$commission->vendor->name}' কে বিল পরিশোধ। বুকিং আইডি: #{$commission->booking_id}",
                'date' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'ভেন্ডর পেমেন্ট সফলভাবে সম্পন্ন হয়েছে।',
                'data' => $commission,
            ]);
        });
    }
}
