<x-tabler-layout :title="'আয় ব্যবস্থাপনা'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">আয় ব্যবস্থাপনা</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div>{{ session('success') }}</div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <div class="row g-3">
                <!-- Statistics & Filters -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('incomes.index') }}" method="GET" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">শুরুর তারিখ</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">শেষ তারিখ</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">ক্যাটাগরি</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">সব ক্যাটাগরি</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">ফিল্টার করুন</button>
                                    <a href="{{ route('incomes.index') }}" class="btn btn-link">রিসেট</a>
                                </div>
                                <div class="ms-auto col-auto text-end">
                                    <div class="text-secondary small">মোট আয়</div>
                                    <div class="h3 mb-0">৳ {{ number_format($totalIncome, 2) }}</div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Form & List -->
                <div class="col-md-4">
                    <form action="{{ route('incomes.store') }}" method="POST" class="card">
                        @csrf
                        <div class="card-header"><h3 class="card-title">নতুন আয় যোগ করুন</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">টাকার পরিমাণ</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">তারিখ</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">ক্যাটাগরি</label>
                                <select name="income_category_id" class="form-select" required>
                                    <option value="">সিলেক্ট করুন</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">বিবরণ</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="বিস্তারিত লিখুন..."></textarea>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-success w-100">আয় সংরক্ষণ করুন</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="table-responsive" style="overflow: visible;">
                            <table class="table table-vcenter table-hover card-table">
                                <thead>
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>ক্যাটাগরি</th>
                                        <th>বিবরণ</th>
                                        <th class="text-end">পরিমাণ</th>
                                        <th class="w-1 text-center">একশন</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($incomes as $income)
                                        <tr>
                                            <td>{{ $income->date }}</td>
                                            <td>
                                                <span class="badge bg-blue-lt">{{ $income->incomeCategory->name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="small">
                                                {{ $income->description }}
                                                @if($income->booking)
                                                    <div class="text-muted text-truncate" style="max-width: 150px;">
                                                        বুকিং: {{ $income->booking->customer->name }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-end text-success font-weight-bold">৳ {{ number_format($income->amount, 2) }}</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle align-text-top" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-auto-close="true" aria-expanded="false">
                                                        একশন
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end shadow">
                                                        <a class="dropdown-item" href="{{ route('incomes.edit', $income) }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon text-warning me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                            এডিট
                                                        </a>
                                                        <form action="{{ route('incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই আয়টি মুছে ফেলতে চান?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon text-danger me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                                ডিলিট
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">কোনো রেকর্ড পাওয়া যায়নি।</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {{ $incomes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
