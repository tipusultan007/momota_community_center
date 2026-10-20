<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles and Permissions
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
        ]);

        // 2. Demo Business Setting
        \App\Models\BusinessSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'মমতা কমিউনিটি সেন্টার',
                'phone' => '01711112222',
                'address' => 'ঢাকা, বাংলাদেশ',
                'capacity' => 500,
                'price_per_slot' => 30000,
                'default_server_rate' => 500,
            ]
        );

        // 3. Demo Admin User
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'ম্যানেজার',
                'phone' => '01711112222',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Assign Admin role
        $user->assignRole('Tenant Admin');

        // 4. Standard Categories
        $incomeCats = ['ভেন্যু বুকিং', 'ক্যাটারিং ও সার্ভিস', 'অতিরিক্ত সেবা', 'অন্যান্য আয়'];
        foreach ($incomeCats as $cat) {
            \App\Models\IncomeCategory::firstOrCreate(['name' => $cat]);
        }

        $expenseCats = ['পরিবেশনকারী খরচ', 'ইউটিলিটি ও বিদ্যুৎ বিল', 'রক্ষণাবেক্ষণ ও মেরামত', 'স্টাফ বেতন ও ভাতা', 'অন্যান্য ব্যয়'];
        foreach ($expenseCats as $cat) {
            \App\Models\ExpenseCategory::firstOrCreate(['name' => $cat]);
        }
    }
}
