<x-tabler-layout :title="'রিপোর্ট ও অ্যানালিটিক্স'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">রিপোর্ট ও অ্যানালিটিক্স</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('reports.pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                            PDF ডাউনলোড করুন
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                            প্রিন্ট করুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <!-- Filter Bar -->
    <div class="card mb-3 d-print-none">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">শুরু তারিখ</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">শেষ তারিখ</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-auto align-self-end">
                    <button type="submit" class="btn btn-primary">ফিল্টার করুন</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-link">রিসেট</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg></span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">মোট বুকিং</div>
                            <div class="text-secondary">{{ $bookingsCount }} টি (৳ {{ number_format($bookingsTotalValue, 2) }})</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7l0 10" /></svg></span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-success">মোট আয়</div>
                            <div class="h3 mb-0">৳ {{ number_format($totalIncome, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-danger text-white avatar"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7l0 10" /></svg></span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-danger">মোট ব্যয়</div>
                            <div class="h3 mb-0">৳ {{ number_format($totalExpense, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-azure text-white avatar"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg></span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">নীট লাভ/ক্ষতি</div>
                            <div class="h3 mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                ৳ {{ number_format($netProfit, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakdown Tables -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success-lt"><h3 class="card-title">আয় ক্যাটাগরি ভিত্তিক</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>ক্যাটাগরি</th><th class="text-end">পরিমাণ (৳)</th></tr></thead>
                        <tbody>
                            @foreach($incomeByCategory as $ic)
                                <tr>
                                    <td>{{ $ic->incomeCategory->name ?? 'অনির্ধারিত' }}</td>
                                    <td class="text-end">৳ {{ number_format($ic->total, 2) }}</td>
                                </tr>
                            @endforeach
                            @if($incomeByCategory->isEmpty())
                                <tr><td colspan="2" class="text-center text-muted">কোনো আয়ের তথ্য নেই</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger-lt"><h3 class="card-title">ব্যয় ক্যাটাগরি ভিত্তিক</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>ক্যাটাগরি</th><th class="text-end">পরিমাণ (৳)</th></tr></thead>
                        <tbody>
                            @foreach($expenseByCategory as $ec)
                                <tr>
                                    <td>{{ $ec->expenseCategory->name ?? 'অনির্ধারিত' }}</td>
                                    <td class="text-end">৳ {{ number_format($ec->total, 2) }}</td>
                                </tr>
                            @endforeach
                            @if($expenseByCategory->isEmpty())
                                <tr><td colspan="2" class="text-center text-muted">কোনো ব্যয়ের তথ্য নেই</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
