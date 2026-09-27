<x-tabler-layout :title="'ভেন্ডর তালিকা'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">ভেন্ডর তালিকা</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('vendors.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            নতুন ভেন্ডর যুক্ত করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div>{{ session('success') }}</div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>নাম</th>
                                <th>ধরণ</th>
                                <th>মোবাইল</th>
                                <th>কমিশন হার (%)</th>
                                <th>অবস্থা</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr>
                                    <td>
                                        <a href="{{ route('vendors.show', $vendor) }}" class="fw-bold fs-3">{{ $vendor->name }}</a>
                                        <div class="text-muted small">{{ $vendor->phone ?? 'মোবাইল নেই' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-lt">
                                            @if($vendor->type == 'catering') খাবার সরবরাহকারী (Catering)
                                            @elseif($vendor->type == 'decoration') ডেকোরেশন (Decoration)
                                            @elseif($vendor->type == 'sound') সাউন্ড ও লাইটিং (Sound)
                                            @elseif($vendor->type == 'generator') জেনারেটর (Generator)
                                            @else {{ ucfirst($vendor->type) }} @endif
                                        </span>
                                    </td>
                                    <td>{{ $vendor->phone }}</td>
                                    <td>{{ $vendor->commission_rate }}%</td>
                                    <td>
                                        @if($vendor->is_active)
                                            <span class="badge bg-success">সক্রিয়</span>
                                        @else
                                            <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-list flex-nowrap justify-content-end">
                                            <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-sm btn-outline-info">প্রোফাইল</a>
                                            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                                            <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">ডিলিট</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">কোনো ভেন্ডর পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $vendors->links() }}
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
