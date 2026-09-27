<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    public function show(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($request->only(['name', 'phone', 'email', 'address']));

        return response()->json([
            'status' => 'success',
            'message' => 'কাস্টমার সফলভাবে যোগ করা হয়েছে।',
            'data' => $customer,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer->update($request->only(['name', 'phone', 'email', 'address']));

        return response()->json([
            'status' => 'success',
            'message' => 'কাস্টমার প্রোফাইল আপডেট করা হয়েছে।',
            'data' => $customer,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'কাস্টমার মুছে ফেলা হয়েছে।',
        ]);
    }
}
