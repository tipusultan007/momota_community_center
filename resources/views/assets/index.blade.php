<x-tabler-layout :title="'মালামাল ও ইনভেন্টরি তালিকা'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">মালামাল ও ইনভেন্টরি তালিকা</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('assets.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            নতুন মালামাল যুক্ত করুন
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div>{{ session('error') }}</div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>নাম</th>
                                <th>মোট স্টক</th>
                                <th>বর্তমানে আছে</th>
                                <th>বিস্তারিত</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                <tr>
                                    <td class="font-weight-bold">{{ $asset->name }}</td>
                                    <td>{{ $asset->total_stock }}</td>
                                    <td>
                                        <span class="badge {{ $asset->available_stock < 10 ? 'bg-danger-lt' : 'bg-success-lt' }}">
                                            {{ $asset->available_stock }}
                                        </span>
                                    </td>
                                    <td class="text-secondary small">{{ Str::limit($asset->description, 50) }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-white">এডিট</a>
                                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white">ডিলিট</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">কোনো মালামাল পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
