<x-tabler-layout :title="'ম্যানুয়াল পেমেন্ট গেটওয়ে'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">ম্যানুয়াল পেমেন্ট গেটওয়ে সেটআপ</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.payment-gateways.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            + নতুন গেটওয়ে যুক্ত করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>নাম</th>
                                <th>অ্যাকাউন্ট নম্বর</th>
                                <th>অ্যাকাউন্ট টাইপ</th>
                                <th>স্ট্যাটাস</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gateways as $gateway)
                                <tr>
                                    <td><strong>{{ $gateway->name }}</strong></td>
                                    <td>{{ $gateway->account_number }}</td>
                                    <td class="text-secondary">{{ $gateway->account_type }}</td>
                                    <td>
                                        @if($gateway->is_active)
                                            <span class="badge bg-success text-white">সক্রিয়</span>
                                        @else
                                            <span class="badge bg-secondary text-white">নিষ্ক্রিয়</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" class="btn btn-sm btn-white">এডিট</a>
                                            <form action="{{ route('admin.payment-gateways.destroy', $gateway) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">ডিলিট</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">কোন পেমেন্ট গেটওয়ে নেই। নতুন গেটওয়ে যুক্ত করুন।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
