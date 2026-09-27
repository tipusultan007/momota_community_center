<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->paginate(10);
        return view('expense_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expense_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        ExpenseCategory::create($request->all());

        return redirect()->route('expense-categories.index')->with('success', 'ব্যয় ক্যাটাগরি সফলভাবে তৈরি করা হয়েছে।');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense_categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $expenseCategory->update($request->all());

        return redirect()->route('expense-categories.index')->with('success', 'ব্যয় ক্যাটাগরি সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->exists()) {
            return redirect()->back()->with('error', 'এই ক্যাটাগরির অধীনে ব্যয় রেকর্ড থাকায় এটি ডিলিট করা সম্ভব নয়।');
        }

        $expenseCategory->delete();
        return redirect()->route('expense-categories.index')->with('success', 'ব্যয় ক্যাটাগরি সফলভাবে ডিলিট করা হয়েছে।');
    }
}
