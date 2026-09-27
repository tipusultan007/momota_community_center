<x-tabler-layout :title="'সাবস্ক্রিপশন সম্পাদনা'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('admin.subscriptions.index') }}" class="text-secondary text-decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                            সাবস্ক্রিপশন তালিকায় ফিরে যান
                        </a>
                    </div>
                    <h2 class="page-title mt-1">সাবস্ক্রিপশন সম্পাদনা (#{{ $history->id }})</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                        তালিকায় ফিরে যান
                    </a>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body" x-data="{ status: '{{ old('status', $history->status) }}' }">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>{{ session('success') }}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="fw-bold mb-1">ফর্মটি সঠিকভাবে পূরণ করুন:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <form action="{{ route('admin.subscriptions.update', $history->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row row-cards">
                    <!-- Left Column: Main Details -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">ভেন্ডর ও প্যাকেজ তথ্য</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">ভেন্ডর (টেন্যান্ট)</label>
                                        <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                                            <option value="">ভেন্ডর নির্বাচন করুন</option>
                                            @foreach($tenants as $tenant)
                                                <option value="{{ $tenant->id }}" {{ old('tenant_id', $history->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                                    {{ $tenant->name }} (ID: #{{ $tenant->id }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tenant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label required">প্যাকেজ (প্ল্যান)</label>
                                        <select name="plan" class="form-select @error('plan') is-invalid @enderror" required>
                                            @foreach($plans as $key => $title)
                                                <option value="{{ $key }}" {{ old('plan', strtolower($history->plan)) == $key ? 'selected' : '' }}>
                                                    {{ $title }}
                                                </option>
                                            @endforeach
                                            @if(!array_key_exists(strtolower($history->plan), $plans))
                                                <option value="{{ $history->plan }}" selected>{{ ucfirst($history->plan) }}</option>
                                            @endif
                                        </select>
                                        @error('plan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label required">পরিশোধের পরিমাণ (৳)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" min="0" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $history->amount) }}" required>
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">পেমেন্ট মেথড</label>
                                        <input type="text" list="payment-methods" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" value="{{ old('payment_method', $history->payment_method) }}" placeholder="যেমন: bKash, Nagad, Bank">
                                        <datalist id="payment-methods">
                                            <option value="bKash">
                                            <option value="Nagad">
                                            <option value="Rocket">
                                            <option value="Bank Transfer">
                                            <option value="Cash">
                                            @foreach($gateways as $gateway)
                                                <option value="{{ $gateway->name }}">
                                            @endforeach
                                        </datalist>
                                        @error('payment_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">প্রেরকের নম্বর (Sender Number)</label>
                                        <input type="text" name="sender_number" class="form-control @error('sender_number') is-invalid @enderror" value="{{ old('sender_number', $history->sender_number) }}" placeholder="যেমন: 017xxxxxxxx">
                                        @error('sender_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">ট্রানজেকশন আইডি (TrxID)</label>
                                        <input type="text" name="transaction_id" class="form-control font-monospace @error('transaction_id') is-invalid @enderror" value="{{ old('transaction_id', $history->transaction_id) }}" placeholder="যেমন: 9J3K9D0...">
                                        @error('transaction_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Reason Box (Conditional) -->
                        <div class="card mb-3" x-show="status === 'rejected'" x-cloak>
                            <div class="card-header bg-danger-lt">
                                <h3 class="card-title text-danger">বাতিলের কারণ</h3>
                            </div>
                            <div class="card-body">
                                <label class="form-label">কারণ লিখুন (ভেন্ডর দেখতে পাবেন)</label>
                                <textarea name="reject_reason" class="form-control @error('reject_reason') is-invalid @enderror" rows="3" placeholder="বাতিলের কারণ লিখুন...">{{ old('reject_reason', $history->reject_reason) }}</textarea>
                                @error('reject_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Status & Validity -->
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">স্ট্যাটাস ও সময়সীমা</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required">সাবস্ক্রিপশন স্ট্যাটাস</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" x-model="status" required>
                                        <option value="pending">অপেক্ষমান (Pending)</option>
                                        <option value="active">অনুমোদিত / সক্রিয় (Active)</option>
                                        <option value="rejected">বাতিল (Rejected)</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">শুরুর তারিখ</label>
                                    <input type="date" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', $history->starts_at ? $history->starts_at->format('Y-m-d') : '') }}">
                                    <small class="form-hint">খালি রাখলে এবং স্ট্যাটাস সক্রিয় থাকলে বর্তমান তারিখ যুক্ত হবে।</small>
                                    @error('starts_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">মেয়াদ শেষ হওয়ার তারিখ</label>
                                    <input type="date" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at', $history->ends_at ? $history->ends_at->format('Y-m-d') : '') }}">
                                    <small class="form-hint">খালি রাখলে স্বয়ংক্রিয়ভাবে ১ মাস মেয়াদ নির্ধারণ হবে।</small>
                                    @error('ends_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-3">

                                <div class="mb-1">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="sync_tenant" value="1" checked>
                                        <span class="form-check-label fw-medium">টেন্যান্ট অ্যাকাউন্টে মেয়াদ আপডেট করুন</span>
                                    </label>
                                    <small class="text-secondary d-block mt-1">
                                        স্ট্যাটাস অ্যাক্টিভ থাকলে ভেন্ডরের মেইন অ্যাকাউন্টের সাবস্ক্রিপশন মেয়াদ ও প্যাকেজ সাথে সাথে আপডেট হবে।
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Info Card -->
                        <div class="card bg-muted-lt">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="avatar avatar-sm bg-primary-lt me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                                    </span>
                                    <strong>রিকোয়েস্ট তথ্য</strong>
                                </div>
                                <div class="text-secondary small">
                                    <div><strong>তৈরির তারিখ:</strong> {{ $history->created_at->format('d M Y, h:i A') }}</div>
                                    @if($history->updated_at)
                                        <div><strong>সর্বশেষ আপডেট:</strong> {{ $history->updated_at->format('d M Y, h:i A') }}</div>
                                    @endif
                                    @if($history->tenant)
                                        <div class="mt-2 pt-2 border-top">
                                            <strong>ভেন্ডর ফোন:</strong> {{ $history->tenant->phone ?? 'N/A' }}<br>
                                            <strong>বর্তমান সাবস্ক্রিপশন শেষ:</strong> {{ $history->tenant->subscription_ends_at ? $history->tenant->subscription_ends_at->format('d M Y') : 'নাই' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Submit Footer -->
                <div class="card mt-3">
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                            বাতিল করুন
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                            পরিবর্তন সংরক্ষণ করুন
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
