<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        $this->createDefaultIncomeCategories($tenant);
        $this->createDefaultExpenseCategories($tenant);
    }

    private function createDefaultIncomeCategories(Tenant $tenant): void
    {
        $categories = [
            ['name' => 'হলের বুকিং ভাড়া', 'description' => 'হলের ভেন্যু বুকিং থেকে অর্জিত আয়।'],
            ['name' => 'ক্যাটারিং সার্ভিস', 'description' => 'খাবার ও ক্যাটারিং সার্ভিস থেকে আয়।'],
            ['name' => 'কমিশন (ভেন্ডর)', 'description' => 'ডেকোরেশন, সাউন্ড ও অন্যান্য ভেন্ডর থেকে প্রাপ্ত কমিশন।'],
            ['name' => 'অন্যান্য আয়', 'description' => 'বিবিধ উৎস থেকে আয়।'],
        ];

        foreach ($categories as $category) {
            IncomeCategory::create(array_merge($category, ['tenant_id' => $tenant->id]));
        }
    }

    private function createDefaultExpenseCategories(Tenant $tenant): void
    {
        $categories = [
            ['name' => 'স্টাফ স্যালারি', 'description' => 'কর্মচারীদের বেতন ও বোনাস।'],
            ['name' => 'বিদ্যুৎ বিল', 'description' => 'মাসিক বিদ্যুৎ বিল পরিশোধ।'],
            ['name' => 'পানি ও গ্যাস বিল', 'description' => 'ইউটিলিটি বিল পরিশোধ।'],
            ['name' => 'ভেন্ডর পেমেন্ট', 'description' => 'ভেন্ডরদের বিল পরিশোধ।'],
            ['name' => 'মেরামত ও রক্ষণাবেক্ষণ', 'description' => 'হলের রক্ষণাবেক্ষণ সংক্রান্ত খরচ।'],
            ['name' => 'অফিস খরচ', 'description' => 'স্টেশনারি ও অন্যান্য অফিস সংক্রান্ত ব্যয়।'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create(array_merge($category, ['tenant_id' => $tenant->id]));
        }
    }
}
