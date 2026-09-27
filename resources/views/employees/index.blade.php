<x-tabler-layout :title="'স্টাফ ও কর্মচারী তালিকা'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">স্টাফ ও কর্মচারী তালিকা</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('employees.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            নতুন স্টাফ যুক্ত করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>নাম</th>
                                <th>পদবী</th>
                                <th>মোবাইল</th>
                                <th>মাসিক বেতন (ফিক্সড)</th>
                                <th>অবস্থা</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex py-1 align-items-center">
                                            <span class="avatar me-2" style="background-image: url({{ $employee->hasMedia('photo') ? $employee->getFirstMediaUrl('photo') : asset('assets/static/avatars/default.png') }})">
                                                @if(!$employee->hasMedia('photo'))
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                                @endif
                                            </span>
                                            <div class="flex-fill">
                                                <div class="font-weight-medium">{{ $employee->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-secondary">{{ $employee->designation ?? 'নাই' }}</td>
                                    <td>
                                        {{ $employee->phone }}
                                    </td>
                                    <td>৳ {{ number_format($employee->salary_amount, 2) }}</td>
                                    <td>
                                        @if($employee->is_active)
                                            <span class="badge bg-success">কর্মরত</span>
                                        @else
                                            <span class="badge bg-secondary">অব্যাহতি</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-white">বিস্তারিত ও বেতন</a>
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-white">এডিট</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">কোন স্টাফের তথ্য পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
