<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of the loans/borrowings.
     */
    public function index(Request $request)
    {
        $query = Loan::query()->with('payments')->latest('date');

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'unsettled') {
                $query->whereIn('status', ['pending', 'partial']);
            } elseif (in_array($request->status, ['pending', 'partial', 'paid'])) {
                $query->where('status', $request->status);
            }
        }

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('person_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Overall summary totals
        $summaryQuery = Loan::query();
        $totalBorrowed = $summaryQuery->sum('amount');
        $totalRepaid = $summaryQuery->sum('paid_amount');
        $totalDue = max(0, $totalBorrowed - $totalRepaid);
        $unsettledCount = (clone $summaryQuery)->whereIn('status', ['pending', 'partial'])->count();

        $loans = $query->paginate(15)->withQueryString();

        return view('loans.index', compact(
            'loans',
            'totalBorrowed',
            'totalRepaid',
            'totalDue',
            'unsettledCount'
        ));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        return view('loans.create');
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'person_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'person_name.required' => 'ঋণদাতা বা ব্যক্তির নাম প্রদান করুন।',
            'amount.required' => 'টাকার পরিমাণ উল্লেখ করুন।',
            'amount.min' => 'টাকার পরিমাণ কমপক্ষে ১ টাকা হতে হবে।',
            'date.required' => 'টাকা গ্রহণের তারিখ প্রদান করুন।',
        ]);

        $loan = Loan::create([
            'person_name' => $request->person_name,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'paid_amount' => 0,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'purpose' => $request->purpose,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'টাকা ধার গ্রহণের তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।');
    }

    /**
     * Display the specified loan and its repayments.
     */
    public function show(Loan $loan)
    {
        $loan->load('payments');
        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified loan.
     */
    public function edit(Loan $loan)
    {
        return view('loans.edit', compact('loan'));
    }

    /**
     * Update the specified loan in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'person_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:' . $loan->paid_amount,
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'person_name.required' => 'ঋণদাতা বা ব্যক্তির নাম প্রদান করুন।',
            'amount.required' => 'টাকার পরিমাণ উল্লেখ করুন।',
            'amount.min' => 'টাকার পরিমাণ ইতিমধ্যে পরিশোধিত টাকার (' . number_format($loan->paid_amount, 2) . ' ৳) চেয়ে কম হতে পারে না।',
            'date.required' => 'টাকা গ্রহণের তারিখ প্রদান করুন।',
        ]);

        $loan->update([
            'person_name' => $request->person_name,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'purpose' => $request->purpose,
            'notes' => $request->notes,
        ]);

        $loan->recalculateStatus();

        return redirect()->route('loans.show', $loan)
            ->with('success', 'ধারের তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Remove the specified loan from storage.
     */
    public function destroy(Loan $loan)
    {
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'ধারের রেকর্ড সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * Add a repayment to the specified loan.
     */
    public function addPayment(Request $request, Loan $loan)
    {
        $maxPayable = $loan->due_amount;

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $maxPayable,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'পরিশোধের পরিমাণ উল্লেখ করুন।',
            'amount.min' => 'পরিশোধের পরিমাণ কমপক্ষে ১ টাকা হতে হবে।',
            'amount.max' => 'পরিশোধের পরিমাণ বকেয়া ' . number_format($maxPayable, 2) . ' ৳ এর বেশি হতে পারে না।',
            'payment_date.required' => 'পরিশোধের তারিখ প্রদান করুন।',
            'payment_method.required' => 'পরিশোধ মাধ্যম নির্বাচন করুন।',
        ]);

        $loan->payments()->create([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'পরিশোধের তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।');
    }

    /**
     * Delete a repayment entry.
     */
    public function destroyPayment(LoanPayment $payment)
    {
        $loan = $payment->loan;
        $payment->delete();

        return redirect()->route('loans.show', $loan)
            ->with('success', 'পরিশোধের এন্ট্রি মুছে ফেলা হয়েছে।');
    }
}
