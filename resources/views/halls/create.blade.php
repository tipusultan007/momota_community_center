<x-tabler-layout :title="'নতুন হল বা রুম যুক্ত করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">নতুন হল বা রুম যুক্ত করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('halls.store') }}" method="POST" class="card">
                        @csrf
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">হলের নাম</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="যেমন: মেইন হল বা গ্রাউন্ড ফ্লোর" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">ঠিকানা</label>
                                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="হলের সঠিক ঠিকানা লিখুন" value="{{ old('address') }}">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">মোবাইল নম্বর</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="01xxxxxxxxx" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">সর্বোচ্চ মেহমান ধারণক্ষমতা</label>
                                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="যেমন: ৫০০ জন" value="{{ old('capacity') }}">
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required">প্রতি স্লট বুকিং রেট (৳)</label>
                                        <input type="number" step="0.01" name="price_per_slot" class="form-control @error('price_per_slot') is-invalid @enderror" placeholder="যেমন: ৫০,০০০.০০" value="{{ old('price_per_slot') }}" required>
                                        @error('price_per_slot')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">হল বা রুমের বিস্তারিত বিবরণ (সুযোগ-সুবিধা ইত্যাদি)</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="যেমন: এসি আছে কি না, ডেকোরেশন সুবিধা, সাউন্ড সিস্টেম ইত্যাদি সম্পর্কে বিস্তারিত লিখুন">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('halls.index') }}" class="btn btn-link">বাতিল করুন</a>
                            <button type="submit" class="btn btn-primary">তথ্য সংরক্ষণ করুন</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
