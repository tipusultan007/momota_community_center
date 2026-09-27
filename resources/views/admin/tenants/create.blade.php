<x-tabler-layout :title="'নতুন ভেন্ডর যুক্ত করুন'">
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
                    <h2 class="page-title">নতুন ভেন্ডর (কনভেনশন হল) তৈরি করুন</h2>
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
                    <h4 class="alert-title mb-1">ফর্ম পূরণে কিছু ত্রুটি রয়েছে:</h4>
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

    <form action="{{ route('admin.tenants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row row-cards">
            <!-- Left Column: Tenant & Limits -->
            <div class="col-lg-8">
                <!-- Card 1: ভেন্ডরের ব্যবসা তথ্য -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="7" width="18" height="13" rx="2" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><line x1="12" y1="12" x2="12" y2="12.01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
                            ভেন্ডর / প্রতিষ্ঠান এর তথ্য
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label required">ভেন্ডর / কনভেনশন হলের নাম</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="উদা: রয়েল গ্র্যান্ড কনভেনশন হল" id="tenantName">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label required">ইউনিক স্ল্যাগ (URL Slug)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted small">/hall/</span>
                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="royal-grand" id="tenantSlug">
                                </div>
                                <small class="form-hint">ইউনিক ইংরেজি ছোট অক্ষরের স্ল্যাগ।</small>
                                @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">অফিসিয়াল যোগাযোগ ফোন নং</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="01700000000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">প্রতিষ্ঠান / হলের ঠিকানা</label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="উদা: ধানমন্ডি ২৭, ঢাকা">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">লোগো (ঐচ্ছিক)</label>
                                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                <small class="form-hint">PNG/JPG/WEBP ফরম্যাট (সর্বোচ্চ 2MB)</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ইনভয়েস শর্তাবলী ও নিয়মাবলী</label>
                                <textarea name="invoice_conditions" rows="3" class="form-control @error('invoice_conditions') is-invalid @enderror" placeholder="১. বুকিং বাতিল হলে অগ্রিম টাকা ফেরতযোগ্য নয়...&#10;২. অনুষ্ঠান শুরুর পূর্বে সম্পূর্ণ বিল পরিশোধ বাধ্যতামূলক...">{{ old('invoice_conditions') }}</textarea>
                                @error('invoice_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: লিমিট ও সেটিংস -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-indigo me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="10" x2="4" y2="20" /><line x1="10" y1="4" x2="10" y2="20" /><line x1="16" y1="4" x2="16" y2="20" /><line x1="20" y1="12" x2="20" y2="20" /><line x1="1" y1="14" x2="7" y2="14" /><line x1="7" y1="8" x2="13" y2="8" /><line x1="13" y1="16" x2="19" y2="16" /><line x1="17" y1="12" x2="23" y2="12" /></svg>
                            সিস্টেম লিমিট ও ফিচার কন্ট্রোল
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">সর্বোচ্চ হল লিমিট</label>
                                <input type="number" name="hall_limit" min="1" max="100" class="form-control" value="{{ old('hall_limit', 5) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">সর্বোচ্চ স্টাফ লিমিট</label>
                                <input type="number" name="staff_limit" min="1" max="500" class="form-control" value="{{ old('staff_limit', 10) }}">
                            </div>

                            <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="sms_enabled" value="1" id="smsEnabledSwitch" {{ old('sms_enabled') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="smsEnabledSwitch">এসএমএস গেটওয়ে সক্রিয়</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="auto_sms_confirmation" value="1" id="autoSmsSwitch" {{ old('auto_sms_confirmation', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="autoSmsSwitch">অটো বুকিং এসএমএস</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="auto_email_confirmation" value="1" id="autoEmailSwitch" {{ old('auto_email_confirmation', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="autoEmailSwitch">অটো ইমেইল রশিদ</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Plan & Owner Account -->
            <div class="col-lg-4">
                <!-- Card 3: প্যাকেজ নির্বাচন -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7v10" /></svg>
                            প্যাকেজ ও অ্যাক্টিভেশন
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">প্যাকেজ (Plan)</label>
                            <select name="plan" class="form-select @error('plan') is-invalid @enderror" required>
                                @foreach($plans as $pKey => $pLabel)
                                    <option value="{{ $pKey }}" {{ old('plan', 'basic') === $pKey ? 'selected' : '' }}>
                                        {{ $pLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">স্ট্যাটাস</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>সক্রিয় (Active)</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ফ্রি ট্রায়াল শেষ তারিখ</label>
                            <input type="date" name="trial_ends_at" class="form-control @error('trial_ends_at') is-invalid @enderror" value="{{ old('trial_ends_at', now()->addDays(14)->format('Y-m-d')) }}">
                            @error('trial_ends_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">সাবস্ক্রিপশন মেয়াদ শেষ</label>
                            <input type="date" name="subscription_ends_at" class="form-control @error('subscription_ends_at') is-invalid @enderror" value="{{ old('subscription_ends_at', now()->addYear()->format('Y-m-d')) }}">
                            @error('subscription_ends_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 4: মালিকের একাউন্ট তথ্য -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h3 class="card-title text-dark d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                            মালিক / এডমিন লগইন তথ্য
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">মালিকের নাম</label>
                            <input type="text" name="owner_name" class="form-control @error('owner_name') is-invalid @enderror" value="{{ old('owner_name') }}" required placeholder="উদা: মো: হাসান আলী">
                            @error('owner_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">লগইন ইমেইল</label>
                            <input type="email" name="owner_email" class="form-control @error('owner_email') is-invalid @enderror" value="{{ old('owner_email') }}" required placeholder="owner@gmail.com">
                            @error('owner_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">লগইন মোবাইল নম্বর</label>
                            <input type="text" name="owner_phone" class="form-control @error('owner_phone') is-invalid @enderror" value="{{ old('owner_phone') }}" required placeholder="017xxxxxxxx">
                            @error('owner_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">পাসওয়ার্ড</label>
                            <input type="password" name="owner_password" class="form-control @error('owner_password') is-invalid @enderror" required placeholder="কমপক্ষে ৬ অক্ষর">
                            @error('owner_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="btn-list">
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                ভেন্ডর তৈরি করুন
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
