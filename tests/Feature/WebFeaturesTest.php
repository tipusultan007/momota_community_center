<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\Hall;
use App\Models\Customer;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Hall $hall;

    protected function setUp(): void
    {
        parent::setUp();

        BusinessSetting::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'গ্র্যান্ড প্যালেস কনভেনশন হল',
                'phone' => '01711112222',
                'address' => 'ঢাকা, বাংলাদেশ',
            ]
        );

        $this->hall = Hall::create([
            'name' => 'রয়েল ব্যাঙ্কোয়েট হল',
            'capacity' => 800,
            'price_per_slot' => 50000,
            'is_active' => true,
        ]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->user = User::factory()->create([
            'name' => 'ম্যানেজার',
            'email' => 'manager@test.com',
            'phone' => '01711112222',
        ]);
        $this->user->assignRole('Tenant Admin');
    }

    public function test_web_dashboard_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_web_halls_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/halls');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_web_bookings_full_lifecycle(): void
    {
        // 1. Create screen
        $createRes = $this->actingAs($this->user)->get('/bookings/create');
        $createRes->assertStatus(200);

        // 2. Store booking
        $storeRes = $this->actingAs($this->user)->post('/bookings', [
            'customer_name' => 'মোহাম্মদ আলী',
            'customer_phone' => '01755555555',
            'customer_address' => 'মিরপুর, ঢাকা',
            'total_amount' => 50000,
            'advance_amount' => 10000,
            'items' => [
                [
                    'hall_id' => $this->hall->id,
                    'event_date' => '2026-11-20',
                    'slot' => 'day',
                    'base_price' => 50000,
                    'sub_total' => 50000,
                ]
            ]
        ]);
        $storeRes->assertRedirect(route('bookings.index'));

        // 3. Conflict validation on duplicate date & slot
        $conflictRes = $this->actingAs($this->user)->post('/bookings', [
            'customer_name' => 'অন্য গ্রাহক',
            'customer_phone' => '01766666666',
            'total_amount' => 50000,
            'items' => [
                [
                    'hall_id' => $this->hall->id,
                    'event_date' => '2026-11-20',
                    'slot' => 'day',
                    'base_price' => 50000,
                    'sub_total' => 50000,
                ]
            ]
        ]);
        $conflictRes->assertSessionHasErrors('items');

        // 4. Bookings index
        $indexRes = $this->actingAs($this->user)->get('/bookings');
        $indexRes->assertStatus(200);
    }

    public function test_web_accounting_and_transactions(): void
    {
        $inCat = IncomeCategory::create(['name' => 'হল বুকিং ফি']);
        $exCat = ExpenseCategory::create(['name' => 'বিদ্যুৎ বিল']);

        // Incomes
        $incomesRes = $this->actingAs($this->user)->get('/incomes');
        $incomesRes->assertStatus(200);

        // Expenses
        $expensesRes = $this->actingAs($this->user)->get('/expenses');
        $expensesRes->assertStatus(200);

        // Transactions
        $transRes = $this->actingAs($this->user)->get('/transactions');
        $transRes->assertStatus(200);
    }

    public function test_web_settings_index_and_update(): void
    {
        $settingsRes = $this->actingAs($this->user)->get('/settings');
        $settingsRes->assertStatus(200);

        $updateRes = $this->actingAs($this->user)->post('/settings', [
            'name' => 'আপডেটেড কনভেনশন হল',
            'phone' => '01799999999',
            'address' => 'গুলশান, ঢাকা',
            'capacity' => 600,
            'price_per_slot' => 45000,
            'default_server_rate' => 500,
        ]);
        $updateRes->assertRedirect();

        $this->assertDatabaseHas('business_settings', [
            'company_name' => 'আপডেটেড কনভেনশন হল',
            'phone' => '01799999999',
        ]);
    }
}
