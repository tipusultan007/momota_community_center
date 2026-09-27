<x-tabler-layout>
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">আয় এডিট করুন</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form action="{{ route('incomes.update', $income) }}" method="POST" class="card shadow-sm">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">টাকার পরিমাণ</label>
                                <input type="number" name="amount" class="form-control" step="0.01" value="{{ old('amount', $income->amount) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">তারিখ</label>
                                <input type="date" name="date" class="form-control" value="{{ old('date', $income->date) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">ক্যাটাগরি</label>
                                <select name="income_category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('income_category_id', $income->income_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">বিবরণ</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $income->description) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">আপডেট করুন</button>
                            <a href="{{ route('incomes.index') }}" class="btn btn-link text-muted">ফিরে যান</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
