<x-tabler-layout :title="'সেটিংস (হলের প্রোফাইল)'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">সেটিংস (হলের প্রোফাইল)</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        </div>
                        <div>{{ session('success') }}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row row-cards">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">সাধারণ তথ্য</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required">নাম (নাম পরিবর্তন করলে ইনভয়েসেও পরিবর্তন হবে)</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $hall->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ঠিকানা</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $hall->address) }}" placeholder="হলের সঠিক ঠিকানা লিখুন">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">মোবাইল নম্বর</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $hall->phone) }}" placeholder="01xxxxxxxxx">
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label required">হলের ধারণক্ষমতা (মেহমান)</label>
                                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $hall->capacity ?? 500) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label required">স্লট প্রতি ভাড়া (৳)</label>
                                            <input type="number" name="price_per_slot" class="form-control" value="{{ old('price_per_slot', $hall->price_per_slot ?? 30000) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label required">জনপ্রতি পরিবেশনকারী রেট (৳)</label>
                                            <input type="number" name="default_server_rate" class="form-control" value="{{ old('default_server_rate', $hall->default_server_rate ?? 500) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ইনভয়েস শর্তাবলী (Terms & Conditions)</label>
                                    <textarea name="invoice_conditions" class="form-control" rows="8" placeholder="প্রতিটি ইনভয়েসের নিচে এই শর্তাবলী দেখা যাবে">{{ old('invoice_conditions', $tenant->invoice_conditions) }}</textarea>
                                    <small class="form-hint">প্রতিটি নতুন লাইন একটি বুলেট পয়েন্ট হিসেবে গণ্য হতে পারে।</small>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">সেটিংস সংরক্ষণ করুন</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">অটোমেশন ও নোটিফিকেশন</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_email_confirmation" {{ ($tenant->settings['auto_email_confirmation'] ?? true) ? 'checked' : '' }}>
                                        <span class="form-check-label">বুকিং কনফার্মেশন ইমেইল</span>
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_sms_confirmation" {{ ($tenant->settings['auto_sms_confirmation'] ?? false) ? 'checked' : '' }}>
                                        <span class="form-check-label">বুকিং কনফার্মেশন SMS</span>
                                    </label>
                                    @if($tenant->settings['auto_sms_confirmation'] ?? false)
                                    <div class="mt-2">
                                        <label class="small text-muted">SMS টেমপ্লেট:</label>
                                        <textarea name="sms_template_confirmation" class="form-control form-control-sm" rows="3">{{ $tenant->settings['sms_template_confirmation'] ?? "প্রিয় [CustomerName], [EventDate] তারিখের জন্য আপনার বুকিং নিশ্চিৎ করা হয়েছে। মোট বিল: [TotalAmount]। ধন্যবাদ!" }}</textarea>
                                    </div>
                                    @endif
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_payment_reminder" {{ ($tenant->settings['auto_payment_reminder'] ?? false) ? 'checked' : '' }}>
                                        <span class="form-check-label">বকেয়া বিল রিমাইন্ডার (SMS)</span>
                                    </label>
                                    @if($tenant->settings['auto_payment_reminder'] ?? false)
                                    <div class="mt-2">
                                        <textarea name="sms_template_reminder" class="form-control form-control-sm" rows="3">{{ $tenant->settings['sms_template_reminder'] ?? "প্রিয় [CustomerName], আপনার ইভেন্টের জন্য [DueAmount] টাকা বকেয়া আছে। অনুগ্রহ করে পরিশোধ করুন। ধন্যবাদ!" }}</textarea>
                                    </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_event_greeting" {{ ($tenant->settings['auto_event_greeting'] ?? false) ? 'checked' : '' }}>
                                        <span class="form-check-label">ইভেন্টের আগের দিন শুভেচ্ছা (SMS)</span>
                                    </label>
                                    @if($tenant->settings['auto_event_greeting'] ?? false)
                                    <div class="mt-2">
                                        <textarea name="sms_template_greeting" class="form-control form-control-sm" rows="3">{{ $tenant->settings['sms_template_greeting'] ?? "প্রিয় [CustomerName], আগামীকাল আপনার ইভেন্টের জন্য আমরা প্রস্তুত। দেখা হবে ইনশাআল্লাহ! - [HallName]" }}</textarea>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="alert alert-info py-2 small">
                                    প্লেসহোল্ডার: [CustomerName], [EventDate], [TotalAmount], [DueAmount], [HallName]
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">হলের লোগো</h3>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    @if($tenant->logo_path)
                                        <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="Logo" class="img-thumbnail" style="max-height: 150px;">
                                    @else
                                        <div class="bg-light border rounded-3 p-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
                                            <p class="mt-2 mb-0">লোগো নেই</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <small class="form-hint mt-2">প্রস্তাবিত সাইজ: ২০০x২০০ পিক্সেল</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
