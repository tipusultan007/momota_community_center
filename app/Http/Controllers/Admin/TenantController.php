<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantController extends Controller
{
    protected array $plans = [
        'free' => 'ফ্রি ট্রায়াল (Free Trial)',
        'basic' => 'স্টার্টআপ / বেসিক (Startup / Basic)',
        'pro' => 'প্রফেশনাল (Professional)',
        'enterprise' => 'এন্টারপ্রাইজ (Enterprise)',
    ];

    /**
     * Display a listing of all tenants (vendors) in the system.
     */
    public function index(Request $request)
    {
        $query = Tenant::with(['users' => function($q) {
            $q->latest();
        }])->withCount(['users', 'halls', 'bookings']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('users', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Plan
        if ($request->filled('plan') && $request->plan !== 'all') {
            $query->where('plan', $request->plan);
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('is_active', true)->count(),
            'inactive' => Tenant::where('is_active', false)->count(),
            'expired' => Tenant::where('subscription_ends_at', '<', now())->count(),
        ];

        $plans = $this->plans;

        return view('admin.tenants.index', compact('tenants', 'stats', 'plans'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        $plans = $this->plans;
        return view('admin.tenants.create', compact('plans'));
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug|alpha_dash',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'plan' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'trial_ends_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'invoice_conditions' => 'nullable|string|max:2000',
            'hall_limit' => 'nullable|integer|min:1',
            'staff_limit' => 'nullable|integer|min:1',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email',
            'owner_phone' => 'required|string|max:30',
            'owner_password' => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($request) {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            $settings = [
                'hall_limit' => $request->hall_limit ?? 3,
                'staff_limit' => $request->staff_limit ?? 10,
                'sms_enabled' => $request->boolean('sms_enabled'),
                'auto_email_confirmation' => $request->boolean('auto_email_confirmation'),
                'auto_sms_confirmation' => $request->boolean('auto_sms_confirmation'),
            ];

            $tenant = Tenant::create([
                'name' => $request->name,
                'slug' => Str::slug($request->slug),
                'phone' => $request->phone,
                'address' => $request->address,
                'plan' => $request->plan,
                'is_active' => $request->boolean('is_active'),
                'trial_ends_at' => $request->trial_ends_at ? Carbon::parse($request->trial_ends_at) : null,
                'subscription_ends_at' => $request->subscription_ends_at ? Carbon::parse($request->subscription_ends_at)->endOfDay() : null,
                'logo_path' => $logoPath,
                'invoice_conditions' => $request->invoice_conditions,
                'settings' => $settings,
            ]);

            // Create Owner User
            $owner = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'phone' => $request->owner_phone,
                'password' => Hash::make($request->owner_password),
            ]);

            // Default Hall
            Hall::create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name . ' - মূল হল',
                'capacity' => 500,
                'price_per_slot' => 30000,
                'default_server_rate' => 500,
                'is_active' => true,
            ]);
        });

        return redirect()->route('admin.tenants.index')->with('success', 'নতুন ভেন্ডর সফলভাবে তৈরি করা হয়েছে।');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load([
            'users', 
            'halls', 
            'bookings' => fn($q) => $q->latest()->take(10), 
            'subscriptionHistories' => fn($q) => $q->latest()->take(10)
        ]);

        $stats = [
            'total_bookings' => $tenant->bookings()->count(),
            'total_revenue' => $tenant->bookings()->sum('total_amount'),
            'total_halls' => $tenant->halls()->count(),
            'total_users' => $tenant->users()->count(),
        ];

        $plans = $this->plans;

        return view('admin.tenants.show', compact('tenant', 'stats', 'plans'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        $tenant->load('users');
        $owner = $tenant->users->first();
        $plans = $this->plans;

        return view('admin.tenants.edit', compact('tenant', 'owner', 'plans'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $owner = $tenant->users->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:tenants,slug,' . $tenant->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'plan' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'trial_ends_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'remove_logo' => 'nullable|boolean',
            'invoice_conditions' => 'nullable|string|max:2000',
            'hall_limit' => 'nullable|integer|min:1',
            'staff_limit' => 'nullable|integer|min:1',
            // Owner User Validation
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255|unique:users,email,' . ($owner?->id ?? 'NULL'),
            'owner_phone' => 'nullable|string|max:30',
            'owner_password' => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($request, $tenant, $owner) {
            $logoPath = $tenant->logo_path;

            // Handle logo removal
            if ($request->boolean('remove_logo')) {
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = null;
            }

            // Handle new logo upload
            if ($request->hasFile('logo')) {
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            // Merge settings
            $currentSettings = $tenant->settings ?? [];
            $newSettings = [
                'hall_limit' => $request->filled('hall_limit') ? (int)$request->hall_limit : ($currentSettings['hall_limit'] ?? 5),
                'staff_limit' => $request->filled('staff_limit') ? (int)$request->staff_limit : ($currentSettings['staff_limit'] ?? 10),
                'sms_enabled' => $request->has('sms_enabled') ? $request->boolean('sms_enabled') : ($currentSettings['sms_enabled'] ?? false),
                'auto_email_confirmation' => $request->has('auto_email_confirmation') ? $request->boolean('auto_email_confirmation') : ($currentSettings['auto_email_confirmation'] ?? false),
                'auto_sms_confirmation' => $request->has('auto_sms_confirmation') ? $request->boolean('auto_sms_confirmation') : ($currentSettings['auto_sms_confirmation'] ?? false),
                'auto_payment_reminder' => $request->has('auto_payment_reminder') ? $request->boolean('auto_payment_reminder') : ($currentSettings['auto_payment_reminder'] ?? false),
                'auto_event_greeting' => $request->has('auto_event_greeting') ? $request->boolean('auto_event_greeting') : ($currentSettings['auto_event_greeting'] ?? false),
            ];
            $mergedSettings = array_merge($currentSettings, $newSettings);

            // Update Tenant
            $tenant->update([
                'name' => $request->name,
                'slug' => Str::slug($request->slug),
                'phone' => $request->phone,
                'address' => $request->address,
                'plan' => $request->plan,
                'is_active' => $request->boolean('is_active'),
                'trial_ends_at' => $request->trial_ends_at ? Carbon::parse($request->trial_ends_at) : null,
                'subscription_ends_at' => $request->subscription_ends_at ? Carbon::parse($request->subscription_ends_at)->endOfDay() : null,
                'logo_path' => $logoPath,
                'invoice_conditions' => $request->invoice_conditions,
                'settings' => $mergedSettings,
            ]);

            // Update or Create Primary Owner User
            if ($owner) {
                $ownerData = [];
                if ($request->filled('owner_name')) $ownerData['name'] = $request->owner_name;
                if ($request->filled('owner_email')) $ownerData['email'] = $request->owner_email;
                if ($request->filled('owner_phone')) $ownerData['phone'] = $request->owner_phone;
                if ($request->filled('owner_password')) $ownerData['password'] = Hash::make($request->owner_password);

                if (!empty($ownerData)) {
                    $owner->update($ownerData);
                }
            } elseif ($request->filled('owner_email')) {
                User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $request->owner_name ?? $tenant->name,
                    'email' => $request->owner_email,
                    'phone' => $request->owner_phone ?? $tenant->phone,
                    'password' => Hash::make($request->owner_password ?? '12345678'),
                ]);
            }
        });

        return redirect()->route('admin.tenants.show', $tenant)->with('success', 'ভেন্ডরের তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            if ($tenant->logo_path && Storage::disk('public')->exists($tenant->logo_path)) {
                Storage::disk('public')->delete($tenant->logo_path);
            }
            $tenant->delete();
        });

        return redirect()->route('admin.tenants.index')->with('success', 'ভেন্ডর সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
