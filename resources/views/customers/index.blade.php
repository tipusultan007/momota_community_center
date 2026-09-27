<x-tabler-layout :title="'গ্রাহক তালিকা (CRM)'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">গ্রাহক তালিকা (CRM)</h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('customers.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                নতুন গ্রাহক যোগ করুন
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th>নাম</th>
                                <th>ফোন নম্বর</th>
                                <th>এনআইডি (NID)</th>
                                <th>ঠিকানা</th>
                                <th class="text-end pe-3 w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                    </td>
                                    <td>
                                        <span class="text-secondary fw-medium">{{ $customer->phone }}</span>
                                    </td>
                                    <td>
                                        @if($customer->nid)
                                            <span class="badge bg-secondary-lt">{{ $customer->nid }}</span>
                                        @else
                                            <span class="text-muted small">নাই</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary">{{ $customer->address ?? 'নাই' }}</td>
                                    <td class="text-end pe-3">
                                        <div class="btn-list flex-nowrap justify-content-end">
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning" title="এডিট করুন">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                এডিট
                                            </a>
                                            <form id="delete-customer-{{ $customer->id }}" action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDeleteCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ addslashes($customer->phone) }}')"
                                                    title="মুছে ফেলুন"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                    মুছে ফেলুন
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">কোন গ্রাহকের তথ্য পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($customers->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('js')
    <script>
        function confirmDeleteCustomer(id, name, phone) {
            Swal.fire({
                title: 'আপনি কি নিশ্চিত?',
                html: `গ্রাহক <strong>${name}</strong> (${phone})-এর তথ্য মুছে ফেলতে চান?<br><small class="text-danger d-block mt-2">সতর্কতা: এই অ্যাকশনটি ফেরানো সম্ভব নয়!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
                cancelButtonText: 'বাতিল'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-customer-' + id).submit();
                }
            });
        }
    </script>
    @endpush
</x-tabler-layout>
