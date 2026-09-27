<x-tabler-layout :title="'ভেন্ডর সম্পাদনা - ' . $tenant->name">
    <x-slot:header>
        <div class="page-header d-print-none mb-3">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('admin.tenants.index') }}" class="text-secondary text-decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12" /><line x1="5" y1="12" x2="11" y2="18" /><line x1="5" y1="12" x2="11" y2="6" /></svg>
                            ভেন্ডর তালিকা
                        </a>
                    </div>
                    <h2 class="page-title d-flex align-items-center gap-2">
                        <span>ভেন্ডর তথ্য সম্পাদনা:</span>
                        <span class="text-primary">{{ $tenant->name }}</span>
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" /></svg>
                            প্রোফাইল দেখুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                </div>
                <div>
                    <h4 class="alert-title mb-1">ফর্ম পূরণে কিছু ভুল পাওয়া গেছে:</h4>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row row-cards">
            <!-- Left Column: Business Details & Limits -->
            <div class="col-lg-8">
                <!-- Card 1: ভেন্ডরের মূল ব্যবসা তথ্য -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="7" width="18" height="13" rx="2" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><line x1="12" y1="12" x2="12" y2="12.01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
                            ভেন্ডর / কনভেনশন হল প্রোফাইল
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label required">ভেন্ডর / প্রতিষ্ঠান এর নাম</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name) }}" required placeholder="উদা: রেডিসন কনভেনশন সেন্টার">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label required">স্ল্যাগ / আইডেন্টিফায়ার (URL Slug)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted small">/hall/</span>
                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $tenant->slug) }}" required placeholder="radisson-hall">
                                </div>
                                <small class="form-hint">ইউনিক ইংরেজি ছোট হাতের অক্ষর ও হাইফেন ব্যবহার করুন।</small>
                                @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">অফিসিয়াল যোগাযোগ মোবাইল নং</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $tenant->phone) }}" placeholder="উদা: 01700000000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">প্রতিষ্ঠান / হলের ঠিকানা</label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $tenant->address) }}" placeholder="উদা: প্লট # ১২, রোড # ৪, গুলশান-২, ঢাকা">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">লোগো (Logo)</label>
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        @if($tenant->logo_path)
                                            <div class="position-relative d-inline-block">
                                                <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="Logo" class="rounded border p-1" style="width: 72px; height: 72px; object-fit: contain; background: #fff;">
                                            </div>
                                        @else
                                            <div class="avatar avatar-xl rounded bg-blue-lt text-uppercase fw-bold">
                                                {{ mb_substr($tenant->name, 0, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                        <small class="form-hint">নতুন লোগো আপলোড করতে ফাইল নির্বাচন করুন (সর্বোচ্চ 2MB, JPG/PNG/WEBP)</small>
                                        @if($tenant->logo_path)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                                <label class="form-check-label text-danger small fw-medium" for="removeLogoCheck">
                                                    বর্তমান লোগো মুছে ফেলুন
                                                </label>
                                            </div>
                                        @endif
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">ইনভয়েস শর্তাবলী ও নিয়মাবলী (Invoice Conditions)</label>
                                <textarea name="invoice_conditions" rows="3" class="form-control @error('invoice_conditions') is-invalid @enderror" placeholder="১. বুকিং বাতিল হলে অগ্রিম টাকা ফেরতযোগ্য নয়...&#10;২. অনুষ্ঠান শুরুর পূর্বে সম্পূর্ণ বিল পরিশোধ বাধ্যতামূলক...">{{ old('invoice_conditions', $tenant->invoice_conditions) }}</textarea>
                                <small class="form-hint">ইনভয়েস ও রশিদের নিচে এই শর্তগুলো প্রদর্শিত হবে।</small>
                                @error('invoice_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: সিস্টেম লিমিট ও ফিচার কনফিগারেশন -->
                @php
                    $settings = $tenant->settings ?? [];
                @endphp
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-indigo me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="10" x2="4" y2="20" /><line x1="10" y1="4" x2="10" y2="20" /><line x1="16" y1="4" x2="16" y2="20" /><line x1="20" y1="12" x2="20" y2="20" /><line x1="1" y1="14" x2="7" y2="14" /><line x1="7" y1="8" x2="13" y2="8" /><line x1="13" y1="16" x2="19" y2="16" /><line x1="17" y1="12" x2="23" y2="12" /></svg>
                            সিস্টেম লিমিট ও অটোমেশন কন্ট্রোল (Settings)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">সর্বোচ্চ হল সংখ্যা লিমিট (Max Hall Limit)</label>
                                <input type="number" name="hall_limit" min="1" max="100" class="form-control" value="{{ old('hall_limit', $settings['hall_limit'] ?? 5) }}">
                                <small class="form-hint">এই ভেন্ডর সর্বোচ্চ কয়টি হল তৈরি করতে পারবে।</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">সর্বোচ্চ স্টাফ/ম্যানেজার লিমিট (Staff Limit)</label>
                                <input type="number" name="staff_limit" min="1" max="500" class="form-control" value="{{ old('staff_limit', $settings['staff_limit'] ?? 10) }}">
                                <small class="form-hint">মোট কতজন ইউজার বা স্টাফ একাউন্ট তৈরি করা যাবে।</small>
                            </div>

                            <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="sms_enabled" value="1" id="smsEnabledSwitch" {{ old('sms_enabled', $settings['sms_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="smsEnabledSwitch">এসএমএস গেটওয়ে সক্রিয় (SMS Gateway)</label>
                                </div>
                                <small class="text-muted d-block">সক্রিয় থাকলে ভেন্ডর তার গ্রাহকদের এসএমএস পাঠাতে পারবে।</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="auto_sms_confirmation" value="1" id="autoSmsSwitch" {{ old('auto_sms_confirmation', $settings['auto_sms_confirmation'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="autoSmsSwitch">অটো বুকিং কনফার্মেশন এসএমএস</label>
                                </div>
                                <small class="text-muted d-block">বুকিং হলে স্বয়ংক্রিয়ভাবে গ্রাহককে এসএমএস যাবে।</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="auto_email_confirmation" value="1" id="autoEmailSwitch" {{ old('auto_email_confirmation', $settings['auto_email_confirmation'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="autoEmailSwitch">অটো ইমেইল কনফার্মেশন ও ইনভয়েস</label>
                                </div>
                                <small class="text-muted d-block">বুকিং হলে স্বয়ংক্রিয়ভাবে ইমেইলে রশিদ চলে যাবে।</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="auto_payment_reminder" value="1" id="autoReminderSwitch" {{ old('auto_payment_reminder', $settings['auto_payment_reminder'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="autoReminderSwitch">পেমেন্ট রিমাইন্ডার নোটিফিকেশন</label>
                                </div>
                                <small class="text-muted d-block">বকেয়া বিলের জন্য স্বয়ংক্রিয় তাগাদা নোটিফিকেশন।</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Subscription & Owner Account -->
            <div class="col-lg-4">
                <!-- Card 3: প্যাকেজ ও সাবস্ক্রিপশন স্ট্যাটাস -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7v10" /></svg>
                            প্যাকেজ ও সাবস্ক্রিপশন
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">বর্তমান প্যাকেজ (Plan)</label>
                            <select name="plan" class="form-select @error('plan') is-invalid @enderror" required>
                                @foreach($plans as $pKey => $pLabel)
                                    <option value="{{ $pKey }}" {{ old('plan', $tenant->plan) === $pKey ? 'selected' : '' }}>
                                        {{ $pLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">অ্যাকাউন্ট স্ট্যাটাস</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', $tenant->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>সক্রিয় (Active) - সম্পূর্ণ সচল</option>
                                <option value="0" {{ old('is_active', $tenant->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>নিষ্ক্রিয় / সাসপেন্ডেড (Inactive)</option>
                            </select>
                            <small class="form-hint">নিষ্ক্রিয় করলে ভেন্ডর লগইন ও সিস্টেম ব্যবহার করতে পারবে না।</small>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ফ্রি ট্রায়াল মেয়াদ শেষ (Trial Ends At)</label>
                            <input type="date" name="trial_ends_at" class="form-control @error('trial_ends_at') is-invalid @enderror" value="{{ old('trial_ends_at', $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d') : '') }}">
                            @error('trial_ends_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">সাবস্ক্রিপশন মেয়াদ শেষ (Subscription Ends)</label>
                            <input type="date" name="subscription_ends_at" class="form-control @error('subscription_ends_at') is-invalid @enderror" value="{{ old('subscription_ends_at', $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('Y-m-d') : '') }}">
                            <small class="form-hint">এই তারিখের পর অটোমেটিক রিনিউয়াল বা সাসপেন্ড নোটিশ দেখাবে।</small>
                            @error('subscription_ends_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 4: মূল মালিক / এডমিন একাউন্ট -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                            মালিক / প্রাইমারি এডমিন একাউন্ট
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">মালিকের নাম</label>
                            <input type="text" name="owner_name" class="form-control @error('owner_name') is-invalid @enderror" value="{{ old('owner_name', $owner?->name) }}" placeholder="উদা: মো: করিম হোসেন">
                            @error('owner_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">লগইন ইমেইল (Email)</label>
                            <input type="email" name="owner_email" class="form-control @error('owner_email') is-invalid @enderror" value="{{ old('owner_email', $owner?->email) }}" placeholder="owner@gmail.com">
                            @error('owner_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">মোবাইল নম্বর (Phone / Login)</label>
                            <input type="text" name="owner_phone" class="form-control @error('owner_phone') is-invalid @enderror" value="{{ old('owner_phone', $owner?->phone) }}" placeholder="017xxxxxxxx">
                            @error('owner_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">নতুন পাসওয়ার্ড রিসেট (ঐচ্ছিক)</label>
                            <input type="password" name="owner_password" class="form-control @error('owner_password') is-invalid @enderror" placeholder="অপরিবর্তিত রাখতে ফাঁকা রাখুন" autocomplete="new-password">
                            <small class="form-hint">পাসওয়ার্ড পরিবর্তন করতে চাইলে ন্যূনতম ৬ অক্ষরের নতুন পাসওয়ার্ড লিখুন।</small>
                            @error('owner_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit / Cancel Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="btn-list">
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                পরিবর্তন সংরক্ষণ করুন
                            </button>
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary w-100">
                                বাতিল করুন
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-tabler-layout>
