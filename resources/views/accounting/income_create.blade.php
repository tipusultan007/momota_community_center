<x-tabler-layout>
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        নতুন আয় যোগ করুন
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('accounting.income.store') }}" method="POST" class="card">
                @csrf
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">টাকার পরিমাণ</label>
                                <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">তারিখ</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required d-flex justify-content-between">
                                    <span>খাত/ক্যাটাগরি</span>
                                    <a href="{{ route('income-categories.create') }}" class="small">+ নতুন ক্যাটাগরি</a>
                                </label>
                                <select name="income_category_id" class="form-select" required>
                                    <option value="">ক্যাটাগরি সিলেক্ট করুন</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">বিবরণ</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="বিস্তারিত লিখুন..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">আয় জমা করুন</button>
                    <a href="{{ route('accounting.index') }}" class="btn btn-link">ফিরে যান</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
