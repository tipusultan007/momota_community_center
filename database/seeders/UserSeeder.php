<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: The original hashes from phpMyAdmin export were truncated with ellipsis (...).
        // A functional bcrypt password ('password123') is set as default so users can log in.
        $users = [
            [
                'id' => 1,
                'name' => 'টিপু সুলতান',
                'email' => 'tipusultan.ctg@gmail.com',
                'phone' => '01829321686',
                'address' => null,
                'email_verified_at' => '2026-09-25 01:03:36',
                'password' => Hash::make('password123'),
                'remember_token' => 'YyQOApWWuy',
                'created_at' => '2026-09-25 01:03:36',
                'updated_at' => '2026-09-25 09:24:16',
                'role' => 'Tenant Admin',
            ],
            [
                'id' => 2,
                'name' => 'মোঃ পারভেজ মোশাররফ',
                'email' => 'parvez@umairit.com',
                'phone' => '01866606081',
                'address' => null,
                'email_verified_at' => null,
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => '2026-09-25 18:18:07',
                'updated_at' => '2026-09-30 13:55:23',
                'role' => 'Tenant Admin',
            ],
            [
                'id' => 3,
                'name' => 'মোঃ সেলিম মিয়া',
                'email' => 'selim@gmail.com',
                'phone' => '01819609315',
                'address' => null,
                'email_verified_at' => null,
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => '2026-09-25 18:18:49',
                'updated_at' => '2026-09-25 18:18:49',
                'role' => 'Tenant Admin',
            ],
        ];

        // Clean up legacy test account if present with a different id
        User::where('email', 'test@example.com')->whereNotIn('id', [1, 2, 3])->delete();

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['id' => $userData['id']],
                $userData
            );

            if ($role && method_exists($user, 'assignRole')) {
                $user->syncRoles([$role]);
            }
        }
    }
}
