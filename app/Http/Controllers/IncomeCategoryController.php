<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $categories = IncomeCategory::latest()->paginate(10);
        return view('income_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('income_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        IncomeCategory::create($request->all());

        return redirect()->route('income-categories.index')->with('success', 'আয় ক্যাটাগরি সফলভাবে তৈরি করা হয়েছে।');
    }

    public function edit(IncomeCategory $incomeCategory)
    {
        return view('income_categories.edit', compact('incomeCategory'));
    }

    public function update(Request $request, IncomeCategory $incomeCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $incomeCategory->update($request->all());

        return redirect()->route('income-categories.index')->with('success', 'আয় ক্যাটাগরি সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(IncomeCategory $incomeCategory)
    {
        if ($incomeCategory->incomes()->exists()) {
            return redirect()->back()->with('error', 'এই ক্যাটাগরির অধীনে আয় রেকর্ড থাকায় এটি ডিলিট করা সম্ভব নয়।');
        }

        $incomeCategory->delete();
        return redirect()->route('income-categories.index')->with('success', 'আয় ক্যাটাগরি সফলভাবে ডিলিট করা হয়েছে।');
    }
}
