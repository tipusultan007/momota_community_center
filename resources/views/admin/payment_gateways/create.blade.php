<x-tabler-layout :title="'নতুন গেটওয়ে'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">নতুন পেমেন্ট গেটওয়ে</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('admin.payment-gateways.store') }}" method="POST" class="card">
                @csrf
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">গেটওয়ের নাম</label>
                            <input type="text" name="name" class="form-control" placeholder="যেমন: bKash, Nagad, Rocket" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">অ্যাকাউন্টের ধরন</label>
                            <select name="account_type" class="form-select" required>
                                <option value="Personal">Personal</option>
                                <option value="Agent">Agent</option>
                                <option value="Merchant">Merchant</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">অ্যাকাউন্ট নম্বর</label>
                            <input type="text" name="account_number" class="form-control" placeholder="01xxxxxxxxx" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">অ্যাক্টিভ স্ট্যাটাস</label>
                            <label class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <span class="form-check-label">এই গেটওয়েটি পেমেন্ট নেয়ার জন্য চালু রাখুন</span>
                            </label>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">গ্রাহকদের জন্য নির্দেশিকা (ঐচ্ছিক)</label>
                            <textarea name="instructions" class="form-control" rows="3" placeholder="যেমন: সেন্ড মানি করে ট্রানজেকশন আইডি দিন।"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                    <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-link">বাতিল</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
