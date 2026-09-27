<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('expenseCategory')->latest();

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $expenses = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::all();
        $totalExpense = $query->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalExpense'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'খরচের তথ্য সফলভাবে যোগ করা হয়েছে।');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'খরচের তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'খরচের রেকর্ড সফলভাবে ডিলিট করা হয়েছে।');
    }
}
