<x-tabler-layout :title="'হল বা রুম এডিট করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">হল বা রুম এডিট করুন: {{ $hall->name }}</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('halls.update', $hall) }}" method="POST" class="card">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">হলের নাম</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $hall->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">ঠিকানা</label>
                                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $hall->address) }}">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">মোবাইল নম্বর</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $hall->phone) }}">
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
                                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $hall->capacity) }}">
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required">প্রতি স্লট বুকিং রেট (৳)</label>
                                        <input type="number" step="0.01" name="price_per_slot" class="form-control @error('price_per_slot') is-invalid @enderror" value="{{ old('price_per_slot', $hall->price_per_slot) }}" required>
                                        @error('price_per_slot')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">হল বা রুমের বিস্তারিত বিবরণ</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $hall->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $hall->is_active) ? 'checked' : '' }}>
                                    <span class="form-check-label">সচল থাকবে কি না?</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('halls.index') }}" class="btn btn-link">বাতিল করুন</a>
                            <button type="submit" class="btn btn-primary">তথ্য আপডেট করুন</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
