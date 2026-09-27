<x-tabler-layout :title="'গ্রাহকের তথ্য সম্পাদনা - ' . $customer->name">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            <a href="{{ route('customers.index') }}" class="text-secondary text-decoration-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                                গ্রাহক তালিকায় ফিরে যান
                            </a>
                        </div>
                        <h2 class="page-title mt-1">গ্রাহকের তথ্য সম্পাদনা: {{ $customer->name }}</h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            তালিকায় ফিরে যান
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="fw-bold mb-1">ফর্মটি সঠিকভাবে পূরণ করুন:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <form action="{{ route('customers.update', $customer) }}" method="POST" class="card shadow-sm border-0">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h3 class="card-title">গ্রাহকের মৌলিক তথ্য</h3>
                </div>
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">নাম</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" placeholder="গ্রাহকের পুরো নাম" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">ফোন নম্বর</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}" placeholder="যেমন: 017xxxxxxxx" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">এনআইডি নম্বর (NID)</label>
                                <input type="text" name="nid" class="form-control @error('nid') is-invalid @enderror" value="{{ old('nid', $customer->nid) }}" placeholder="জাতীয় পরিচয়পত্র নম্বর">
                                @error('nid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">ঠিকানা</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="গ্রাহকের বর্তমান বা স্থায়ী ঠিকানা">{{ old('address', $customer->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        বাতিল করুন
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                        আপডেট সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
