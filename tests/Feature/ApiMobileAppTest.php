<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\Booking;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Vendor;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiMobileAppTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Business Settings
        BusinessSetting::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'গ্র্যান্ড প্যালেস কনভেনশন হল',
                'phone' => '01711112222',
                'address' => 'ঢাকা, বাংলাদেশ',
                'capacity' => 800,
                'price_per_slot' => 50000,
                'default_server_rate' => 500,
            ]
        );

        // 2. Setup User
        $this->user = User::factory()->create([
            'name' => 'ম্যানেজার',
            'email' => 'manager@test.com',
            'phone' => '01711112222',
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_api_user_profile(): void
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'ম্যানেজার');
    }

    public function test_api_dashboard(): void
    {
        $response = $this->getJson('/api/dashboard');
        
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'stats' => [
                        'total_income',
                        'total_expense',
                        'total_bookings',
                        'total_staff',
                        'active_staff',
                        'total_customers',
                        'today_events',
                        'upcoming_events',
                        'total_due',
                        'month_booking_value',
                        'collection_rate',
                    ],
                    'upcoming_bookings',
                    'recent_bookings',
                ]
            ]);
    }

    public function test_api_dashboard_includes_kpis_and_upcoming_events(): void
    {
        $customer = Customer::create([
            'name' => 'আসন্ন ইভেন্ট গ্রাহক',
            'phone' => '01900000000',
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'total_amount' => 60000,
            'advance_amount' => 10000,
            'status' => 'confirmed',
        ]);

        $booking->items()->create([
            'event_date' => now()->addDays(3)->toDateString(),
            'slot' => 'day',
            'event_type' => 'বিয়ে',
            'guest_count' => 200,
            'sub_total' => 60000,
        ]);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats.total_customers', 1)
            ->assertJsonPath('data.stats.today_events', 0)
            ->assertJsonPath('data.stats.upcoming_events', 1)
            ->assertJsonPath('data.stats.total_due', 50000)
            ->assertJsonPath('data.upcoming_bookings.0.id', $booking->id);
    }

    public function test_api_tenant_settings_update(): void
    {
        $response = $this->postJson('/api/tenant/settings', [
            'name' => 'মমতা কমিউনিটি সেন্টার',
            'address' => 'মিরপুর, ঢাকা',
            'phone' => '01700000000',
            'invoice_conditions' => 'সম্পূর্ণ বিল আগে পরিশোধ করতে হবে।',
            'capacity' => 500,
            'price_per_slot' => 35000,
            'default_server_rate' => 600,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'মমতা কমিউনিটি সেন্টার')
            ->assertJsonPath('data.capacity', 500);

        $this->assertDatabaseHas('business_settings', [
            'company_name' => 'মমতা কমিউনিটি সেন্টার',
            'capacity' => 500,
            'price_per_slot' => 35000,
            'default_server_rate' => 600,
        ]);
    }

    public function test_api_customers_crud(): void
    {
        // 1. Create customer
        $createRes = $this->postJson('/api/customers', [
            'name' => 'কামাল হোসেন',
            'phone' => '01700000001',
            'address' => 'উত্তরা, ঢাকা',
        ]);
        $createRes->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'কামাল হোসেন');

        $customerId = $createRes->json('data.id');

        // 2. Get customers
        $listRes = $this->getJson('/api/customers');
        $listRes->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // 3. Show customer
        $showRes = $this->getJson("/api/customers/{$customerId}");
        $showRes->assertStatus(200)
            ->assertJsonPath('data.phone', '01700000001');
    }

    public function test_api_bookings_check_availability_and_creation(): void
    {
        // 1. Check availability for free slot
        $availRes = $this->getJson('/api/bookings/check-availability?' . http_build_query([
            'event_date' => '2026-12-15',
            'slot' => 'day',
        ]));
        $availRes->assertStatus(200)
            ->assertJsonPath('available', true);

        // 2. Create Booking
        $bookRes = $this->postJson('/api/bookings', [
            'customer_name' => 'রহিম আহমেদ',
            'customer_phone' => '01811111111',
            'customer_address' => 'ধানমন্ডি, ঢাকা',
            'total_amount' => 60000,
            'advance_amount' => 10000,
            'items' => [
                [
                    'event_date' => '2026-12-15',
                    'slot' => 'day',
                    'base_price' => 50000,
                    'sub_total' => 60000,
                ]
            ]
        ]);
        $bookRes->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $bookingId = $bookRes->json('data.id');

        // 3. Check availability again for the same slot (must be booked now)
        $conflictCheck = $this->getJson('/api/bookings/check-availability?' . http_build_query([
            'event_date' => '2026-12-15',
            'slot' => 'day',
        ]));
        $conflictCheck->assertStatus(200)
            ->assertJsonPath('available', false);

        // 4. Night slot on the same date should still be free
        $nightCheck = $this->getJson('/api/bookings/check-availability?' . http_build_query([
            'event_date' => '2026-12-15',
            'slot' => 'night',
        ]));
        $nightCheck->assertStatus(200)
            ->assertJsonPath('available', true);

        // 5. Duplicate slot booking attempt must fail with 422
        $duplicateAttempt = $this->postJson('/api/bookings', [
            'customer_name' => 'করিম সাহেব',
            'customer_phone' => '01822222222',
            'total_amount' => 50000,
            'items' => [
                [
                    'event_date' => '2026-12-15',
                    'slot' => 'day',
                    'base_price' => 50000,
                    'sub_total' => 50000,
                ]
            ]
        ]);
        $duplicateAttempt->assertStatus(422)
            ->assertJsonPath('status', 'error');

        // 6. Get Bookings list
        $bookingsList = $this->getJson('/api/bookings');
        $bookingsList->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // 7. Add payment to booking
        $paymentRes = $this->postJson("/api/bookings/{$bookingId}/payment", [
            'amount' => 15000,
            'date' => '2026-12-10',
            'description' => 'দ্বিতীয় কিস্তি',
        ]);
        $paymentRes->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_api_accounting_flow(): void
    {
        // 1. Create Income & Expense Categories
        $inCat = IncomeCategory::create(['name' => 'হল ভাড়া']);
        $exCat = ExpenseCategory::create(['name' => 'বিদ্যুৎ বিল']);

        // 2. Fetch categories
        $catRes = $this->getJson('/api/accounting/categories');
        $catRes->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // 3. Record Income
        $inRes = $this->postJson('/api/accounting/income', [
            'amount' => 25000,
            'date' => '2026-10-01',
            'income_category_id' => $inCat->id,
            'description' => 'হল বুকিং ফি',
        ]);
        $inRes->assertStatus(201)
            ->assertJsonPath('status', 'success');

        // 4. Record Expense
        $exRes = $this->postJson('/api/accounting/expense', [
            'amount' => 5000,
            'date' => '2026-10-02',
            'expense_category_id' => $exCat->id,
            'description' => 'অক্টোবর বিদ্যুৎ বিল',
        ]);
        $exRes->assertStatus(201)
            ->assertJsonPath('status', 'success');

        // 5. Get Accounting index & summary
        $accRes = $this->getJson('/api/accounting');
        $accRes->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'summary' => ['total_income', 'total_expense', 'net_profit'],
                    'transactions',
                    'trends',
                    'breakdown',
                ]
            ]);
    }

    public function test_api_staff_and_vendors(): void
    {
        // 1. Staff
        $staffRes = $this->postJson('/api/staff', [
            'name' => 'সাকিব',
            'phone' => '01733333333',
            'designation' => 'কেয়ারটেকার',
            'salary_amount' => 15000,
        ]);
        $staffRes->assertStatus(201);

        $staffList = $this->getJson('/api/staff');
        $staffList->assertStatus(200);

        // 2. Vendors
        $vendorRes = $this->postJson('/api/vendors', [
            'name' => 'রয়েল ডেকোরেশন',
            'type' => 'Decoration',
            'phone' => '01744444444',
            'commission_rate' => 10,
        ]);
        $vendorList = $this->getJson('/api/vendors');
        $vendorList->assertStatus(200);
    }

    public function test_api_assets_and_reports(): void
    {
        // 1. Assets CRUD
        $assetRes = $this->postJson('/api/assets', [
            'name' => 'VIP চেয়ার',
            'category' => 'Furniture',
            'total_stock' => 100,
            'available_stock' => 100,
            'unit_price' => 500,
        ]);
        $assetRes->assertStatus(201);

        $assetList = $this->getJson('/api/assets');
        $assetList->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // 2. Reports summary
        $repRes = $this->getJson('/api/reports/summary');
        $repRes->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_api_commissions(): void
    {
        $comList = $this->getJson('/api/commissions');
        $comList->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }
}
