<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Get list of loans with summary statistics.
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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('person_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        // Summary calculations
        $summaryBase = Loan::query();

        $totalBorrowed = (float) (clone $summaryBase)->sum('amount');
        $totalRepaid = (float) (clone $summaryBase)->sum('paid_amount');
        $totalDue = max(0, $totalBorrowed - $totalRepaid);
        $unsettledCount = (int) (clone $summaryBase)->whereIn('status', ['pending', 'partial'])->count();

        $perPage = (int) $request->query('per_page', 20);
        $loans = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_borrowed' => $totalBorrowed,
                    'total_repaid' => $totalRepaid,
                    'total_due' => $totalDue,
                    'unsettled_count' => $unsettledCount,
                ],
                'loans' => $loans->items(),
                'pagination' => [
                    'current_page' => $loans->currentPage(),
                    'last_page' => $loans->lastPage(),
                    'total' => $loans->total(),
                    'per_page' => $loans->perPage(),
                ],
            ],
        ]);
    }

    /**
     * Store a new loan.
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

        return response()->json([
            'status' => 'success',
            'message' => 'টাকা ধার গ্রহণের তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।',
            'data' => $loan->load('payments'),
        ], 201);
    }

    /**
     * Get specific loan details.
     */
    public function show($id)
    {
        $loan = Loan::with('payments')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $loan,
        ]);
    }

    /**
     * Update an existing loan.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $request->validate([
            'person_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:' . $loan->paid_amount,
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
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

        return response()->json([
            'status' => 'success',
            'message' => 'ধারের তথ্য আপডেট করা হয়েছে।',
            'data' => $loan->load('payments'),
        ]);
    }

    /**
     * Delete a loan.
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ধারের তথ্য মুছে ফেলা হয়েছে।',
        ]);
    }

    /**
     * Record a repayment for a loan.
     */
    public function addPayment(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $maxPayable = $loan->due_amount;

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $maxPayable,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = $loan->payments()->create([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        $loan->refresh()->load('payments');

        return response()->json([
            'status' => 'success',
            'message' => 'পরিশোধের তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।',
            'data' => [
                'payment' => $payment,
                'loan' => $loan,
            ],
        ], 201);
    }

    /**
     * Delete a repayment entry.
     */
    public function destroyPayment($paymentId)
    {
        $payment = LoanPayment::findOrFail($paymentId);
        $loan = $payment->loan;
        $payment->delete();

        $loan?->refresh()->load('payments');

        return response()->json([
            'status' => 'success',
            'message' => 'পরিশোধের তথ্য মুছে ফেলা হয়েছে।',
            'data' => $loan,
        ]);
    }
}
