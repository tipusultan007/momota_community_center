<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'salary_amount' => 'required|numeric|min:0',
            'join_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $employee = Employee::create($request->except(['photo', 'documents']));

        if ($request->hasFile('photo')) {
            $employee->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $employee->addMedia($file)->toMediaCollection('documents');
            }
        }

        return redirect()->route('employees.index')->with('success', 'নতুন স্টাফ সফলভাবে যোগ করা হয়েছে।');
    }

    public function show(Employee $employee)
    {
        $salaries = $employee->salaries()->latest()->get();
        return view('employees.show', compact('employee', 'salaries'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'salary_amount' => 'required|numeric|min:0',
            'join_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $employee->update($request->except(['photo', 'documents']));

        if ($request->hasFile('photo')) {
            $employee->clearMediaCollection('photo');
            $employee->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $employee->addMedia($file)->toMediaCollection('documents');
            }
        }

        return redirect()->route('employees.index')->with('success', 'স্টাফের তথ্য আপডেট করা হয়েছে।');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'স্টাফের তথ্য মুছে ফেলা হয়েছে।');
    }

    public function paySalary(Request $request, Employee $employee)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'month' => 'required|string',
            'year' => 'required|integer',
        ]);

        $salary = Salary::create([
            'employee_id' => $employee->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'month' => $request->month,
            'year' => $request->year,
            'notes' => $request->notes,
        ]);

        // Automatically record as expense
        \App\Models\Expense::create([
            'amount' => $request->amount,
            'category' => 'staff_salary',
            'description' => $employee->name . ' এর ' . $request->month . ', ' . $request->year . ' মাসের বেতন',
            'date' => $request->payment_date,
        ]);

        return back()->with('success', 'বেতন প্রদান সফল হয়েছে এবং অটোমেটিক খরচে যোগ করা হয়েছে।');
    }
}
