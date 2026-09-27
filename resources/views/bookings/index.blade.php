<x-tabler-layout :title="'বুকিং তালিকা'">
    @push('css')
    <style>
        .date-block {
            width: 46px; height: 50px; flex-shrink: 0;
            border: 1px solid var(--cui-border-color, #dee2e6);
            border-radius: .6rem; overflow: hidden;
            display: flex; flex-direction: column; text-align: center;
            background: var(--cui-body-bg, #fff);
        }
        .date-block .date-day { font-size: 1.15rem; font-weight: 800; line-height: 1.5; }
        .date-block .date-mon {
            font-size: .65rem; font-weight: 700; text-transform: uppercase;
            background: var(--cui-primary, #5856d6); color: #fff; padding: 1px 0;
        }
    </style>
    @endpush
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">বুকিং তালিকা</h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('bookings.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                নতুন বুকিং
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <!-- KPI Summary Cards -->
            <div class="row row-cards mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="border-left: 4px solid #004D40 !important;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-teal-lt avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">মোট বুকিং</div>
                                    <div class="text-dark fw-bold fs-2">{{ $totalCount ?? $bookings->total() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="border-left: 4px solid #2fb344 !important;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success-lt avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">নিশ্চিত বুকিং</div>
                                    <div class="text-dark fw-bold fs-2">{{ $confirmedCount ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="border-left: 4px solid #F5A623 !important;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-warning-lt avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">অপেক্ষমান</div>
                                    <div class="text-dark fw-bold fs-2">{{ $pendingCount ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="border-left: 4px solid #d63939 !important;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-danger-lt avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">মোট বকেয়া</div>
                                    <div class="text-danger fw-bold fs-3">৳ {{ number_format($totalDue ?? 0, 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Card -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('bookings.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4 col-sm-12">
                                <label class="form-label small fw-bold text-secondary mb-1">অনুসন্ধান (নাম / মোবাইল / বুকিং #)</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="গ্রাহকের নাম বা ফোন...">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small fw-bold text-secondary mb-1">অবস্থা (Status)</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all">সকল অবস্থা</option>
                                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>নিশ্চিত (Confirmed)</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>অপেক্ষমান (Pending)</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>সম্পন্ন (Completed)</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>বাতিল (Cancelled)</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small fw-bold text-secondary mb-1">পেমেন্ট অবস্থা</label>
                                <select name="payment_status" class="form-select form-select-sm">
                                    <option value="all">সকল পেমেন্ট</option>
                                    <option value="due" {{ request('payment_status') === 'due' ? 'selected' : '' }}>বকেয়া রয়েছে (Due)</option>
                                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>পরিশোধিত (Paid)</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small fw-bold text-secondary mb-1">অনুষ্ঠানের তারিখ</label>
                                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="btn-list flex-nowrap">
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="background-color: #004D40; border-color: #004D40;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" /></svg>
                                        ফিল্টার
                                    </button>
                                    @if(request()->hasAny(['search', 'status', 'payment_status', 'date']))
                                        <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary" title="রিসেট">
                                            ✕
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-lg bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                        বুকিং তালিকা
                                        <span class="badge bg-primary rounded-pill">{{ $totalCount ?? $bookings->total() }}টি</span>
                                    </h5>
                                    <p class="text-body-secondary small mb-0">ফিল্টার অনুযায়ী ফলাফল — সর্বশেষ আগে</p>
                                </div>
                            </div>
                        </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover align-middle mb-0">
                        <thead class="text-body-secondary">
                            <tr>
                                <th class="ps-3 fw-semibold small text-uppercase">বুকিং</th>
                                <th class="fw-semibold small text-uppercase">ইভেন্ট তারিখ</th>
                                <th class="fw-semibold small text-uppercase">গ্রাহক</th>
                                <th class="fw-semibold small text-uppercase">হল ও স্লট</th>
                                <th class="fw-semibold small text-uppercase">ইভেন্ট</th>
                                <th class="fw-semibold small text-uppercase" style="min-width:170px">পেমেন্ট</th>
                                <th class="fw-semibold small text-uppercase text-center">স্ট্যাটাস</th>
                                <th class="text-end pe-3 fw-semibold small text-uppercase">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                @php
                                    $firstItem = $booking->items->first();
                                    $eventDate = $firstItem && $firstItem->event_date ? \Carbon\Carbon::parse($firstItem->event_date)->startOfDay() : null;
                                    $daysLeft = $eventDate ? (int) now()->startOfDay()->diffInDays($eventDate, false) : null;
                                    $bnWeekdays = ['Saturday' => 'শনিবার', 'Sunday' => 'রবিবার', 'Monday' => 'সোমবার', 'Tuesday' => 'মঙ্গলবার', 'Wednesday' => 'বুধবার', 'Thursday' => 'বৃহস্পতিবার', 'Friday' => 'শুক্রবার'];
                                    $weekday = $eventDate ? ($bnWeekdays[$eventDate->format('l')] ?? $eventDate->format('l')) : null;
                                    $itemCount = $booking->items->count();
                                    $slotOf = fn($s) => match($s) { 'day', 'morning' => ['দিন', 'bg-warning-lt'], 'night', 'evening' => ['রাত', 'bg-indigo-lt'], default => ['সারাদিন', 'bg-info-lt'] };
                                    $total = (float) $booking->total_amount;
                                    $paid = (float) $booking->advance_amount;
                                    $due = max(0, $total - $paid);
                                    $paidPct = $total > 0 ? min(100, round($paid / $total * 100)) : 0;
                                    $statusMeta = match($booking->status) {
                                        'confirmed' => ['নিশ্চিত', 'success'],
                                        'completed' => ['সম্পন্ন', 'info'],
                                        'cancelled' => ['বাতিল', 'danger'],
                                        default => ['অপেক্ষমান', 'warning'],
                                    };
                                    $initials = mb_substr($booking->customer?->name ?? '?', 0, 2);
                                @endphp
                                <tr @if($daysLeft === 0) class="table-warning" @endif>
                                    <td class="ps-3">
                                        <a href="{{ route('bookings.show', $booking) }}" class="fw-bold text-decoration-none" title="বুকিং বিস্তারিত দেখুন">#{{ $booking->id }}</a>
                                        @if(!is_null($daysLeft))
                                            @if($daysLeft === 0)
                                                <div><span class="badge bg-danger mt-1">আজ</span></div>
                                            @elseif($daysLeft === 1)
                                                <div><span class="badge bg-info mt-1">আগামীকাল</span></div>
                                            @elseif($daysLeft === -1)
                                                <div><span class="badge bg-secondary mt-1">গতকাল</span></div>
                                            @elseif($daysLeft > 1 && $daysLeft <= 7)
                                                <div><span class="badge bg-warning-lt mt-1">{{ $daysLeft }} দিন বাকি</span></div>
                                            @elseif($daysLeft > 7)
                                                <div class="text-body-secondary small mt-1">{{ $daysLeft }} দিন বাকি</div>
                                            @else
                                                <div class="text-body-secondary small mt-1">{{ abs($daysLeft) }} দিন আগে</div>
                                            @endif
                                        @endif
                                        @if($itemCount > 1)
                                            <div><span class="badge bg-purple-lt mt-1">+{{ $itemCount - 1 }} দিন অতিরিক্ত</span></div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($eventDate)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="date-block">
                                                    <span class="date-day">{{ $eventDate->format('d') }}</span>
                                                    <span class="date-mon">{{ $eventDate->format('M') }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $weekday }}</div>
                                                    <div class="text-body-secondary small">{{ $eventDate->format('d M, Y') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-body-secondary">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar bg-primary text-white fw-bold">{{ $initials }}</span>
                                            <div>
                                                <div class="fw-semibold">{{ $booking->customer?->name ?? 'N/A' }}</div>
                                                @if($booking->customer?->phone)
                                                    <a href="tel:{{ $booking->customer->phone }}" class="text-body-secondary small text-decoration-none">{{ $booking->customer->phone }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($booking->items->pluck('hall.name')->unique() as $hallName)
                                            <div class="fw-semibold">{{ $hallName }}</div>
                                        @endforeach
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @if($itemCount == 1)
                                                @php [$slotLabel, $slotBadge] = $slotOf($booking->items->first()->slot); @endphp
                                                <span class="badge {{ $slotBadge }}">{{ $slotLabel }}</span>
                                            @else
                                                <span class="badge bg-secondary-lt">মাল্টি-স্লট</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $firstItem->event_type ?? '—' }}</div>
                                        <div class="text-body-secondary small">{{ $firstItem->guest_count ?? 0 }} জন অতিথি</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="fw-bold">৳ {{ number_format($total, 0) }}</span>
                                            <span class="text-body-secondary">{{ $paidPct }}%</span>
                                        </div>
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar {{ $due > 0 ? 'bg-warning' : 'bg-success' }}" role="progressbar" style="width: {{ $paidPct }}%"></div>
                                        </div>
                                        <div class="small mt-1">
                                            @if($due > 0)
                                                <span class="text-danger fw-semibold">বকেয়া ৳ {{ number_format($due, 0) }}</span>
                                                <span class="text-body-secondary"> · জমা ৳ {{ number_format($paid, 0) }}</span>
                                            @else
                                                <span class="text-success fw-semibold">পরিশোধিত</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false">অ্যাকশন</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('bookings.show', $booking) }}">বিস্তারিত দেখুন</a></li>
                                                <li><a class="dropdown-item" href="{{ route('bookings.invoice', $booking) }}">ইনভয়েস</a></li>
                                                <li><a class="dropdown-item" href="{{ route('bookings.edit', $booking) }}">এডিট করুন</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form id="delete-booking-{{ $booking->id }}" action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="button"
                                                            class="dropdown-item text-danger"
                                                            onclick="deleteConfirm('delete-booking-{{ $booking->id }}', {
                                                                booking: 'বুকিং #{{ $booking->id }}',
                                                                customer: '{{ addslashes($booking->customer->name) }}',
                                                                phone: '{{ addslashes($booking->customer->phone) }}',
                                                                date: '{{ addslashes($eventDate ? $eventDate->format('d M, Y') : 'N/A') }}',
                                                                halls: '{{ addslashes($booking->items->pluck('hall.name')->unique()->implode(', ')) }}',
                                                                total: '{{ number_format($booking->total_amount, 2) }}',
                                                                advance: '{{ number_format($booking->advance_amount, 2) }}'
                                                            })"
                                                        >মুছে ফেলুন</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="avatar avatar-lg bg-success-lt mx-auto mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                        </div>
                                        <div class="h5 mb-1">কোন বুকিং পাওয়া যায়নি</div>
                                        <p class="text-body-secondary small mb-3">ফিল্টার বদলে আবার চেষ্টা করুন অথবা নতুন বুকিং তৈরি করুন</p>
                                        <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">নতুন বুকিং করুন</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($bookings->hasPages())
                    <div class="card-footer bg-transparent d-flex align-items-center">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const originalDeleteConfirm = window.deleteConfirm;

            window.deleteConfirm = function (formId, booking = null) {
                if (!booking) {
                    return originalDeleteConfirm(formId);
                }

                const summary = `
                    <div class="text-start">
                        <div><strong>${booking.booking}</strong></div>
                        <div>গ্রাহক: ${booking.customer}</div>
                        <div>মোবাইল: ${booking.phone}</div>
                        <div>তারিখ: ${booking.date}</div>
                        <div>হল: ${booking.halls}</div>
                        <div>মোট টাকা: ৳ ${booking.total}</div>
                        <div>অগ্রিম: ৳ ${booking.advance}</div>
                    </div>
                `;

                Swal.fire({
                    title: 'আপনি কি নিশ্চিত?',
                    html: summary,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'হ্যাঁ, ডিলিট করুন!',
                    cancelButtonText: 'বাতিল'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            };
        });
    </script>
    @endpush
</x-tabler-layout>
