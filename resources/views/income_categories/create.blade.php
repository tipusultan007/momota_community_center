<x-tabler-layout :title="'নতুন আয় ক্যাটাগরি তৈরি করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">নতুন আয় ক্যাটাগরি তৈরি করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('income-categories.store') }}" method="POST" class="card">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">নাম</label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: বুকিং এডভান্স" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">বিবরণ</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="বিস্তারিত লিখুন..."></textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                    <a href="{{ route('income-categories.index') }}" class="btn btn-link">ফিরে যান</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
