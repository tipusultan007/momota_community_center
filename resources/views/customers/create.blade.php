<x-tabler-layout :title="'নতুন গ্রাহকের তথ্য সরাসরি যোগ করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">নতুন গ্রাহকের তথ্য সরাসরি যোগ করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('customers.store') }}" method="POST" class="card">
                @csrf
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">নাম</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="গ্রাহকের পুরো নাম">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">ফোন নম্বর</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="যেমন: 017xxxxxxxx">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">এনআইডি নম্বর (NID)</label>
                                <input type="text" name="nid" class="form-control @error('nid') is-invalid @enderror" value="{{ old('nid') }}" placeholder="জাতীয় পরিচয়পত্র নম্বর">
                                @error('nid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">ঠিকানা</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-link">ফিরে যান</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
