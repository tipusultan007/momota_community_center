<x-tabler-layout :title="'ভেন্ডর তথ্য এডিট করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">ভেন্ডর তথ্য এডিট করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <form action="{{ route('vendors.update', $vendor) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">ভেন্ডরের নাম</label>
                                <input type="text" name="name" class="form-control" value="{{ $vendor->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">ভেন্ডরের ধরণ</label>
                                <select name="type" class="form-select" required>
                                    <option value="catering" {{ $vendor->type == 'catering' ? 'selected' : '' }}>খাবার সরবরাহকারী (Catering)</option>
                                    <option value="decoration" {{ $vendor->type == 'decoration' ? 'selected' : '' }}>ডেকোরেশন (Decoration)</option>
                                    <option value="sound" {{ $vendor->type == 'sound' ? 'selected' : '' }}>সাউন্ড ও লাইটিং</option>
                                    <option value="photography" {{ $vendor->type == 'photography' ? 'selected' : '' }}>ফটোগ্রাফি ও ভিডিও</option>
                                    <option value="other" {{ $vendor->type == 'other' ? 'selected' : '' }}>অন্যান্য</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">মোবাইল নম্বর</label>
                                <input type="text" name="phone" class="form-control" value="{{ $vendor->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ইমেইল</label>
                                <input type="email" name="email" class="form-control" value="{{ $vendor->email }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">কমিশন হার (%)</label>
                                <input type="number" step="0.01" name="commission_rate" class="form-control" value="{{ $vendor->commission_rate }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-label">অবস্থা</div>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $vendor->is_active ? 'checked' : '' }}>
                                    <span class="form-check-label">সক্রিয়</span>
                                </label>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">ঠিকানা</label>
                                <textarea name="address" class="form-control" rows="2">{{ $vendor->address }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">আপডেট করুন</button>
                        <a href="{{ route('vendors.index') }}" class="btn btn-link">ফিরে যান</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-tabler-layout>
