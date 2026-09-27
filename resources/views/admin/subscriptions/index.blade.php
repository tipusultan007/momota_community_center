<x-tabler-layout :title="'সাবস্ক্রিপশন ম্যানেজমেন্ট'">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">সিস্টেম সাবস্ক্রিপশন সমূহ</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ভেন্ডর (টেন্যান্ট)</th>
                                <th>প্যাকেজ (প্ল্যান)</th>
                                <th>পেমেন্ট তথ্য</th>
                                <th>পরিশোধ (৳)</th>
                                <th>তারিখ</th>
                                <th>স্ট্যাটাস</th>
                                <th class="w-1">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $sub)
                                <tr>
                                    <td>
                                        <div class="font-weight-medium">{{ $sub->tenant->name ?? 'অজানা' }}</div>
                                        <div class="text-secondary small">ID: {{ $sub->tenant_id ?? '' }}</div>
                                    </td>
                                    <td>
                                        <strong>{{ ucfirst($sub->plan) }}</strong> 
                                    </td>
                                    <td>
                                        <div><strong>{{ $sub->payment_method ?? 'N/A' }}</strong></div>
                                        <div class="text-secondary small">নং: {{ $sub->sender_number ?? 'N/A' }}</div>
                                        <div class="text-secondary small">TrxID: {{ $sub->transaction_id ?? 'N/A' }}</div>
                                    </td>
                                    <td>৳ {{ number_format($sub->amount, 2) }}</td>
                                    <td>
                                        <div>{{ $sub->created_at->format('d M Y') }}</div>
                                        @if($sub->starts_at && $sub->ends_at)
                                            <div class="text-secondary small">মেয়াদ: {{ $sub->starts_at->format('d/m/Y') }} - {{ $sub->ends_at->format('d/m/Y') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sub->status === 'pending')
                                            <span class="badge bg-warning text-white">অপেক্ষমান</span>
                                        @elseif($sub->status === 'active')
                                            <span class="badge bg-success text-white">অনুমোদিত</span>
                                        @else
                                            <span class="badge bg-danger text-white">বাতিল</span>
                                            @if($sub->reject_reason)
                                                <div class="small text-muted mt-1" style="max-width: 150px;">কারণ: {{ $sub->reject_reason }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('admin.subscriptions.edit', $sub->id) }}" class="btn btn-sm btn-outline-primary" title="সম্পাদনা করুন">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                এডিট
                                            </a>
                                            @if($sub->status === 'pending')
                                                <form id="approve-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.approve', $sub->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                                <button type="button" class="btn btn-sm btn-success" onclick="approveSubscription({{ $sub->id }})">অনুমোদন</button>

                                                <form id="reject-form-{{ $sub->id }}" action="{{ route('admin.subscriptions.reject', $sub->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    <input type="hidden" name="reject_reason" id="reject-reason-{{ $sub->id }}">
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="rejectSubscription({{ $sub->id }})">বাতিল</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">কোন সাবস্ক্রিপশন ডাটা পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($subscriptions->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $subscriptions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('js')
    <script>
        function approveSubscription(id) {
            Swal.fire({
                title: 'পেমেন্ট নিশ্চিত করুন',
                text: "আপনি কি নিশ্চিত যে পেমেন্টটি সঠিকভাবে পাওয়া গেছে? অনুমোদন করলে এই ভেন্ডরের সাবস্ক্রিপশন চালু হয়ে যাবে।",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2fb344',
                cancelButtonColor: '#d33',
                confirmButtonText: 'হ্যাঁ, অনুমোদন করুন',
                cancelButtonText: 'বাতিল'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + id).submit();
                }
            })
        }

        function rejectSubscription(id) {
            Swal.fire({
                title: 'রিকোয়েস্টটি বাতিল করুন',
                text: "দয়া করে পেমেন্ট রিকোয়েস্টটি বাতিল করার একটি প্রাসঙ্গিক কারণ উল্লেখ করুন। এই কারণটি ভেন্ডর দেখতে পাবেন।",
                input: 'textarea',
                inputPlaceholder: 'যেমন: পর্যাপ্ত টাকা আসেনি, অথবা ট্রানজেকশন আইডি ভুল...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'রিকোয়েস্টটি বাতিল করুন',
                cancelButtonText: 'ফিরে যান',
                inputValidator: (value) => {
                    if (!value) {
                        return 'আপনাকে অবশ্যই একটি কারণ উল্লেখ করতে হবে!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason-' + id).value = result.value;
                    document.getElementById('reject-form-' + id).submit();
                }
            })
        }
    </script>
    @endpush
</x-tabler-layout>
