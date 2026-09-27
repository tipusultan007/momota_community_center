<x-tabler-layout :title="'নতুন স্টাফের তথ্য দিন'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">নতুন স্টাফের তথ্য দিন</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('employees.store') }}" method="POST" class="card" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">পুরো নাম</label>
                                <input type="text" name="name" class="form-control" placeholder="যেমন: মো: আল-আমিন" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">মোবাইল নম্বর</label>
                                <input type="text" name="phone" class="form-control" placeholder="01xxxxxxxxx" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">ঠিকানা</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="বর্তমান ঠিকানা"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">পদবী (ডেজিগনেশন)</label>
                                <input type="text" name="designation" class="form-control" placeholder="যেমন: ম্যানেজার বা ওয়েটার">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">মাসিক বেতন (৳)</label>
                                <input type="number" name="salary_amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">যোগদানের তারিখ</label>
                                <input type="date" name="join_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">স্টাফের ছবি</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">প্রয়োজনীয় ডকুমেন্টস (NID, CV ইত্যাদি)</label>
                                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="form-hint">একাধিক ফাইল নির্বাচন করতে পারেন।</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">তথ্য সংরক্ষণ করুন</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-link">বাতিল</a>
                </div>
            </form>
        </div>
    </div>
</x-tabler-layout>
