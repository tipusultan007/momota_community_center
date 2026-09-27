<x-tabler-layout :title="'ভেন্ডর প্রোফাইল - ' . $vendor->name">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <span class="avatar avatar-lg rounded bg-blue-lt">{{ substr($vendor->name, 0, 2) }}</span>
                </div>
                <div class="col">
                    <h2 class="page-title">{{ $vendor->name }}</h2>
                    <div class="text-muted">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><span class="badge bg-blue-lt">{{ ucfirst($vendor->type) }}</span></li>
                            <li class="list-inline-item">৫/৫ রেটিং</li>
                            <li class="list-inline-item">কমিশন রেট: {{ $vendor->commission_rate }}%</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-auto ms-auto">
                    <div class="btn-list">
                        <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                            তথ্য এডিট করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="row row-cards">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card card-sm bg-indigo text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white-transparent text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট ইভেন্ট</div>
                            <div class="h2 mb-0">{{ $history->count() }} টি</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-sm bg-vk text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white-transparent text-white avatar">৳</span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট সার্ভিস চার্জ</div>
                            <div class="h2 mb-0">৳{{ number_format($totalServiceValue) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-sm bg-red text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white-transparent text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7l0 10" /><path d="M7 12l10 0" /><path d="M20 12l-16 0" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট কমিশন প্রদান</div>
                            <div class="h2 mb-0">৳{{ number_format($totalCommissionPaid + $totalCommissionPending) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-sm bg-green text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white-transparent text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">ভেন্ডর পাওনা (বকেয়া)</div>
                            <div class="h2 mb-0">৳{{ number_format($commissions->where('payout_status', 'pending')->sum('net_payout')) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed History Section -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ইভেন্ট ইতিহাস ও সেটেলমেন্ট বিস্তারিত</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>বুকিং দিন</th>
                                <th>কাস্টমার</th>
                                <th>সার্ভিস মূল্য (৳)</th>
                                <th>হল কমিশন (৳)</th>
                                <th>ভেন্ডর বিল (৳)</th>
                                <th>পেমেন্ট স্থিতি</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                @php
                                    $commission = $commissions->where('booking_id', $item['booking']->id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item['date']->format('d M, Y') }}</div>
                                        <div class="small text-muted">ID: #{{ $item['booking']->id }}</div>
                                    </td>
                                    <td>{{ $item['booking']->customer->name ?? 'N/A' }}</td>
                                    <td class="fw-bold">৳{{ number_format($item['total_price']) }}</td>
                                    <td>
                                        @if($commission)
                                            <div class="text-success">৳{{ number_format($commission->amount) }}</div>
                                            @if($commission->status == 'paid')
                                                <span class="badge bg-success-lt">সংগৃহীত</span>
                                            @else
                                                <span class="badge bg-warning-lt">বকেয়া</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="fw-bold text-azure">
                                        ৳{{ number_format($commission->net_payout ?? 0) }}
                                    </td>
                                    <td>
                                        @if($commission)
                                            @if($commission->payout_status == 'paid')
                                                <span class="badge bg-green text-green-fg">পরিশোধিত</span>
                                            @else
                                                <span class="badge bg-warning text-warning-fg">বকেয়া</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if($commission && $commission->payout_status == 'pending')
                                                <form action="{{ route('commissions.pay-vendor', $commission) }}" method="POST" id="pay-form-{{ $commission->id }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="confirmPay({{ $commission->id }}, {{ $commission->net_payout }}, '{{ $vendor->name }}')">
                                                        পেমেন্ট দিন
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('bookings.show', $item['booking']->id) }}" class="btn btn-sm btn-light">বুকিং</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">কোনো ইভেন্ট ইতিহাস পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        window.confirmPay = function(id, amount, vendor) {
            Swal.fire({
                title: 'ভেন্ডর পেমেন্ট?',
                text: "আপনি কি " + vendor + " কে ৳" + amount + " পরিশোধ করতে চান? এটি অটোমেটিক ব্যয় (Expense) হিসেবে রেকর্ড হবে।",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#206bc4',
                cancelButtonColor: '#868e96',
                confirmButtonText: 'হ্যাঁ, পরিশোধ করুন!',
                cancelButtonText: 'বাতিল'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('pay-form-' + id).submit();
                }
            })
        }
    </script>
    @endpush
</x-tabler-layout>
