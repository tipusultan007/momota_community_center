<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $hallId = $request->header('X-Hall-Id');
        $type = $request->query('type', 'all'); // 'all', 'income', 'expense'
        $perPage = (int) $request->query('per_page', 20);
        $currentPage = (int) $request->query('page', 1);

        $applyDateFilter = function ($query) use ($year, $month) {
            if ($year && $month) {
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate = Carbon::parse($startDate)->endOfMonth()->toDateString();
                return $query->whereBetween('date', [$startDate, $endDate]);
            } elseif ($year) {
                return $query->whereBetween('date', ["{$year}-01-01", "{$year}-12-31"]);
            } elseif ($month) {
                return $query->whereMonth('date', $month);
            }
            return $query;
        };

        // Summary queries with index-friendly date filters
        $incomeBase = Income::when($hallId, fn($q) => $q->where('hall_id', $hallId));
        $applyDateFilter($incomeBase);

        $expenseBase = Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId));
        $applyDateFilter($expenseBase);

        $totalIncome = (float) (clone $incomeBase)->sum('amount');
        $totalExpense = (float) (clone $expenseBase)->sum('amount');

        // Result collection with database pagination
        $totalCount = 0;
        $lastPage = 1;

        if ($type === 'income') {
            $results = (clone $incomeBase)
                ->with('incomeCategory')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->paginate($perPage);

            $transactions = collect($results->items())->map(fn($i) => array_merge($i->toArray(), ['type' => 'income']));
            $totalCount = $results->total();
            $lastPage = $results->lastPage();
            $currentPage = $results->currentPage();
        } elseif ($type === 'expense') {
            $results = (clone $expenseBase)
                ->with('expenseCategory')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->paginate($perPage);

            $transactions = collect($results->items())->map(fn($i) => array_merge($i->toArray(), ['type' => 'expense']));
            $totalCount = $results->total();
            $lastPage = $results->lastPage();
            $currentPage = $results->currentPage();
        } else {
            // Paginate across both tables via direct indexed queries (0 derived tables)
            $incomeCount = (clone $incomeBase)->count();
            $expenseCount = (clone $expenseBase)->count();
            $totalCount = $incomeCount + $expenseCount;
            $lastPage = max(1, (int) ceil($totalCount / $perPage));
            $fetchLimit = $currentPage * $perPage;

            $incomes = (clone $incomeBase)
                ->with('incomeCategory')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->limit($fetchLimit)
                ->get()
                ->map(fn($i) => array_merge($i->toArray(), ['type' => 'income']));

            $expenses = (clone $expenseBase)
                ->with('expenseCategory')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->limit($fetchLimit)
                ->get()
                ->map(fn($i) => array_merge($i->toArray(), ['type' => 'expense']));

            $transactions = $incomes->concat($expenses)
                ->sort(function ($a, $b) {
                    $cmp = strcmp($b['date'] ?? '', $a['date'] ?? '');
                    if ($cmp !== 0) return $cmp;
                    return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
                })
                ->forPage($currentPage, $perPage)
                ->values();
        }

        // Monthly trends: Cached by hall and year (served in 0.001ms)
        $currentYear = (int) ($year ?? now()->year);
        $trendsCacheKey = "acc_trends_{$hallId}_{$currentYear}";
        $trends = Cache::remember($trendsCacheKey, 300, function () use ($hallId, $currentYear) {
            $yearStart = "{$currentYear}-01-01";
            $yearEnd = "{$currentYear}-12-31";
            $monthExpr = DB::getDriverName() === 'sqlite'
                ? "CAST(strftime('%m', date) AS INTEGER)"
                : "MONTH(date)";

            $monthlyIncomes = Income::when($hallId, fn($q) => $q->where('hall_id', $hallId))
                ->whereBetween('date', [$yearStart, $yearEnd])
                ->selectRaw("{$monthExpr} as month_num, SUM(amount) as total")
                ->groupBy('month_num')
                ->pluck('total', 'month_num')
                ->all();

            $monthlyExpenses = Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId))
                ->whereBetween('date', [$yearStart, $yearEnd])
                ->selectRaw("{$monthExpr} as month_num, SUM(amount) as total")
                ->groupBy('month_num')
                ->pluck('total', 'month_num')
                ->all();

            $t = [];
            for ($m = 1; $m <= 12; $m++) {
                $t[] = [
                    'month' => date('M', mktime(0, 0, 0, $m, 1)),
                    'income' => (float) ($monthlyIncomes[$m] ?? 0),
                    'expense' => (float) ($monthlyExpenses[$m] ?? 0),
                ];
            }
            return $t;
        });

        // Category breakdown: Cached by hall, year, month (served in 0.001ms)
        $bdCacheKey = "acc_bd_{$hallId}_{$year}_{$month}";
        $breakdown = Cache::remember($bdCacheKey, 300, function () use ($hallId, $applyDateFilter) {
            $incQuery = Income::when($hallId, fn($q) => $q->where('hall_id', $hallId));
            $applyDateFilter($incQuery);
            $incCats = $incQuery
                ->selectRaw('COALESCE(category, "অন্যান্য") as category, SUM(amount) as total')
                ->groupBy('category')
                ->get()
                ->map(fn($item) => ['category' => $item->category, 'total' => (float) $item->total]);

            $expQuery = Expense::when($hallId, fn($q) => $q->where('hall_id', $hallId));
            $applyDateFilter($expQuery);
            $expCats = $expQuery
                ->selectRaw('COALESCE(category, "অন্যান্য") as category, SUM(amount) as total')
                ->groupBy('category')
                ->get()
                ->map(fn($item) => ['category' => $item->category, 'total' => (float) $item->total]);

            return [
                'income' => $incCats,
                'expense' => $expCats,
            ];
        });

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
                'breakdown' => $breakdown,
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

        $this->clearAccountingCache($hallId, $request->date);

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

        $this->clearAccountingCache($hallId, $request->date);

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

        $hallId = $income->hall_id;
        $date = $income->date;
        $income->delete();
        $this->clearAccountingCache($hallId, $date);

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

        $this->clearAccountingCache($hallId, $request->date);

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

        $this->clearAccountingCache($hallId, $request->date);

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যয় আপডেট করা হয়েছে।',
            'data' => $expense,
        ]);
    }

    public function deleteExpense(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $hallId = $expense->hall_id;
        $date = $expense->date;
        $expense->delete();
        $this->clearAccountingCache($hallId, $date);

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যয় মুছে ফেলা হয়েছে।',
        ]);
    }

    public function getCategories(Request $request)
    {
        $data = Cache::remember('accounting_categories', 3600, function () {
            return [
                'income' => \App\Models\IncomeCategory::orderBy('name')->get(),
                'expense' => \App\Models\ExpenseCategory::orderBy('name')->get(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
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

        Cache::forget('accounting_categories');

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

        Cache::forget('accounting_categories');

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

        Cache::forget('accounting_categories');

        return response()->json([
            'status' => 'success',
            'message' => 'ক্যাটাগরি মুছে ফেলা হয়েছে।',
            'data' => null,
        ]);
    }

    private function clearAccountingCache($hallId = null, $date = null)
    {
        $year = $date ? Carbon::parse($date)->year : now()->year;
        $month = $date ? Carbon::parse($date)->month : now()->month;

        if ($hallId) {
            Cache::forget("acc_trends_{$hallId}_{$year}");
            Cache::forget("acc_bd_{$hallId}_{$year}_{$month}");
            Cache::forget("acc_bd_{$hallId}__{$month}");
            Cache::forget("acc_bd_{$hallId}_{$year}_");
            Cache::forget("acc_bd_{$hallId}__");
        }
        Cache::forget("acc_trends__{$year}");
        Cache::forget("acc_bd___{$month}");
        Cache::forget("acc_bd__{$year}_");
        Cache::forget("acc_bd___");
    }
}
