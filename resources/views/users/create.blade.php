<x-tabler-layout>
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">নতুন ইউজার যোগ করুন</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('users.index') }}" class="btn btn-white d-none d-sm-inline-block">
                    ফিরে যান
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="card">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">নাম</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="ইউজারের পূর্ণ নাম">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">ইমেইল ঠিকানা</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="email@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ফোন নম্বর</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="01712345678">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ঠিকানা</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" placeholder="ঠিকানা">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">পাসওয়ার্ড</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">পাসওয়ার্ড নিশ্চিত করুন</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">প্রোফাইল ছবি</label>
                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" name="profile_photo" accept="image/*">
                        @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NID ছবি</label>
                        <input type="file" class="form-control @error('nid_photo') is-invalid @enderror" name="nid_photo" accept="image/*">
                        @error('nid_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">সিস্টেম ভূমিকা (Role)</label>
                    <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                        <option value="">-- একটি ভূমিকা নির্বাচন করুন --</option>
                        <option value="Tenant Admin" {{ old('role') == 'Tenant Admin' ? 'selected' : '' }}>অ্যাডমিন (সব ক্ষমতা)</option>
                        <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>ম্যানেজার (বুকিং ও হিসাব)</option>
                        <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>স্টাফ (শুধু বুকিং এন্ট্রি)</option>
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="card-footer text-end mt-4">
                <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">বাতিল</a>
            </div>
        </form>
    </div>
</div>
</x-tabler-layout>
