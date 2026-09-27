<x-tabler-layout :title="'কমিশন ম্যানেজমেন্ট'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">কমিশন ও ভেন্ডর পাওনা</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body border-bottom py-3">
                    <form action="{{ route('commissions.index') }}" method="GET">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <select name="vendor_id" class="form-select">
                                    <option value="">সকল ভেন্ডর</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">সকল স্ট্যাটাস</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>বকেয়া (Pending)</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>সংগৃহীত (Paid)</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">ফিল্টার করুন</button>
                                <a href="{{ route('commissions.index') }}" class="btn btn-link">রিসেট</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>বুকিং আইডি</th>
                                <th>ভেন্ডর</th>
                                <th>বিবরণ/সার্ভিস</th>
                                <th>কমিশন পরিমাণ</th>
                                <th>ভেন্ডর বিল (Payout)</th>
                                <th>স্ট্যাটাস</th>
                                <th>তারিখ</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissions as $commission)
                                <tr>
                                    <td>
                                        <a href="{{ route('bookings.show', $commission->booking_id) }}" class="fw-bold">#{{ $commission->booking_id }}</a>
                                        <div class="small text-muted">{{ $commission->booking->customer->name ?? '' }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $commission->vendor->name }}</div>
                                        <div class="text-muted small">{{ $commission->vendor->type }} ({{ $commission->vendor->commission_rate }}%)</div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $commission->notes }}
                                    </td>
                                    <td class="fw-bold text-success">
                                        ৳{{ number_format($commission->amount, 2) }}
                                        @if($commission->status == 'pending')
                                            <div class="small fw-normal text-warning">বকেয়া</div>
                                        @else
                                            <div class="small fw-normal text-success">সংগৃহীত</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-azure">৳{{ number_format($commission->net_payout, 2) }}</div>
                                        @if($commission->payout_status == 'pending')
                                            <span class="badge bg-warning-lt">বকেয়া</span>
                                        @else
                                            <span class="badge bg-green-lt">পরিশোধিত</span>
                                        @endif
                                    </td>
                                    <td>{{ $commission->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if($commission->status == 'pending')
                                                <form action="{{ route('commissions.collect', $commission) }}" method="POST" id="collect-form-{{ $commission->id }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-success" onclick="confirmCollect({{ $commission->id }}, {{ $commission->amount }})">
                                                        কমিশন নিন
                                                    </button>
                                                </form>
                                            @endif

                                            @if($commission->payout_status == 'pending')
                                                <form action="{{ route('commissions.pay-vendor', $commission) }}" method="POST" id="pay-form-{{ $commission->id }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="confirmPay({{ $commission->id }}, {{ $commission->net_payout }}, '{{ $commission->vendor->name }}')">
                                                        ভেন্ডর পেমেন্ট
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">কোনো কমিশন ডাটা পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($commissions->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        {{ $commissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('js')
    <script>
        window.confirmCollect = function(id, amount) {
            Swal.fire({
                title: 'কমিশন সংগ্রহ?',
                text: "আপনি কি নিশ্চিত যে ভেন্ডর থেকে ৳" + amount + " কমিশন সংগ্রহ করেছেন? এটি অটোমেটিক আয় (Income) হিসেবে রেকর্ড হবে।",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2fb344',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'হ্যাঁ, সংগ্রহ করেছি!',
                cancelButtonText: 'বাতিল'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('collect-form-' + id).submit();
                }
            })
        }

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
