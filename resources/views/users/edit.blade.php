<x-tabler-layout>
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">ইউজার এডিট করুন</h2>
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
        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">নাম</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">ইমেইল ঠিকানা</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ফোন নম্বর</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ঠিকানা</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', $user->address) }}">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">নতুন পাসওয়ার্ড (পরিবর্তন না করতে চাইলে ফাঁকা রাখুন)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">পাসওয়ার্ড নিশ্চিত করুন</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">প্রোফাইল ছবি (নতুন ছবি দিলে আগেরটি রিপ্লেস হবে)</label>
                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" name="profile_photo" accept="image/*">
                        @if($user->hasMedia('profile_photo'))
                            <div class="mt-2 text-center">
                                <img src="{{ $user->getFirstMediaUrl('profile_photo') }}" alt="Profile Photo" class="img-thumbnail shadow-sm" style="max-height: 120px; object-fit: cover;">
                            </div>
                        @else
                            <div class="mt-2 text-muted small">কোনো ছবি নেই</div>
                        @endif
                        @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NID ছবি</label>
                        <input type="file" class="form-control @error('nid_photo') is-invalid @enderror" name="nid_photo" accept="image/*">
                        @if($user->hasMedia('nid_photo'))
                            <div class="mt-2 text-center">
                                <img src="{{ $user->getFirstMediaUrl('nid_photo') }}" alt="NID Photo" class="img-thumbnail shadow-sm" style="max-height: 120px; object-fit: cover;">
                            </div>
                        @else
                            <div class="mt-2 text-muted small">কোনো ছবি নেই</div>
                        @endif
                        @error('nid_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                @php
                    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->tenant_id);
                    $currentRole = $user->roles->first()->name ?? '';
                    $isSelf = $user->id === auth()->id();
                @endphp

                <div class="mb-3">
                    <label class="form-label required">সিস্টেম ভূমিকা (Role)</label>
                    <select class="form-select @error('role') is-invalid @enderror" name="role" required {{ $isSelf ? 'disabled' : '' }}>
                        <option value="Tenant Admin" {{ old('role', $currentRole) == 'Tenant Admin' ? 'selected' : '' }}>অ্যাডমিন (সব ক্ষমতা)</option>
                        <option value="Manager" {{ old('role', $currentRole) == 'Manager' ? 'selected' : '' }}>ম্যানেজার (বুকিং ও হিসাব)</option>
                        <option value="Staff" {{ old('role', $currentRole) == 'Staff' ? 'selected' : '' }}>স্টাফ (শুধু বুকিং এন্ট্রি)</option>
                    </select>
                    @if($isSelf)<small class="form-hint text-danger">আপনি নিজের ভূমিকা পরিবর্তন করতে পারবেন না।</small>@endif
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">আপডেট করুন</button>
            </div>
        </form>
    </div>
</div>
</x-tabler-layout>
