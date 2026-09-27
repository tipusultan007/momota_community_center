<x-tabler-layout :title="'সকল লেনদেন (Cashbook)'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">সকল লেনদেন (Cashbook)</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row g-3">
                <!-- Filters -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('transactions.index') }}" method="GET" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">শুরুর তারিখ</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">শেষ তারিখ</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">ধরণ</label>
                                    <select name="type" class="form-select">
                                        <option value="">সকল লেনদেন</option>
                                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>আয় (Income)</option>
                                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>ব্যয় (Expense)</option>
                                        <option value="salary" {{ request('type') == 'salary' ? 'selected' : '' }}>বেতন (Salary)</option>
                                        <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>কমিশন (Commission)</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">ফিল্টার করুন</button>
                                    <a href="{{ route('transactions.index') }}" class="btn btn-link">রিসেট</a>
                                </div>
                                <div class="col-auto ms-auto">
                                    <button type="submit" formaction="{{ route('transactions.export') }}" class="btn btn-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 11v6" /><path d="M9.5 13.5l2.5 -2.5l2.5 2.5" /></svg>
                                        PDF ডাউনলোড করুন
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>ধরণ</th>
                                        <th>বিবরণ</th>
                                        <th>আয় (In)</th>
                                        <th>ব্যয় (Out)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end fw-bold">প্রারম্ভিক জের (Opening Balance)</td>
                                        <td colspan="2" class="fw-bold fs-4 {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                            ৳ {{ number_format($openingBalance, 2) }}
                                        </td>
                                    </tr>
                                    
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M, Y') }}</td>
                                            <td>
                                                @if($transaction->type == 'income')
                                                    <span class="badge bg-success text-success-fg">আয়</span>
                                                @elseif($transaction->type == 'expense')
                                                    <span class="badge bg-danger text-danger-fg">ব্যয়</span>
                                                @elseif($transaction->type == 'salary')
                                                    <span class="badge bg-warning text-warning-fg">বেতন</span>
                                                @elseif($transaction->type == 'commission')
                                                    <span class="badge bg-info text-info-fg">কমিশন</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $transaction->description ?: 'বিবরণ নেই' }}
                                                @if($transaction->type == 'income' && $transaction->transactionable && isset($transaction->transactionable->booking))
                                                    <div class="text-secondary small">বুকিং আইডি: #{{ $transaction->transactionable->booking->id }}</div>
                                                @endif
                                            </td>
                                            <td class="text-success fw-bold">
                                                @if($transaction->type == 'income')
                                                    + ৳ {{ number_format($transaction->amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-danger fw-bold">
                                                @if(in_array($transaction->type, ['expense', 'salary', 'commission']))
                                                    - ৳ {{ number_format($transaction->amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">এই সময়ে কোনো লেনদেন পাওয়া যায়নি</td>
                                        </tr>
                                    @endforelse

                                    <tr class="table-secondary">
                                        <td colspan="3" class="text-end fw-bold">সমাপনী জের (Closing Balance)</td>
                                        <td colspan="2" class="fw-bold fs-3 {{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                            ৳ {{ number_format($closingBalance, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {{ $transactions->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
