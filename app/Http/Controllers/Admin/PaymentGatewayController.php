<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::latest()->get();
        return view('admin.payment_gateways.index', compact('gateways'));
    }

    public function create()
    {
        return view('admin.payment_gateways.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_type' => 'required|string|max:50',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        PaymentGateway::create([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'instructions' => $request->instructions,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.payment-gateways.index')->with('success', 'Payment gateway added successfully.');
    }

    public function edit(PaymentGateway $paymentGateway)
    {
        return view('admin.payment_gateways.edit', compact('paymentGateway'));
    }

    public function update(Request $request, PaymentGateway $paymentGateway)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_type' => 'required|string|max:50',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $paymentGateway->update([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'instructions' => $request->instructions,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.payment-gateways.index')->with('success', 'Payment gateway updated successfully.');
    }

    public function destroy(PaymentGateway $paymentGateway)
    {
        $paymentGateway->delete();
        return redirect()->route('admin.payment-gateways.index')->with('success', 'Payment gateway deleted successfully.');
    }
}
