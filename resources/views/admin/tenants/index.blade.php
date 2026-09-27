<x-tabler-layout :title="'ভেন্ডর তালিকা'">
    <x-slot:header>
        <div class="page-header d-print-none mb-3">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">ম্যানেজমেন্ট</div>
                    <h2 class="page-title">সিস্টেম ভেন্ডর (Tenants) তালিকা</h2>
                    <div class="text-muted mt-1">প্ল্যাটফর্মে নিবন্ধিত সকল কনভেনশন হল ও তাদের তথ্য নিয়ন্ত্রণ করুন</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                            নতুন ভেন্ডর যুক্ত করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <!-- KPI Summary Cards -->
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 14h16" /><path d="M14 14v6" /><path d="M14 4v6" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট ভেন্ডর</div>
                            <div class="h2 mb-0">{{ $stats['total'] }} টি</div>
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
                            <span class="bg-success text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">সক্রিয় ভেন্ডর</div>
                            <div class="h2 mb-0 text-success">{{ $stats['active'] }} টি</div>
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
                            <span class="bg-danger text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">নিষ্ক্রিয় ভেন্ডর</div>
                            <div class="h2 mb-0 text-danger">{{ $stats['inactive'] }} টি</div>
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
                            <span class="bg-warning text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মেয়াদোত্তীর্ণ</div>
                            <div class="h2 mb-0 text-warning">{{ $stats['expired'] }} টি</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tenants.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="ভেন্ডরের নাম, স্ল্যাগ, মোবাইল বা মালিক খুঁজুন..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="plan" class="form-select">
                            <option value="all">সকল প্যাকেজ / প্ল্যান</option>
                            @foreach($plans as $pKey => $pLabel)
                                <option value="{{ $pKey }}" {{ request('plan') === $pKey ? 'selected' : '' }}>{{ $pLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="all">সকল স্ট্যাটাস</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            ফিল্টার
                        </button>
                        @if(request()->anyFilled(['search', 'plan', 'status']))
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary" title="রিসেট">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th class="w-1">#</th>
                        <th>ভেন্ডরের নাম ও তথ্য</th>
                        <th>মালিক ও যোগাযোগ</th>
                        <th>হল ও বুকিং</th>
                        <th>বর্তমান প্ল্যান</th>
                        <th>স্ট্যাটাস</th>
                        <th>সাবস্ক্রিপশন মেয়াদ</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $index => $tenant)
                    @php
                        $owner = $tenant->users->first();
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $tenants->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($tenant->logo_path)
                                    <span class="avatar avatar-md me-3 rounded border" style="background-image: url('{{ asset('storage/' . $tenant->logo_path) }}'); background-size: contain; background-color: #fff;"></span>
                                @else
                                    <span class="avatar avatar-md me-3 rounded bg-blue-lt fw-bold">
                                        {{ mb_substr($tenant->name, 0, 2) }}
                                    </span>
                                @endif
                                <div>
                                    <div class="font-weight-medium text-dark">
                                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-reset fw-bold">{{ $tenant->name }}</a>
                                    </div>
                                    <div class="text-muted small">
                                        স্ল্যাগ: <span class="badge bg-secondary-lt">{{ $tenant->slug }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($owner)
                                <div class="fw-medium text-dark">{{ $owner->name }}</div>
                                <div class="small text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2" /><polyline points="3 7 12 13 21 7" /></svg>
                                    {{ $owner->email }}
                                </div>
                                <div class="small text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                                    {{ $owner->phone ?? $tenant->phone ?? 'N/A' }}
                                </div>
                            @else
                                <span class="text-muted small">কোনো মালিক এসাইন নেই</span>
                            @endif
                        </td>
                        <td>
                            <div><span class="badge bg-blue-lt">{{ $tenant->halls_count }} টি হল</span></div>
                            <div class="small text-muted mt-1"><span class="badge bg-green-lt">{{ $tenant->bookings_count }} টি বুকিং</span></div>
                        </td>
                        <td>
                            @php
                                $planBadges = [
                                    'free' => 'bg-secondary-lt text-secondary',
                                    'basic' => 'bg-info-lt text-info',
                                    'pro' => 'bg-primary-lt text-primary',
                                    'enterprise' => 'bg-purple-lt text-purple',
                                ];
                                $badgeClass = $planBadges[$tenant->plan] ?? 'bg-secondary-lt';
                            @endphp
                            <span class="badge {{ $badgeClass }} px-2 py-1">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td>
                            @if($tenant->is_active)
                                <span class="badge bg-success-lt text-success d-inline-flex align-items-center">
                                    <span class="badge-dot bg-success me-1"></span> সক্রিয়
                                </span>
                            @else
                                <span class="badge bg-danger-lt text-danger d-inline-flex align-items-center">
                                    <span class="badge-dot bg-danger me-1"></span> নিষ্ক্রিয়
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($tenant->subscription_ends_at)
                                @if($tenant->subscription_ends_at->isPast())
                                    <div class="text-danger fw-bold small">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                                        মেয়াদোত্তীর্ণ ({{ $tenant->subscription_ends_at->format('d M, Y') }})
                                    </div>
                                @else
                                    <div class="text-success small fw-medium">
                                        {{ $tenant->subscription_ends_at->format('d M, Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        ({{ now()->diffInDays($tenant->subscription_ends_at) }} দিন বাকি)
                                    </div>
                                @endif
                            @elseif($tenant->trial_ends_at)
                                <div class="text-warning small fw-medium">
                                    ট্রায়াল: {{ $tenant->trial_ends_at->format('d M, Y') }}
                                </div>
                                <div class="text-muted small">
                                    ({{ $tenant->trial_ends_at->isPast() ? 'মেয়াদোত্তীর্ণ' : now()->diffInDays($tenant->trial_ends_at) . ' দিন বাকি' }})
                                </div>
                            @else
                                <span class="text-muted small">আনলিমিটেড / নেই</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-list flex-nowrap justify-content-end">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-info" title="বিস্তারিত দেখুন">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon m-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" /></svg>
                                </a>
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-primary" title="সম্পাদনা করুন">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -2.828 -2.828l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                    এডিট
                                </a>
                                <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ভেন্ডর এবং এর সমস্ত ডাটা মুছে ফেলতে চান?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="মুছে ফেলুন">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon m-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="10" x2="9.01" y2="10" /><line x1="15" y1="10" x2="15.01" y2="10" /><path d="M9.5 16a3.5 3.5 0 0 0 5 0" /></svg>
                                </div>
                                <p class="empty-title">কোনো ভেন্ডর পাওয়া যায়নি</p>
                                <p class="empty-subtitle text-muted">কোন ফিল্টার পরিবর্তন করে পুনরায় চেষ্টা করুন অথবা নতুন ভেন্ডর যুক্ত করুন।</p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                        নতুন ভেন্ডর যুক্ত করুন
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between">
            <div class="text-muted small">
                মোট {{ $tenants->total() }} টির মধ্যে {{ $tenants->firstItem() }} থেকে {{ $tenants->lastItem() }} টি দেখানো হচ্ছে
            </div>
            <div>
                {{ $tenants->links() }}
            </div>
        </div>
        @endif
    </div>
</x-tabler-layout>
