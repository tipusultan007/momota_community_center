<x-tabler-layout :title="'নতুন মালামাল যুক্ত করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">নতুন মালামাল যুক্ত করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <form action="{{ route('assets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">নাম</label>
                                <input type="text" name="name" class="form-control" placeholder="যেমন: চেয়ার, টেবিল" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">মোট স্টক</label>
                                <input type="number" name="total_stock" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">বিস্তারিত বিবরণ</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-link">ফিরে যান</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-tabler-layout>
