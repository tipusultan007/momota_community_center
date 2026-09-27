<x-tabler-layout :title="'বিলের ইতিহাস ও সাবস্ক্রিপশন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">সাবস্ক্রিপশন ও বিলিং ইতিহাস</h2>
                    <div class="text-muted mt-1">আপনার পূর্ববর্তী এবং বর্তমান সাবস্ক্রিপশন পেমেন্টের তালিকা</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-primary d-none d-sm-inline-block">
                            + নতুন সাবস্ক্রিপশন কিনুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>তারিখ</th>
                                <th>প্যাকেজ (প্ল্যান)</th>
                                <th>পেমেন্ট মেথড</th>
                                <th>পরিশোধ (৳)</th>
                                <th>ট্রানজেকশন আইডি</th>
                                <th>স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $history)
                                <tr>
                                    <td>
                                        <div>{{ $history->created_at->format('d M Y') }}</div>
                                        <div class="text-muted small">{{ $history->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <strong>{{ ucfirst($history->plan) }}</strong> 
                                    </td>
                                    <td>
                                        <div>{{ $history->payment_method ?? 'N/A' }}</div>
                                        <div class="text-secondary small">{{ $history->sender_number ?? '' }}</div>
                                    </td>
                                    <td>৳ {{ number_format($history->amount, 2) }}</td>
                                    <td class="text-muted">{{ $history->transaction_id ?? 'N/A' }}</td>
                                    <td>
                                        @if($history->status === 'pending')
                                            <span class="badge bg-warning text-white">অপেক্ষমান</span>
                                        @elseif($history->status === 'active')
                                            <span class="badge bg-success text-white">অনুমোদিত</span>
                                        @else
                                            <span class="badge bg-danger text-white">বাতিল</span>
                                            @if($history->reject_reason)
                                                <div class="small text-muted mt-1" style="max-width: 150px;">কারণ: {{ $history->reject_reason }}</div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">কোন পেমেন্ট রেকর্ড পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($histories->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $histories->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-tabler-layout>
