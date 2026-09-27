<x-tabler-layout :title="$employee->name . ' - বিস্তারিত তথ্য'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ $employee->name }} - বিস্তারিত তথ্য</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-cards">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">সারসংক্ষেপ</div>
                            <div class="h3 mb-3">{{ $employee->designation }}</div>
                            <ul class="list-unstyled space-y-1">
                                <li><strong>ফোন:</strong> {{ $employee->phone }}</li>
                                <li><strong>বেতন:</strong> ৳ {{ number_format($employee->salary_amount, 2) }}</li>
                                <li><strong>যোগদান:</strong> {{ $employee->join_date ?? 'জানা নেই' }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header"><h3 class="card-title">বেতন প্রদান করুন</h3></div>
                        <form action="{{ route('employees.pay-salary', $employee) }}" method="POST" class="card-body">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">টাকার পরিমাণ</label>
                                <input type="number" name="amount" class="form-control" value="{{ $employee->salary_amount }}">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">মাস</label>
                                        <select name="month" class="form-select">
                                            @foreach(['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'] as $m)
                                                <option value="{{ $m }}" {{ $m == 'মার্চ' ? 'selected' : '' }}>{{ $m }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">বছর</label>
                                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">পেমেন্ট তারিখ</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <button type="submit" class="btn btn-success w-100">বেতন জমা করুন</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">বেতন প্রদানের ইতিহাস</h3></div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>মাস/বছর</th>
                                        <th>পরিমাণ</th>
                                        <th>মন্তব্য</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salaries as $salary)
                                        <tr>
                                            <td>{{ $salary->payment_date }}</td>
                                            <td>{{ $salary->month }}, {{ $salary->year }}</td>
                                            <td>৳ {{ number_format($salary->amount, 2) }}</td>
                                            <td class="text-secondary">{{ $salary->notes }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">কোন পেমেন্ট রেকর্ড পাওয়া যায়নি</td></tr>
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
