<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $gateways = \App\Models\PaymentGateway::where('is_active', true)->get();
        $plans = [
            'basic' => [
                'name' => 'স্টার্টআপ',
                'price' => 1000,
                'features' => ['১টি হল ম্যানেজমেন্ট', 'বেসিক রিপোর্ট', 'ইমেইল সাপোর্ট', 'ইনভয়েস জেনারেশন'],
            ],
            'pro' => [
                'name' => 'প্রফেশনাল',
                'price' => 2500,
                'features' => ['আনলিমিটেড হল সাপোর্ট', 'অ্যাডভান্সড PDF রিপোর্ট', 'ইনভেন্টরি ট্র্যাকিং', 'প্রায়োরিটি সাপোর্ট'],
            ],
            'enterprise' => [
                'name' => 'এন্টারপ্রাইজ',
                'price' => 5000,
                'features' => ['কাস্টম ব্র্যান্ডিং', 'API অ্যাক্সেস', 'ফুল ইনভেন্টরি ও অ্যাসেট', 'ডেডিকেটেড অ্যাকাউন্ট ম্যানেজার'],
            ],
        ];

        return view('subscriptions.index', compact('tenant', 'plans', 'gateways'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro,enterprise',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'sender_number' => 'required|string',
            'transaction_id' => 'required|string',
        ]);

        $tenant = auth()->user()->tenant;
        
        \App\Models\SubscriptionHistory::create([
            'tenant_id' => $tenant->id,
            'plan' => $request->plan,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'sender_number' => $request->sender_number,
            'transaction_id' => $request->transaction_id,
            'status' => 'pending',
        ]);

        return redirect()->route('subscriptions.history')->with('success', 'আপনার পেমেন্ট রিকোয়েস্ট জমা দেওয়া হয়েছে। অ্যাডমিন কনফার্ম করার পর সাবস্ক্রিপশন অ্যাক্টিভ হবে।');
    }

    public function history()
    {
        $histories = \App\Models\SubscriptionHistory::where('tenant_id', auth()->user()->tenant->id)
                        ->latest()
                        ->paginate(15);
        return view('subscriptions.history', compact('histories'));
    }

    public function expired()
    {
        $tenant = auth()->user()->tenant;
        
        // If they somehow have an active sub, send them back
        if (($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) || 
            ($tenant->subscription_ends_at && $tenant->subscription_ends_at->isFuture())) {
            return redirect()->route('dashboard');
        }

        return view('subscriptions.expired', compact('tenant'));
    }
}
