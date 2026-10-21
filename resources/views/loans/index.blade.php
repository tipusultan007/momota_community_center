<x-tabler-layout :title="'ঋণ ও দেনা ব্যবস্থাপনা (Loans & Payments)'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">হিসাব নিকাশ ও তহবিল ব্যবস্থাপনা</div>
                    <h2 class="page-title">ঋণ ও দেনা ব্যবস্থাপনা (Loans & Payments)</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('loans.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        + নতুন ঋণ গ্রহণ এন্ট্রি
                    </a>
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

            <!-- KPI Summary Cards -->
            <div class="row row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-secondary small font-weight-medium">মোট ধার গ্রহণ</div>
                                    <div class="h2 mb-0 fw-bold text-blue">৳ {{ number_format($totalBorrowed, 2) }}</div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-secondary small font-weight-medium">মোট ফেরত প্রদান</div>
                                    <div class="h2 mb-0 fw-bold text-success">৳ {{ number_format($totalRepaid, 2) }}</div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16v.01" /><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-secondary small font-weight-medium">বর্তমান অবশিষ্ট দেনা (বকেয়া)</div>
                                    <div class="h2 mb-0 fw-bold text-danger">৳ {{ number_format($totalDue, 2) }}</div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4l3 3" /><path d="M12 3a9 9 0 1 0 9 9" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="text-secondary small font-weight-medium">চলমান ধার সংখ্যা</div>
                                    <div class="h2 mb-0 fw-bold text-warning">{{ $unsettledCount }} টি ঋণ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('loans.index') }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">অনুসন্ধান (নাম / ফোন / উদ্দেশ্য)</label>
                            <input type="text" name="search" class="form-control" placeholder="যেমন: রহিম, 017..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="">সকল স্ট্যাটাস</option>
                                <option value="unsettled" {{ request('status') == 'unsettled' ? 'selected' : '' }}>বকেয়া আছে (চলমান)</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>অপরিশোধিত</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>আংশিক পরিশোধিত</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>সম্পূর্ণ পরিশোধিত</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">শুরুর তারিখ</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">শেষ তারিখ</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                ফিল্টার
                            </button>
                            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">রিসেট</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>ঋণদাতা / ব্যক্তির নাম</th>
                                <th>গ্রহণের তারিখ</th>
                                <th>ফেরত প্রদানের মেয়াদ</th>
                                <th>উদ্দেশ্য</th>
                                <th class="text-end">ধারের পরিমাণ</th>
                                <th class="text-end">পরিশোধিত</th>
                                <th class="text-end">অবশিষ্ট বকেয়া</th>
                                <th class="text-center">স্ট্যাটাস</th>
                                <th class="text-end">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2 bg-azure-lt fw-bold">
                                                {{ mb_substr($loan->person_name, 0, 1) }}
                                            </span>
                                            <div>
                                                <a href="{{ route('loans.show', $loan) }}" class="text-reset fw-bold">{{ $loan->person_name }}</a>
                                                @if($loan->phone)
                                                    <div class="text-muted small">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                                                        {{ $loan->phone }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($loan->date)->format('d M, Y') }}</td>
                                    <td>
                                        @if($loan->due_date)
                                            @php
                                                $isOverdue = $loan->due_amount > 0 && \Carbon\Carbon::parse($loan->due_date)->isPast();
                                            @endphp
                                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ \Carbon\Carbon::parse($loan->due_date)->format('d M, Y') }}
                                            </span>
                                            @if($isOverdue)
                                                <span class="badge bg-danger-lt ms-1">মেয়াদ উত্তীর্ণ</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">নির্দিষ্ট নয়</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $loan->purpose ?: '—' }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        ৳ {{ number_format($loan->amount, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        ৳ {{ number_format($loan->paid_amount, 2) }}
                                    </td>
                                    <td class="text-end fw-bold {{ $loan->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                        ৳ {{ number_format($loan->due_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($loan->status === 'paid')
                                            <span class="badge bg-success-lt fw-bold">পরিশোধিত</span>
                                        @elseif($loan->status === 'partial')
                                            <span class="badge bg-warning-lt fw-bold">আংশিক</span>
                                        @else
                                            <span class="badge bg-danger-lt fw-bold">অপরিশোধিত</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @if($loan->due_amount > 0)
                                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#paymentModal-{{ $loan->id }}">
                                                    ফেরত দিন
                                                </button>
                                            @endif
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-outline-primary" title="বিস্তারিত">
                                                হিস্ট্রি
                                            </a>
                                            <a href="{{ route('loans.edit', $loan) }}" class="btn btn-outline-secondary" title="এডিট">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                            </a>
                                        </div>

                                        <!-- Quick Repayment Modal -->
                                        @if($loan->due_amount > 0)
                                            <div class="modal modal-blur fade text-start" id="paymentModal-{{ $loan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('loans.payments.store', $loan) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">টাকা ফেরত প্রদান (পরিশোধ)</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <div class="fw-bold fs-3 text-dark">{{ $loan->person_name }}</div>
                                                                    <div class="text-muted small">মোট ঋণ: ৳ {{ number_format($loan->amount, 2) }} | বর্তমান প্রদেয় বকেয়া: <span class="text-danger fw-bold">৳ {{ number_format($loan->due_amount, 2) }}</span></div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label required">পরিশোধের পরিমাণ (টাকা)</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">৳</span>
                                                                        <input type="number" step="0.01" min="1" max="{{ $loan->due_amount }}" name="amount" class="form-control" value="{{ $loan->due_amount }}" required>
                                                                    </div>
                                                                    <div class="form-hint">সর্বোচ্চ বকেয়া পরিমাণ: ৳ {{ number_format($loan->due_amount, 2) }}</div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label required">পরিশোধের তারিখ</label>
                                                                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label required">পরিশোধ মাধ্যম</label>
                                                                    <select name="payment_method" class="form-select" required>
                                                                        <option value="Cash" selected>ক্যাশ (Cash)</option>
                                                                        <option value="bKash">বিকাশ (bKash)</option>
                                                                        <option value="Nagad">নগদ (Nagad)</option>
                                                                        <option value="Bank Transfer">ব্যাংক ট্রান্সফার (Bank Transfer)</option>
                                                                        <option value="Cheque">চেক (Cheque)</option>
                                                                    </select>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">নোট / মন্তব্য (ঐচ্ছিক)</label>
                                                                    <textarea name="notes" class="form-control" rows="2" placeholder="যেমন: কিস্তি ১ বা চেক নম্বর"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">বাতিল</button>
                                                                <button type="submit" class="btn btn-success ms-auto">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                                    পরিশোধ নিশ্চিত করুন
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">কোনো ধারের রেকর্ড পাওয়া যায়নি।</div>
                                        <a href="{{ route('loans.create') }}" class="btn btn-primary mt-3">
                                            প্রথম ধার গ্রহণ যুক্ত করুন
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($loans->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        {{ $loans->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-tabler-layout>
