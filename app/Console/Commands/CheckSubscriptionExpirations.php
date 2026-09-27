<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-subscription-expirations')]
#[Description('Command description')]
class CheckSubscriptionExpirations extends Command
{
    protected $signature = 'check:subscriptions';
    protected $description = 'Check for expiring and expired subscriptions and notify parties';

    public function handle()
    {
        $today = now()->startOfDay();
        $warningDate = now()->addDays(3)->startOfDay();

        // 1. Warning for 3 days before expiry
        $expiringTenants = \App\Models\Tenant::whereDate('subscription_ends_at', $warningDate)->get();
        foreach ($expiringTenants as $tenant) {
            $adminUser = $tenant->users()->first();
            if ($adminUser) {
                $adminUser->notify(new \App\Notifications\SubscriptionLifecycleNotification($tenant, 'warning'));
            }
            $this->info("Sent expiry warning to " . $tenant->name);
        }

        // 2. Expired today (or yesterday)
        $expiredTenants = \App\Models\Tenant::where('is_active', true)
            ->whereDate('subscription_ends_at', '<', $today)
            ->get();

        $superAdmins = \App\Models\Admin::all();

        foreach ($expiredTenants as $tenant) {
            // Deactivate tenant
            $tenant->update(['is_active' => false]);

            // Notify Tenant
            $adminUser = $tenant->users()->first();
            if ($adminUser) {
                $adminUser->notify(new \App\Notifications\SubscriptionLifecycleNotification($tenant, 'expired'));
            }

            // Notify Super Admins
            foreach ($superAdmins as $admin) {
                $admin->notify(new \App\Notifications\SubscriptionLifecycleNotification($tenant, 'expired'));
            }

            $this->info("Deactivated and notified expired tenant: " . $tenant->name);
        }
    }
}
