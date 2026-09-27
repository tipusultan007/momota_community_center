<x-tabler-layout :title="'আয় ক্যাটাগরি এডিট করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">আয় ক্যাটাগরি এডিট করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('income-categories.update', $incomeCategory) }}" method="POST" class="card">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">নাম</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $incomeCategory->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">বিবরণ</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $incomeCategory->description) }}</textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">আপডেট করুন</button>
                    <a href="{{ route('income-categories.index') }}" class="btn btn-link">ফিরে যান</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
