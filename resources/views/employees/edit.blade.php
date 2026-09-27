<x-tabler-layout :title="'স্টাফের তথ্য এডিট করুন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">স্টাফের তথ্য এডিট করুন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('employees.update', $employee) }}" method="POST" class="card" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">পুরো নাম</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">মোবাইল নম্বর</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">ঠিকানা</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">পদবী (ডেজিগনেশন)</label>
                                <input type="text" name="designation" class="form-control" value="{{ old('designation', $employee->designation) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">মাসিক বেতন (৳)</label>
                                <input type="number" name="salary_amount" class="form-control" value="{{ old('salary_amount', $employee->salary_amount) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">যোগদানের তারিখ</label>
                                <input type="date" name="join_date" class="form-control" value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">স্টাফের ছবি (নতুন ছবি দিলে আগেরটি মুছে যাবে)</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                @if($employee->hasMedia('photo'))
                                    <div class="mt-2">
                                        <img src="{{ $employee->getFirstMediaUrl('photo') }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">প্রয়োজনীয় ডকুমেন্টস (নতুন ডকুমেন্টস দিলে আগেরগুলোর সাথে যুক্ত হবে)</label>
                                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @if($employee->hasMedia('documents'))
                                    <div class="mt-3">
                                        <strong>বিদ্যমান ডকুমেন্টস:</strong>
                                        <ul class="list-group mt-2">
                                            @foreach($employee->getMedia('documents') as $media)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <a href="{{ $media->getUrl() }}" target="_blank">{{ $media->file_name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">তথ্য আপডেট করুন</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-link">বাতিল</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
