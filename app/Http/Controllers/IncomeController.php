<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::with('incomeCategory', 'booking.customer')->latest();

        if ($request->filled('category_id')) {
            $query->where('income_category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $incomes = $query->paginate(15)->withQueryString();
        $categories = IncomeCategory::all();
        $totalIncome = $query->sum('amount');

        return view('incomes.index', compact('incomes', 'categories', 'totalIncome'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Income::create($request->all());

        return redirect()->route('incomes.index')->with('success', 'আয়ের তথ্য সফলভাবে যোগ করা হয়েছে।');
    }

    public function edit(Income $income)
    {
        $categories = IncomeCategory::all();
        return view('incomes.edit', compact('income', 'categories'));
    }

    public function update(Request $request, Income $income)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $income->update($request->all());

        return redirect()->route('incomes.index')->with('success', 'আয়ের তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'আয়ের রেকর্ড সফলভাবে ডিলিট করা হয়েছে।');
    }
}
