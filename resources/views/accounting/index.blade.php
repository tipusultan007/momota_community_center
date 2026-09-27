<x-tabler-layout :title="'হিসাব নিকাশ (অ্যাকাউন্টিং)'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">হিসাব নিকাশ (অ্যাকাউন্টিং)</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-cards mb-4">
                <div class="col-md-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">মোট আয়</div>
                                    <div class="text-secondary text-h1">৳ {{ number_format($totalIncome, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-danger text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">মোট খরচ</div>
                                    <div class="text-secondary text-h1">৳ {{ number_format($totalExpense, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 6l2 2l2 -2" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">বর্তমান ব্যালেন্স</div>
                                    <div class="text-secondary text-h1">৳ {{ number_format($totalIncome - $totalExpense, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">সাম্প্রতিক আয়</h3>
                            <div class="card-actions">
                                <a href="{{ route('incomes.index') }}" class="btn btn-success btn-sm">আয় ব্যবস্থাপনা</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>বিবরণ</th>
                                        <th>টাকা</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($incomes->take(10) as $income)
                                        <tr>
                                            <td>{{ $income->date }}</td>
                                            <td>{{ $income->description ?? ($income->incomeCategory->name ?? 'N/A') }}</td>
                                            <td class="text-success font-weight-bold">৳ {{ number_format($income->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center">কোন আয়ের তথ্য নেই</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">সাম্প্রতিক খরচ</h3>
                            <div class="card-actions">
                                <a href="{{ route('expenses.index') }}" class="btn btn-danger btn-sm">ব্যয় ব্যবস্থাপনা</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>বিবরণ</th>
                                        <th>টাকা</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses->take(10) as $expense)
                                        <tr>
                                            <td>{{ $expense->date }}</td>
                                            <td>{{ $expense->description ?? ($expense->expenseCategory->name ?? 'N/A') }}</td>
                                            <td class="text-danger font-weight-bold">৳ {{ number_format($expense->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center">কোন খরচের তথ্য নেই</td></tr>
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
