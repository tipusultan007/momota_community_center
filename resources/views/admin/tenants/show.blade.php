<x-tabler-layout :title="'ভেন্ডর বিবরণ - ' . $tenant->name">
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
                    <div class="d-flex align-items-center gap-3 mt-1">
                        @if($tenant->logo_path)
                            <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="Logo" class="rounded border p-1" style="width: 48px; height: 48px; object-fit: contain; background: #fff;">
                        @else
                            <div class="avatar avatar-md rounded bg-primary text-white fw-bold">
                                {{ mb_substr($tenant->name, 0, 2) }}
                            </div>
                        @endif
                        <div>
                            <h2 class="page-title d-inline-block me-2">{{ $tenant->name }}</h2>
                            <div class="d-inline-flex gap-1 align-items-center">
                                @if($tenant->is_active)
                                    <span class="badge bg-success-lt text-success">সক্রিয় (Active)</span>
                                @else
                                    <span class="badge bg-danger-lt text-danger">নিষ্ক্রিয় (Inactive)</span>
                                @endif
                                <span class="badge bg-primary-lt text-primary text-uppercase">{{ $tenant->plan }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -2.828 -2.828l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                            ভেন্ডর তথ্য এডিট করুন
                        </a>
                        <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ভেন্ডর এবং এর সমস্ত ডাটা মুছে ফেলতে চান?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon m-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <!-- KPI Row -->
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-blue text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><rect x="8" y="15" width="2" height="2" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট বুকিং</div>
                            <div class="h2 mb-0">{{ $stats['total_bookings'] }} টি</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট বুকিং আয়</div>
                            <div class="h2 mb-0 text-success">৳ {{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-indigo text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21v-13l9 -4l9 4v13" /><path d="M13 21v-9h-2v9" /><path d="M8 21v-7h8v7" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট হল সংখ্যা</div>
                            <div class="h2 mb-0">{{ $stats['total_halls'] }} টি</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-purple text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট স্টাফ / ইউজার</div>
                            <div class="h2 mb-0">{{ $stats['total_users'] }} জন</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Left: Overview Profile -->
        <div class="col-lg-4">
            <!-- Vendor Info Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-dark">ভেন্ডরের সাধারণ তথ্য</h3>
                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">ভেন্ডর আইডি:</dt>
                        <dd class="col-7 fw-bold">#{{ $tenant->id }}</dd>

                        <dt class="col-5 text-muted">স্ল্যাগ (Slug):</dt>
                        <dd class="col-7"><span class="badge bg-secondary-lt">{{ $tenant->slug }}</span></dd>

                        <dt class="col-5 text-muted">মোবাইল:</dt>
                        <dd class="col-7">{{ $tenant->phone ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">ঠিকানা:</dt>
                        <dd class="col-7">{{ $tenant->address ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">নিবন্ধন তারিখ:</dt>
                        <dd class="col-7">{{ $tenant->created_at->format('d M, Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Owner Info Card -->
            @php
                $owner = $tenant->users->first();
            @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-dark">প্রাইমারি এডমিন / মালিক</h3>
                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                </div>
                <div class="card-body">
                    @if($owner)
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-md bg-blue-lt rounded-circle me-3">
                                {{ mb_substr($owner->name, 0, 2) }}
                            </span>
                            <div>
                                <div class="fw-bold fs-3 text-dark">{{ $owner->name }}</div>
                                <div class="text-muted small">মালিক ও প্রধান ব্যবস্থাপক</div>
                            </div>
                        </div>
                        <dl class="row mb-0">
                            <dt class="col-4 text-muted">ইমেইল:</dt>
                            <dd class="col-8">{{ $owner->email }}</dd>

                            <dt class="col-4 text-muted">ফোন:</dt>
                            <dd class="col-8">{{ $owner->phone ?? 'N/A' }}</dd>

                            <dt class="col-4 text-muted">যোগদান:</dt>
                            <dd class="col-8">{{ $owner->created_at->format('d M, Y') }}</dd>
                        </dl>
                    @else
                        <div class="text-muted small">কোনো মালিক এসাইন করা নেই।</div>
                    @endif
                </div>
            </div>

            <!-- Subscription & Limits Card -->
            @php
                $settings = $tenant->settings ?? [];
            @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom">
                    <h3 class="card-title text-dark">সাবস্ক্রিপশন ও লিমিট</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">প্ল্যান:</dt>
                        <dd class="col-7"><span class="badge bg-primary-lt text-uppercase fw-bold">{{ $tenant->plan }}</span></dd>

                        <dt class="col-5 text-muted">স্ট্যাটাস:</dt>
                        <dd class="col-7">
                            @if($tenant->is_active)
                                <span class="badge bg-success-lt text-success">সক্রিয়</span>
                            @else
                                <span class="badge bg-danger-lt text-danger">নিষ্ক্রিয়</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">ট্রায়াল মেয়াদ:</dt>
                        <dd class="col-7">{{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d M, Y') : 'N/A' }}</dd>

                        <dt class="col-5 text-muted">সাবস্ক্রিপশন শেষ:</dt>
                        <dd class="col-7">
                            @if($tenant->subscription_ends_at)
                                <span class="{{ $tenant->subscription_ends_at->isPast() ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $tenant->subscription_ends_at->format('d M, Y') }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">সর্বোচ্চ হল লিমিট:</dt>
                        <dd class="col-7">{{ $settings['hall_limit'] ?? 5 }} টি</dd>

                        <dt class="col-5 text-muted">স্টাফ লিমিট:</dt>
                        <dd class="col-7">{{ $settings['staff_limit'] ?? 10 }} জন</dd>

                        <dt class="col-5 text-muted">এসএমএস সক্রিয়:</dt>
                        <dd class="col-7">
                            @if(!empty($settings['sms_enabled']))
                                <span class="badge bg-success-lt">সক্রিয়</span>
                            @else
                                <span class="badge bg-secondary-lt">বন্ধ</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            @if($tenant->invoice_conditions)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h3 class="card-title text-dark">ইনভয়েস শর্তাবলী</h3>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0" style="white-space: pre-line;">{{ $tenant->invoice_conditions }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Halls, Recent Bookings, Subscription History -->
        <div class="col-lg-8">
            <!-- Halls List Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom">
                    <h3 class="card-title text-dark d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21v-13l9 -4l9 4v13" /><path d="M13 21v-9h-2v9" /><path d="M8 21v-7h8v7" /></svg>
                        কনভেনশন হল সমূহ ({{ $tenant->halls->count() }} টি)
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>হলের নাম</th>
                                <th>ধারণক্ষমতা</th>
                                <th>স্লট ভাড়া</th>
                                <th>সার্ভার রেট</th>
                                <th>স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->halls as $hall)
                            <tr>
                                <td class="fw-bold">{{ $hall->name }}</td>
                                <td>{{ number_format($hall->capacity) }} জন</td>
                                <td>৳ {{ number_format($hall->price_per_slot, 2) }}</td>
                                <td>৳ {{ number_format($hall->default_server_rate, 2) }}</td>
                                <td>
                                    @if($hall->is_active)
                                        <span class="badge bg-success-lt">সক্রিয়</span>
                                    @else
                                        <span class="badge bg-danger-lt">নিষ্ক্রিয়</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">কোনো হল পাওয়া যায়নি।</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Bookings Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom">
                    <h3 class="card-title text-dark d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><rect x="8" y="15" width="2" height="2" /></svg>
                        সাম্প্রতিক বুকিং সমূহ (সর্বশেষ ১০টি)
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>ইনভয়েস #</th>
                                <th>গ্রাহক</th>
                                <th>তারিখ ও স্লট</th>
                                <th>মোট বিল</th>
                                <th>পরিশোধিত</th>
                                <th>স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->bookings as $booking)
                            <tr>
                                <td class="fw-bold">{{ $booking->booking_number }}</td>
                                <td>
                                    <div>{{ $booking->customer_name }}</div>
                                    <div class="small text-muted">{{ $booking->customer_phone }}</div>
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M, Y') }}</div>
                                    <div class="small text-muted">{{ $booking->slot === 'day' ? 'দিন' : 'রাত' }}</div>
                                </td>
                                <td>৳ {{ number_format($booking->total_amount, 2) }}</td>
                                <td class="text-success">৳ {{ number_format($booking->paid_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-secondary-lt">{{ ucfirst($booking->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">কোনো বুকিং রেকর্ড নেই।</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Subscription Payment History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h3 class="card-title text-dark d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7v10" /></svg>
                        সাবস্ক্রিপশন পেমেন্ট হিস্ট্রি
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>প্যাকেজ</th>
                                <th>পেমেন্ট মেথড</th>
                                <th>টাকা</th>
                                <th>মেয়াদ</th>
                                <th>স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->subscriptionHistories as $sub)
                            <tr>
                                <td class="fw-bold">{{ ucfirst($sub->plan) }}</td>
                                <td>
                                    <div>{{ $sub->payment_method ?? 'N/A' }}</div>
                                    <div class="small text-muted">TrxID: {{ $sub->transaction_id ?? 'N/A' }}</div>
                                </td>
                                <td>৳ {{ number_format($sub->amount, 2) }}</td>
                                <td>
                                    @if($sub->ends_at)
                                        {{ $sub->ends_at->format('d M, Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($sub->status === 'approved')
                                        <span class="badge bg-success-lt">অনুমোদিত</span>
                                    @elseif($sub->status === 'pending')
                                        <span class="badge bg-warning-lt">অপেক্ষমান</span>
                                    @else
                                        <span class="badge bg-danger-lt">বাতিল</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">কোনো সাবস্ক্রিপশন লেনদেন নেই।</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
