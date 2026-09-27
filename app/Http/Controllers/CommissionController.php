<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Vendor;
use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $hallId = session('active_hall_id');

        $query = Commission::query();

        if ($hallId) {
            // Check if booking belongs tohall
            $query->whereHas('booking', function($q) use ($hallId) {
                $q->where('hall_id', $hallId);
            });
        }

        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $commissions = $query->with(['vendor', 'booking.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $vendors = Vendor::where('is_active', true)->get();

        return view('commissions.index', compact('commissions', 'vendors'));
    }

    public function collect(Commission $commission)
    {
        if ($commission->status === 'paid') {
            return back()->with('error', 'এই কমিশনটি ইতিপূর্বেই সংগ্রহ করা হয়েছে।');
        }

        DB::transaction(function () use ($commission) {
            // 1. Update commission status
            $commission->update(['status' => 'paid']);

            // 2. Find or create "Commission" income category
            $category = IncomeCategory::firstOrCreate(
                ['name' => 'কমিশন'],
                ['is_active' => true]
            );

            // 3. Record as Income
            Income::create([
                'hall_id' => $commission->booking->hall_id ?? session('active_hall_id'),
                'amount' => $commission->amount,
                'category' => $category->name,
                'income_category_id' => $category->id,
                'booking_id' => $commission->booking_id,
                'description' => "ভেন্ডর '{$commission->vendor->name}' থেকে কমিশন সংগ্রহ। নোট: {$commission->notes}",
                'date' => now(),
            ]);
        });

        return back()->with('success', 'কমিশন সফলভাবে সংগ্রহ করা হয়েছে এবং আয় হিসেবে রেকর্ড করা হয়েছে।');
    }

    public function payVendor(Commission $commission)
    {
        if ($commission->payout_status === 'paid') {
            return back()->with('error', 'এই ভেন্ডর পেমেন্ট ইতিপূর্বেই পরিশোধ করা হয়েছে।');
        }

        DB::transaction(function () use ($commission) {
            // 1. Update payout status
            $commission->update(['payout_status' => 'paid']);

            // 2. Find or create "Vendor Payment" expense category
            $category = \App\Models\ExpenseCategory::firstOrCreate(
                ['name' => 'ভেন্ডর পেমেন্ট'],
                ['is_active' => true]
            );

            // 3. Record as Expense
            \App\Models\Expense::create([
                'hall_id' => $commission->booking->hall_id ?? session('active_hall_id'),
                'amount' => $commission->net_payout,
                'category' => $category->name,
                'expense_category_id' => $category->id,
                'description' => "ভেন্ডর '{$commission->vendor->name}' কে বিল পরিশোধ। বুকিং আইডি: #{$commission->booking_id}",
                'date' => now(),
            ]);
        });

        return back()->with('success', 'ভেন্ডর পেমেন্ট সফলভাবে পরিশোধ করা হয়েছে এবং ব্যয় হিসেবে রেকর্ড করা হয়েছে।');
    }
}
