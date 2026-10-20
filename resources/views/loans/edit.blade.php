<x-tabler-layout :title="'ধারের তথ্য সম্পাদনা'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">হাওলাত / ঋণ ও ধার-দেনা</div>
                    <h2 class="page-title">ধারের তথ্য সম্পাদনা করুন</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('loans.show', $loan) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                        ফিরে যান
                    </a>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">ধারের তথ্য সংশোধন</h3>
                        </div>
                        <form action="{{ route('loans.update', $loan) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">ঋণদাতা / ব্যক্তির নাম</label>
                                        <input type="text" name="person_name" class="form-control @error('person_name') is-invalid @enderror" value="{{ old('person_name', $loan->person_name) }}" required>
                                        @error('person_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">ফোন নম্বর</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $loan->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label required">ধারের মোট পরিমাণ (টাকা)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" min="{{ $loan->paid_amount }}" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $loan->amount) }}" required>
                                        </div>
                                        @if($loan->paid_amount > 0)
                                            <small class="form-hint text-warning">ইতিমধ্যে ৳ {{ number_format($loan->paid_amount, 2) }} পরিশোধ করা হয়েছে, তাই এর চেয়ে কম দেওয়া যাবে না।</small>
                                        @endif
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label required">টাকা গ্রহণের তারিখ</label>
                                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $loan->date?->format('Y-m-d')) }}" required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">ফেরত দেওয়ার সম্ভাব্য তারিখ</label>
                                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $loan->due_date?->format('Y-m-d')) }}">
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">টাকা গ্রহণের উদ্দেশ্য / প্রয়োজন</label>
                                        <input type="text" name="purpose" class="form-control @error('purpose') is-invalid @enderror" value="{{ old('purpose', $loan->purpose) }}">
                                        @error('purpose')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">অতিরিক্ত নোট / বিবরণ</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $loan->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent d-flex justify-content-between">
                                <a href="{{ route('loans.show', $loan) }}" class="btn btn-link link-secondary">বাতিল</a>
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    আপডেট করুন
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
