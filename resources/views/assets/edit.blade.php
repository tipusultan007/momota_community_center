<x-tabler-layout :title="'মালামাল এডিট করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">মালামাল এডিট করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <form action="{{ route('assets.update', $asset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">নাম</label>
                                <input type="text" name="name" class="form-control" value="{{ $asset->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">মোট স্টক</label>
                                <input type="number" name="total_stock" class="form-control" value="{{ $asset->total_stock }}" min="0" required>
                                <small class="text-muted">বর্তমানে আছে: {{ $asset->available_stock }}</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">বিস্তারিত বিবরণ</label>
                                <textarea name="description" class="form-control" rows="4">{{ $asset->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">আপডেট করুন</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-link">ফিরে যান</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-tabler-layout>
