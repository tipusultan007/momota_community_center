<x-tabler-layout :title="'ধারের বিস্তারিত ও পরিশোধ বিবরণ - ' . $loan->person_name">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('loans.index') }}" class="text-secondary text-decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                            হাওলাত ও ঋণ তালিকা
                        </a>
                    </div>
                    <h2 class="page-title d-flex align-items-center gap-2">
                        <span>{{ $loan->person_name }}</span>
                        @if($loan->status === 'paid')
                            <span class="badge bg-success text-white">সম্পূর্ণ পরিশোধিত</span>
                        @elseif($loan->status === 'partial')
                            <span class="badge bg-warning text-white">আংশিক পরিশোধিত</span>
                        @else
                            <span class="badge bg-danger text-white">অপরিশোধিত</span>
                        @endif
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none d-flex gap-2">
                    @if($loan->due_amount > 0)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            টাকা ফেরত দিন
                        </button>
                    @endif
                    <a href="{{ route('loans.edit', $loan) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                        এডিট
                    </a>
                    <form action="{{ route('loans.destroy', $loan) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ধারের সম্পূর্ণ রেকর্ড মুছে ফেলতে চান?');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            মুছুন
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-xl">
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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 3 Financial Stat Cards -->
            @php
                $percentPaid = $loan->amount > 0 ? min(100, round(($loan->paid_amount / $loan->amount) * 100)) : 100;
            @endphp
            <div class="row row-cards mb-4">
                <div class="col-md-4">
                    <div class="card card-sm border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-secondary small font-weight-medium">মোট ধারের পরিমাণ</div>
                            <div class="h1 fw-bold text-dark mt-1">৳ {{ number_format($loan->amount, 2) }}</div>
                            <div class="text-muted small">টাকা গ্রহণের তারিখ: {{ \Carbon\Carbon::parse($loan->date)->format('d M, Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-sm border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-secondary small font-weight-medium">পরিশোধিত পরিমাণ</div>
                                <span class="badge bg-success-lt fw-bold">{{ $percentPaid }}% পরিশোধিত</span>
                            </div>
                            <div class="h1 fw-bold text-success mt-1">৳ {{ number_format($loan->paid_amount, 2) }}</div>
                            <div class="progress progress-sm mt-2">
                                <div class="progress-bar bg-success" style="width: {{ $percentPaid }}%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-sm border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-secondary small font-weight-medium">অবশিষ্ট প্রদেয় বকেয়া</div>
                            <div class="h1 fw-bold {{ $loan->due_amount > 0 ? 'text-danger' : 'text-muted' }} mt-1">
                                ৳ {{ number_format($loan->due_amount, 2) }}
                            </div>
                            <div class="small">
                                @if($loan->due_date)
                                    @php
                                        $isPast = $loan->due_amount > 0 && \Carbon\Carbon::parse($loan->due_date)->isPast();
                                    @endphp
                                    <span class="{{ $isPast ? 'text-danger fw-bold' : 'text-muted' }}">
                                        পরিশোধের লক্ষ্য: {{ \Carbon\Carbon::parse($loan->due_date)->format('d M, Y') }}
                                        @if($isPast) (মেয়াদ শেষ) @endif
                                    </span>
                                @else
                                    <span class="text-muted">পরিশোধের কোনো নির্দিষ্ট মেয়াদ নেই</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                <!-- Loan Information Overview -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title">ধারের বিবরণ</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted">ঋণদাতার নাম:</dt>
                                <dd class="col-7 fw-bold text-dark">{{ $loan->person_name }}</dd>

                                <dt class="col-5 text-muted">ফোন নম্বর:</dt>
                                <dd class="col-7">{{ $loan->phone ?: '—' }}</dd>

                                <dt class="col-5 text-muted">গ্রহণের তারিখ:</dt>
                                <dd class="col-7">{{ \Carbon\Carbon::parse($loan->date)->format('d M, Y') }}</dd>

                                <dt class="col-5 text-muted">পরিশোধের মেয়াদ:</dt>
                                <dd class="col-7">
                                    {{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->format('d M, Y') : 'নির্দিষ্ট নেই' }}
                                </dd>

                                <dt class="col-5 text-muted">গ্রহণের উদ্দেশ্য:</dt>
                                <dd class="col-7">{{ $loan->purpose ?: '—' }}</dd>

                                <dt class="col-5 text-muted">বর্তমান স্ট্যাটাস:</dt>
                                <dd class="col-7">
                                    @if($loan->status === 'paid')
                                        <span class="badge bg-success-lt fw-bold">পরিশোধিত</span>
                                    @elseif($loan->status === 'partial')
                                        <span class="badge bg-warning-lt fw-bold">আংশিক</span>
                                    @else
                                        <span class="badge bg-danger-lt fw-bold">অপরিশোধিত</span>
                                    @endif
                                </dd>
                            </dl>

                            @if($loan->notes)
                                <div class="mt-3 pt-3 border-top">
                                    <label class="form-label text-muted small">অতিরিক্ত নোট / বিবরণ:</label>
                                    <div class="p-2 bg-light rounded text-secondary">{{ $loan->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Repayment History Table -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                                টাকা ফেরত প্রদানের ইতিহাস (Repayments)
                            </h3>
                            @if($loan->due_amount > 0)
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    + নতুন কিস্তি পরিশোধ
                                </button>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>নং</th>
                                        <th>পরিশোধের তারিখ</th>
                                        <th class="text-end">পরিশোধের পরিমাণ</th>
                                        <th>পেমেন্ট মাধ্যম</th>
                                        <th>নোট / মন্তব্য</th>
                                        <th class="text-end">অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loan->payments as $index => $payment)
                                        <tr>
                                            <td class="text-muted">{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                                            <td class="text-end fw-bold text-success fs-3">
                                                ৳ {{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-lt">{{ $payment->payment_method }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $payment->notes ?: '—' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('loans.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('এই পেমেন্ট এন্ট্রিটি মুছে ফেলতে চান? এটি মুছে ফেললে বকেয়া টাকা পুনরায় বৃদ্ধি পাবে।');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="মুছুন">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                এখনো পর্যন্ত কোনো টাকা ফেরত প্রদান করা হয়নি।
                                            </td>
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

    <!-- Repayment Modal -->
    @if($loan->due_amount > 0)
        <div class="modal modal-blur fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('loans.payments.store', $loan) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">টাকা ফেরত প্রদান এন্ট্রি করুন</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">পরিশোধের পরিমাণ (টাকা)</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="1" max="{{ $loan->due_amount }}" name="amount" class="form-control" value="{{ $loan->due_amount }}" required>
                                </div>
                                <div class="form-hint">সর্বোচ্চ অবশিষ্ট বকেয়া: ৳ {{ number_format($loan->due_amount, 2) }}</div>
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
                                <textarea name="notes" class="form-control" rows="2" placeholder="যেমন: কিস্তি নম্বর বা রেফারেন্স"></textarea>
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
</x-tabler-layout>
