<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $hallId = $request->header('X-Hall-Id');
        $type = $request->query('type', 'all'); // 'all', 'income', 'expense'
        $perPage = $request->query('per_page', 20);

        // Summary queries (always calculate totals based on filters, regardless of pagination)
        $incomeQuery = Income::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year));
            
        $expenseQuery = Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year));

        $totalIncome = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');

        // Result collection
        $currentPage = (int) $request->query('page', 1);
        $totalCount = 0;
        $lastPage = 1;

        if ($type === 'income') {
            $results = $incomeQuery->with('incomeCategory')->orderByDesc('date')->paginate($perPage);
            $transactions = collect($results->items())->map(fn($i) => array_merge($i->toArray(), ['type' => 'income']));
            $totalCount = $results->total();
            $lastPage = $results->lastPage();
            $currentPage = $results->currentPage();
        } elseif ($type === 'expense') {
            $results = $expenseQuery->with('expenseCategory')->orderByDesc('date')->paginate($perPage);
            $transactions = collect($results->items())->map(fn($i) => array_merge($i->toArray(), ['type' => 'expense']));
            $totalCount = $results->total();
            $lastPage = $results->lastPage();
            $currentPage = $results->currentPage();
        } else {
            $incomes = $incomeQuery->with('incomeCategory')->get()->map(function ($i) {
                $arr = is_array($i) ? $i : (method_exists($i, 'toArray') ? $i->toArray() : (array)$i);
                return array_merge($arr, ['type' => 'income']);
            });
            $expenses = $expenseQuery->with('expenseCategory')->get()->map(function ($e) {
                $arr = is_array($e) ? $e : (method_exists($e, 'toArray') ? $e->toArray() : (array)$e);
                return array_merge($arr, ['type' => 'expense']);
            });
            
            $combined = $incomes->concat($expenses)->sortByDesc('date');
            $totalCount = $combined->count();
            $lastPage = max(1, (int) ceil($totalCount / $perPage));
            $transactions = $combined->forPage($currentPage, $perPage)->values();
        }

        // Monthly trends for the current year
        $trends = [];
        $currentYear = $year ?? now()->year;
        for ($m = 1; $m <= 12; $m++) {
            $mIncome = (float) Income::when($hallId, fn($q) => $q->where('hall_id', $hallId))
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $m)
                ->sum('amount');
                
            $mExpense = (float) Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId))
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $m)
                ->sum('amount');

            $trends[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'income' => $mIncome,
                'expense' => $mExpense,
            ];
        }

        // Category breakdown
        $incomeCategories = Income::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->get()
            ->map(fn($item) => ['category' => $item->category, 'total' => (float)$item->total]);

        $expenseCategories = Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->get()
            ->map(fn($item) => ['category' => $item->category, 'total' => (float)$item->total]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'net_profit' => (float) ($totalIncome - $totalExpense),
                ],
                'transactions' => $transactions,
                'pagination' => [
                    'current_page' => $currentPage,
                    'last_page' => $lastPage,
                    'total' => $totalCount,
                    'per_page' => (int) $perPage,
                ],
                'trends' => $trends,
                'breakdown' => [
                    'income' => $incomeCategories,
                    'expense' => $expenseCategories,
                ],
            ],
        ]);
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'income_category_id' => 'nullable',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'hall_id' => 'nullable',
        ]);

        $defaultHallId = \App\Models\Hall::first()?->id ?? 1;
        $hallId = $request->hall_id ?: $defaultHallId;

        $incomeCategory = null;
        if ($request->filled('income_category_id')) {
            $incomeCategory = \App\Models\IncomeCategory::find($request->income_category_id);
        }
        if (!$incomeCategory && $request->filled('category')) {
            $incomeCategory = \App\Models\IncomeCategory::firstOrCreate(['name' => $request->category]);
        }
        if (!$incomeCategory) {
            $incomeCategory = \App\Models\IncomeCategory::firstOrCreate(['name' => 'অন্যান্য আয়']);
        }

        $income = Income::create([
            'amount' => $request->amount,
            'date' => $request->date,
            'income_category_id' => $incomeCategory->id,
            'category' => $incomeCategory->name,
            'description' => $request->description,
            'hall_id' => $hallId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'আয় সফলভাবে রেকর্ড করা হয়েছে।',
            'data' => $income,
        ], 201);
    }

    public function updateIncome(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'income_category_id' => 'nullable',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'hall_id' => 'nullable',
        ]);

        $income = Income::findOrFail($id);
        $defaultHallId = \App\Models\Hall::first()?->id ?? 1;
        $hallId = $request->hall_id ?: ($income->hall_id ?: $defaultHallId);

        $incomeCategory = null;
        if ($request->filled('income_category_id')) {
            $incomeCategory = \App\Models\IncomeCategory::find($request->income_category_id);
        }
        if (!$incomeCategory && $request->filled('category')) {
            $incomeCategory = \App\Models\IncomeCategory::firstOrCreate(['name' => $request->category]);
        }
        if (!$incomeCategory) {
            $incomeCategory = $income->incomeCategory ?: \App\Models\IncomeCategory::firstOrCreate(['name' => 'অন্যান্য আয়']);
        }

        $income->update([
            'amount' => $request->amount,
            'date' => $request->date,
            'income_category_id' => $incomeCategory->id,
            'category' => $incomeCategory->name,
            'description' => $request->description,
            'hall_id' => $hallId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'আয় আপডেট করা হয়েছে।',
            'data' => $income,
        ]);
    }

    public function deleteIncome(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        
        // Check if linked to a booking
        if ($income->booking_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'বুকিংয়ের সাথে যুক্ত আয় সরাসরি মুছে ফেলা সম্ভব নয়।',
            ], 422);
        }

        $income->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'আয় মুছে ফেলা হয়েছে।',
        ]);
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'expense_category_id' => 'nullable',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'hall_id' => 'nullable',
        ]);

        $defaultHallId = \App\Models\Hall::first()?->id ?? 1;
        $hallId = $request->hall_id ?: $defaultHallId;

        $expenseCategory = null;
        if ($request->filled('expense_category_id')) {
            $expenseCategory = \App\Models\ExpenseCategory::find($request->expense_category_id);
        }
        if (!$expenseCategory && $request->filled('category')) {
            $expenseCategory = \App\Models\ExpenseCategory::firstOrCreate(['name' => $request->category]);
        }
        if (!$expenseCategory) {
            $expenseCategory = \App\Models\ExpenseCategory::firstOrCreate(['name' => 'অন্যান্য ব্যয়']);
        }

        $expense = Expense::create([
            'amount' => $request->amount,
            'date' => $request->date,
            'expense_category_id' => $expenseCategory->id,
            'category' => $expenseCategory->name,
            'description' => $request->description,
            'hall_id' => $hallId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যয় সফলভাবে রেকর্ড করা হয়েছে।',
            'data' => $expense,
        ], 201);
    }

    public function updateExpense(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'expense_category_id' => 'nullable',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'hall_id' => 'nullable',
        ]);

        $expense = Expense::findOrFail($id);
        $defaultHallId = \App\Models\Hall::first()?->id ?? 1;
        $hallId = $request->hall_id ?: ($expense->hall_id ?: $defaultHallId);

        $expenseCategory = null;
        if ($request->filled('expense_category_id')) {
            $expenseCategory = \App\Models\ExpenseCategory::find($request->expense_category_id);
        }
        if (!$expenseCategory && $request->filled('category')) {
            $expenseCategory = \App\Models\ExpenseCategory::firstOrCreate(['name' => $request->category]);
        }
        if (!$expenseCategory) {
            $expenseCategory = $expense->expenseCategory ?: \App\Models\ExpenseCategory::firstOrCreate(['name' => 'অন্যান্য ব্যয়']);
        }

        $expense->update([
            'amount' => $request->amount,
            'date' => $request->date,
            'expense_category_id' => $expenseCategory->id,
            'category' => $expenseCategory->name,
            'description' => $request->description,
            'hall_id' => $hallId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যয় আপডেট করা হয়েছে।',
            'data' => $expense,
        ]);
    }

    public function deleteExpense(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যয় মুছে ফেলা হয়েছে।',
        ]);
    }

    public function getCategories(Request $request)
    {
        $incomeCategories = \App\Models\IncomeCategory::get();
        $expenseCategories = \App\Models\ExpenseCategory::get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'income' => $incomeCategories,
                'expense' => $expenseCategories,
            ],
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);

        $model = $request->type === 'income' ? \App\Models\IncomeCategory::class : \App\Models\ExpenseCategory::class;
        
        $category = $model::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ক্যাটাগরি তৈরি করা হয়েছে।',
            'data' => $category,
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);

        $model = $request->type === 'income' ? \App\Models\IncomeCategory::class : \App\Models\ExpenseCategory::class;
        $category = $model::findOrFail($id);
        
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ক্যাটাগরি আপডেট করা হয়েছে।',
            'data' => $category,
        ]);
    }

    public function deleteCategory(Request $request, $id)
    {
        $request->validate(['type' => 'required|in:income,expense']);
        
        $model = $request->type === 'income' ? \App\Models\IncomeCategory::class : \App\Models\ExpenseCategory::class;
        $category = $model::findOrFail($id);
        
        // Check for usage
        if ($request->type === 'income' && $category->incomes()->exists()) {
            return response()->json(['status' => 'error', 'message' => 'এই ক্যাটাগরিতে লেনদেন থাকায় এটি মুছে ফেলা সম্ভব নয়।'], 422);
        }
        if ($request->type === 'expense' && $category->expenses()->exists()) {
            return response()->json(['status' => 'error', 'message' => 'এই ক্যাটাগরিতে লেনদেন থাকায় এটি মুছে ফেলা সম্ভব নয়।'], 422);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ক্যাটাগরি মুছে ফেলা হয়েছে।',
        ]);
    }
}
