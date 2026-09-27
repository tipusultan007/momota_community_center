<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\SubscriptionHistory;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = SubscriptionHistory::with('tenant')
                            ->latest()
                            ->paginate(15);
                            
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function edit(SubscriptionHistory $history)
    {
        $history->load('tenant');
        $tenants = Tenant::orderBy('name')->get();
        $gateways = PaymentGateway::where('is_active', true)->get();
        $plans = [
            'basic' => 'স্টার্টআপ (Startup)',
            'pro' => 'প্রফেশনাল (Professional)',
            'enterprise' => 'এন্টারপ্রাইজ (Enterprise)',
        ];

        return view('admin.subscriptions.edit', compact('history', 'tenants', 'gateways', 'plans'));
    }

    public function update(Request $request, SubscriptionHistory $history)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'sender_number' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:100',
            'status' => 'required|in:pending,active,rejected',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'reject_reason' => 'nullable|string|max:500',
            'sync_tenant' => 'nullable|boolean',
        ]);

        $oldStatus = $history->status;

        $startsAt = $request->filled('starts_at')
            ? Carbon::parse($request->starts_at)
            : ($request->status === 'active' ? ($history->starts_at ?? Carbon::now()) : null);

        $endsAt = $request->filled('ends_at')
            ? Carbon::parse($request->ends_at)->endOfDay()
            : ($request->status === 'active' ? ($history->ends_at ?? Carbon::now()->addMonth()->endOfDay()) : null);

        $history->update([
            'tenant_id' => $validated['tenant_id'],
            'plan' => $validated['plan'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'sender_number' => $validated['sender_number'],
            'transaction_id' => $validated['transaction_id'],
            'status' => $validated['status'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'reject_reason' => $validated['status'] === 'rejected' ? $validated['reject_reason'] : null,
        ]);

        // Sync with tenant account if requested
        if ($request->boolean('sync_tenant', true) && $history->tenant) {
            if ($history->status === 'active') {
                $history->tenant->update([
                    'plan' => $history->plan,
                    'subscription_ends_at' => $history->ends_at,
                    'is_active' => true,
                ]);
            }
        }

        // Notify tenant user if status was changed to active or rejected
        if ($oldStatus !== $history->status && in_array($history->status, ['active', 'rejected'])) {
            try {
                $adminUser = $history->tenant?->users()->first();
                if ($adminUser) {
                    $adminUser->notify(new \App\Notifications\SubscriptionStatusNotification($history));
                }
            } catch (\Exception $e) {
                \Log::error('Tenant notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.subscriptions.index')->with('success', 'সাবস্ক্রিপশন সফলভাবে আপডেট করা হয়েছে।');
    }

    public function approve(SubscriptionHistory $history)
    {
        if ($history->status === 'active') {
            return back()->with('error', 'এই সাবস্ক্রিপশনটি ইতিমধ্যেই অ্যাক্টিভ।');
        }

        // Calculate subscription period ends at
        $months = 1; // Default to 1 month for our system unless specified
        $duration = 1;

        if (strtolower($history->plan) == 'basic' || strtolower($history->plan) == 'pro' || strtolower($history->plan) == 'enterprise') {
            $duration = 1;
        }

        $now = Carbon::now();
        $history->update([
            'status' => 'active',
            'starts_at' => $now,
            'ends_at' => $now->copy()->addMonths($duration),
        ]);

        // Activate the tenant
        if ($history->tenant) {
            $history->tenant->update([
                'plan' => $history->plan,
                'subscription_ends_at' => $history->ends_at,
                'is_active' => true,
            ]);
        }

        // Notify Tenant Admin
        try {
            $adminUser = $history->tenant->users()->first(); // Assuming first user is the admin
            if ($adminUser) {
                $adminUser->notify(new \App\Notifications\SubscriptionStatusNotification($history));
            }
        } catch (\Exception $e) {
            \Log::error('Tenant notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'পেমেন্ট অনুমোদিত হয়েছে এবং সাবস্ক্রিপশন অ্যাক্টিভ করা হয়েছে।');
    }

    public function reject(Request $request, SubscriptionHistory $history)
    {
        if ($history->status === 'rejected') {
            return back()->with('error', 'ইতিমধ্যেই বাতিল করা হয়েছে।');
        }

        $history->update([
            'status' => 'rejected',
            'reject_reason' => $request->input('reject_reason'),
        ]);

        // Notify Tenant Admin
        try {
            $adminUser = $history->tenant->users()->first();
            if ($adminUser) {
                $adminUser->notify(new \App\Notifications\SubscriptionStatusNotification($history));
            }
        } catch (\Exception $e) {
            \Log::error('Tenant notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'পেমেন্ট রিকোয়েস্ট বাতিল করা হয়েছে।');
    }
}
