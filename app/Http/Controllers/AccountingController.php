<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index()
    {
        $incomes = Income::latest()->paginate(10, ['*'], 'incomes_page');
        $expenses = Expense::latest()->paginate(10, ['*'], 'expenses_page');
        
        $totalIncome = Income::sum('amount');
        $totalExpense = Expense::sum('amount');
        
        return view('accounting.index', compact('incomes', 'expenses', 'totalIncome', 'totalExpense'));
    }

    public function createIncome()
    {
        $categories = \App\Models\IncomeCategory::all();
        return view('accounting.income_create', compact('categories'));
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Income::create($request->all());

        return redirect()->route('accounting.index')->with('success', 'আয়ের তথ্য সফলভাবে যোগ করা হয়েছে।');
    }

    public function createExpense()
    {
        $categories = \App\Models\ExpenseCategory::all();
        return view('accounting.expense_create', compact('categories'));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Expense::create($request->all());

        return redirect()->route('accounting.index')->with('success', 'খরচের তথ্য সফলভাবে যোগ করা হয়েছে।');
    }
}
