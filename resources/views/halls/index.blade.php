<x-tabler-layout :title="'হল ও রুম ম্যানেজমেন্ট'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">হল ও রুম ম্যানেজমেন্ট</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('halls.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            নতুন হল যুক্ত করুন
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
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        </div>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter table-mobile-md card-table">
                        <thead>
                            <tr>
                                <th>হলের নাম</th>
                                <th>ঠিকানা ও ফোন</th>
                                <th>ধারণক্ষমতা (মেহমান)</th>
                                <th>ভাড়া (প্রতি স্লট)</th>
                                <th>অবস্থা</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($halls as $hall)
                                <tr>
                                    <td data-label="হলের নাম" >
                                        <div class="font-weight-medium">{{ $hall->name }}</div>
                                    </td>
                                    <td data-label="ঠিকানা ও ফোন" >
                                        <div class="text-muted small">{{ $hall->address ?? 'ঠিকানা নেই' }}</div>
                                        <div class="text-muted small">{{ $hall->phone ?? 'ফোন নেই' }}</div>
                                    </td>
                                    <td data-label="ধারণক্ষমতা" >
                                        {{ $hall->capacity ?? 'নাই' }} জন
                                    </td>
                                    <td data-label="ভাড়া" >
                                        ৳ {{ number_format($hall->price_per_slot, 2) }}
                                    </td>
                                    <td data-label="অবস্থা" >
                                        @if($hall->is_active)
                                            <span class="badge bg-success">সচল</span>
                                        @else
                                            <span class="badge bg-danger">অচল</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('halls.show', $hall) }}" class="btn btn-sm btn-white">
                                                দেখুন
                                            </a>
                                            <a href="{{ route('halls.edit', $hall) }}" class="btn btn-sm btn-info">
                                                এডিট
                                            </a>
                                            <form id="delete-form-{{ $hall->id }}" action="{{ route('halls.destroy', $hall) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteConfirm('delete-form-{{ $hall->id }}', {
                                                    bookings: {{ $hall->bookings_count }},
                                                    incomes: {{ $hall->incomes_count }},
                                                    expenses: {{ $hall->expenses_count }},
                                                    commissions: {{ $hall->commissions_count }},
                                                    assets: {{ $hall->assets_count }}
                                                })">
                                                ডিলিট
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">কোন হল পাওয়া যায়নি। নতুন একটি হল যুক্ত করে শুরু করুন!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
