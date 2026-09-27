<x-tabler-layout :title="'সুপার এডমিন ড্যাশবোর্ড'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">ওভারভিউ</div>
                    <h2 class="page-title">সিস্টেম ড্যাশবোর্ড</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-deck row-cards">
                
                <!-- KPIs -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="subheader text-slate fw-bold">মোট ভেন্ডর</div>
                                <div class="ms-auto">
                                    <span class="bg-blue-lt avatar avatar-xs" style="background: rgba(32, 107, 196, 0.08); color: #206bc4;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="h1 mb-1 text-dark">{{ $totalTenants }}</div>
                            <div class="d-flex align-items-center">
                                <div class="badge bg-success-lt me-2">অ্যাক্টিভ: {{ $activeTenants }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="subheader text-slate fw-bold">অপেক্ষমান সাবস্ক্রিপশন</div>
                                <div class="ms-auto">
                                    <span class="bg-warning-lt avatar avatar-xs" style="background: rgba(245, 159, 0, 0.08); color: #f59f00;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7l0 10" /><path d="M9 10l3 3l3 -3" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="h1 mb-1 text-warning">{{ $pendingSubscriptions }}</div>
                            <div class="small text-muted">অনুমোদনের অপেক্ষায়</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, rgba(80, 200, 120, 0.08), rgba(255, 255, 255, 0.5)) !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="subheader text-emerald fw-bold">মোট রেভিনিউ</div>
                                <div class="ms-auto">
                                    <span class="bg-emerald text-white avatar avatar-xs" style="background: #50C878;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="h1 mb-1 text-dark">৳ {{ number_format($totalRevenue, 2) }}</div>
                            <div class="small text-muted">প্ল্যাটফর্ম সাবস্ক্রিপশন ফি</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="subheader text-slate fw-bold">অ্যাক্টিভ গেটওয়ে</div>
                                <div class="ms-auto">
                                    <span class="bg-azure-lt avatar avatar-xs" style="background: rgba(66, 153, 225, 0.08); color: #4299e1;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="3" /><line x1="3" y1="10" x2="21" y2="10" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="h1 mb-1 text-azure">{{ $activeGateways }}</div>
                            <div class="small text-muted">সচল পেমেন্ট মেথড</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Subscriptions Table -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title text-dark fw-bold mb-0">সাম্প্রতিক ট্রানজেকশন রিকোয়েস্ট</h3>
                            <div class="card-actions">
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-primary px-3 rounded-pill">সব দেখুন</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-mobile-md card-table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">ভেন্ডর</th>
                                        <th class="py-3">প্ল্যান</th>
                                        <th class="text-end py-3">অ্যামাউন্ট</th>
                                        <th class="py-3">পেমেন্ট মেথড</th>
                                        <th class="py-3">ট্রানজেকশন আইডি</th>
                                        <th class="py-3 text-center">স্ট্যাটাস</th>
                                        <th class="text-end py-3">তারিখ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSubscriptions as $sub)
                                    <tr class="border-bottom-0">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm rounded bg-slate-100 text-slate-800 me-2">{{ substr($sub->tenant->name ?? '?', 0, 1) }}</span>
                                                <div class="text-dark fw-medium">{{ $sub->tenant->name ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-blue-lt px-3">{{ ucfirst($sub->plan) }}</span></td>
                                        <td class="text-end text-dark">৳ {{ number_format($sub->amount, 2) }}</td>
                                        <td class="text-muted small">{{ $sub->payment_method }}</td>
                                        <td><code class="text-emerald small bg-emerald-lt px-2 py-1 rounded">{{ $sub->transaction_id }}</code></td>
                                        <td class="text-center">
                                            @php
                                                $subStatusColor = match($sub->status) {
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    default => 'danger'
                                                };
                                                $subStatusText = match($sub->status) {
                                                    'active' => 'অনুমোদিত',
                                                    'pending' => 'অপেক্ষমান',
                                                    default => 'বাতিল'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $subStatusColor }}-lt px-3 py-1 rounded-2">
                                                {{ $subStatusText }}
                                            </span>
                                        </td>
                                        <td class="text-muted text-end small">{{ $sub->created_at->format('d M, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">কোনো ট্রানজেকশন পাওয়া যায়নি।</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-tabler-layout>
