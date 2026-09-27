<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = Employee::get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'role' => $employee->designation,
                    'phone' => $employee->phone,
                    'status' => $employee->status ?? 'active',
                    'current_task' => 'তৈরি করা হচ্ছে...',
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $staff,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:100',
            'salary_amount' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'join_date' => 'nullable|date',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'salary_amount' => $request->salary_amount,
            'address' => $request->address,
            'join_date' => $request->join_date ?? now(),
            'is_active' => true,
            'status' => 'clocked-out',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'স্টাফ সফলভাবে যোগ করা হয়েছে।',
            'data' => $employee,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $employee = Employee::with('salaries')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $employee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:100',
            'salary_amount' => 'required|numeric|min:0',
            'address' => 'nullable|string',
        ]);

        $employee->update($request->only(['name', 'phone', 'designation', 'salary_amount', 'address', 'is_active']));

        return response()->json([
            'status' => 'success',
            'message' => 'স্টাফ তথ্য আপডেট করা হয়েছে।',
            'data' => $employee,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'স্টাফ মুছে ফেলা হয়েছে।',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:clocked-in,clocked-out,on-break,active,inactive',
        ]);

        $employee->update(['status' => $request->status, 'is_active' => $request->status !== 'inactive']);

        return response()->json([
            'status' => 'success',
            'message' => 'স্টাফ স্ট্যাটাস আপডেট করা হয়েছে।',
            'data' => $employee,
        ]);
    }

    public function paySalary(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'month' => 'required|string|max:20',
            'year' => 'required|integer',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        return \DB::transaction(function () use ($request, $employee) {
            $salary = $employee->salaries()->create([
                'amount' => $request->amount,
                'month' => $request->month,
                'year' => $request->year,
                'payment_date' => $request->payment_date,
                'notes' => $request->notes,
            ]);

            // Record as expense in accounting
            \App\Models\Expense::create([
                'amount' => $request->amount,
                'category' => 'Salary',
                'description' => "Salary for {$employee->name} ({$request->month} {$request->year})",
                'date' => $request->payment_date,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'বেতন প্রদান সফল হয়েছে।',
                'data' => $salary,
            ], 201);
        });
    }

    public function salaries(Request $request)
    {
        $salaries = \App\Models\Salary::with('employee')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $salaries,
        ]);
    }
}
