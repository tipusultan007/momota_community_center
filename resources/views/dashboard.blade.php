<x-tabler-layout :title="$title">
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
                        <div class="page-pretitle">একনজরে</div>
                        <h2 class="page-title">ড্যাশবোর্ড (এই মাস)</h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm border-0 shadow-lg overflow-hidden text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 110px;">
                        <div class="card-body d-flex align-items-center">
                            <div class="row align-items-center w-100">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg rounded-3" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase fw-bold small opacity-75 mb-1">মোট বুকিং</div>
                                    <div class="h1 fw-bold mb-0">{{ $totalBookingsCount }} <span class="small fw-normal">টি</span></div>
                                    <div class="small opacity-100 fw-medium mt-1">৳ {{ number_format($totalBookingsValue, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm border-0 shadow-lg overflow-hidden text-white" style="background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); min-height: 110px;">
                        <div class="card-body d-flex align-items-center">
                            <div class="row align-items-center w-100">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg rounded-3" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trending-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase fw-bold small opacity-75 mb-1">মোট আয়</div>
                                    <div class="h1 fw-bold mb-0">৳ {{ number_format($totalIncome, 0) }}</div>
                                    <div class="small opacity-80 mt-1">এই মাসে লাভজনক</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm border-0 shadow-lg overflow-hidden text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 110px;">
                        <div class="card-body d-flex align-items-center">
                            <div class="row align-items-center w-100">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg rounded-3" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trending-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 6l4 -4l8 8" /><path d="M21 10l0 7l-7 0" /></svg>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase fw-bold small opacity-75 mb-1">মোট ব্যয়</div>
                                    <div class="h1 fw-bold mb-0">৳ {{ number_format($totalExpense, 0) }}</div>
                                    <div class="small opacity-80 mt-1">ব্যয় কমানোর সুযোগ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    @php
                        $profit = $totalIncome - $totalExpense;
                        $profitGradient = $profit >= 0 
                            ? 'linear-gradient(135deg, #38f9d7 0%, #43e97b 100%)' 
                            : 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)';
                    @endphp
                    <div class="card card-sm border-0 shadow-lg overflow-hidden text-white" style="background: {{ $profitGradient }}; min-height: 110px;">
                        <div class="card-body d-flex align-items-center">
                            <div class="row align-items-center w-100">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg rounded-3" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" /></svg>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase fw-bold small opacity-75 mb-1">নীট লাভ</div>
                                    <div class="h1 fw-bold mb-0">৳ {{ number_format($profit, 0) }}</div>
                                    <div class="small opacity-80 mt-1">{{ $profit >= 0 ? 'সাফল্যের পথে' : 'লোকসান হচ্ছে' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-lg bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                        আসন্ন বুকিংসমূহ
                                        <span class="badge bg-primary rounded-pill">{{ $upcomingBookings->count() }}টি</span>
                                    </h5>
                                    <p class="text-body-secondary small mb-0">পরবর্তী ১০টি ইভেন্ট — তারিখের ক্রমানুসারে</p>
                                </div>
                            </div>
                            <div class="card-actions btn-list">
                                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">সব বুকিং দেখুন</a>
                                <a href="{{ route('calendar.index') }}" class="btn btn-primary btn-sm">ক্যালেন্ডার দেখুন</a>
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
                                    @forelse($upcomingBookings as $ub)
                                        @php
                                            $bk = $ub->booking;
                                            $eventDate = \Carbon\Carbon::parse($ub->event_date)->startOfDay();
                                            $daysLeft = (int) now()->startOfDay()->diffInDays($eventDate, false);
                                            $bnWeekdays = ['Saturday' => 'শনিবার', 'Sunday' => 'রবিবার', 'Monday' => 'সোমবার', 'Tuesday' => 'মঙ্গলবার', 'Wednesday' => 'বুধবার', 'Thursday' => 'বৃহস্পতিবার', 'Friday' => 'শুক্রবার'];
                                            $weekday = $bnWeekdays[$eventDate->format('l')] ?? $eventDate->format('l');
                                            $slotLabel = match($ub->slot) {
                                                'day', 'morning' => 'দিন',
                                                'night', 'evening' => 'রাত',
                                                default => 'সারাদিন',
                                            };
                                            $slotBadge = match($ub->slot) {
                                                'day', 'morning' => 'bg-warning-lt',
                                                'night', 'evening' => 'bg-indigo-lt',
                                                default => 'bg-info-lt',
                                            };
                                            $total = (float) ($bk->total_amount ?? 0);
                                            $paid = (float) ($bk->advance_amount ?? 0);
                                            $due = max(0, $total - $paid);
                                            $paidPct = $total > 0 ? min(100, round($paid / $total * 100)) : 0;
                                            $statusMeta = match($bk->status) {
                                                'confirmed' => ['নিশ্চিত', 'success'],
                                                'completed' => ['সম্পন্ন', 'info'],
                                                'cancelled' => ['বাতিল', 'danger'],
                                                default => ['অপেক্ষমান', 'warning'],
                                            };
                                            $initials = mb_substr($bk->customer?->name ?? '?', 0, 2);
                                        @endphp
                                        <tr @if($daysLeft === 0) class="table-warning" @endif>
                                            <td class="ps-3">
                                                <a href="{{ route('bookings.show', $bk) }}" class="fw-bold text-decoration-none">#{{ $bk->id }}</a>
                                                @if($daysLeft === 0)
                                                    <div><span class="badge bg-danger mt-1">আজ</span></div>
                                                @elseif($daysLeft === 1)
                                                    <div><span class="badge bg-info mt-1">আগামীকাল</span></div>
                                                @elseif($daysLeft <= 7)
                                                    <div><span class="badge bg-warning-lt mt-1">{{ $daysLeft }} দিন বাকি</span></div>
                                                @else
                                                    <div class="text-body-secondary small mt-1">{{ $daysLeft }} দিন বাকি</div>
                                                @endif
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="avatar bg-primary text-white fw-bold">{{ $initials }}</span>
                                                    <div>
                                                        <div class="fw-semibold">{{ $bk->customer?->name ?? 'N/A' }}</div>
                                                        @if($bk->customer?->phone)
                                                            <a href="tel:{{ $bk->customer->phone }}" class="text-body-secondary small text-decoration-none">{{ $bk->customer->phone }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $ub->hall?->name ?? '—' }}</div>
                                                <span class="badge {{ $slotBadge }} mt-1">{{ $slotLabel }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $ub->event_type ?? '—' }}</div>
                                                <div class="text-body-secondary small">{{ $ub->guest_count ?? 0 }} জন অতিথি</div>
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
                                                <div class="btn-list flex-nowrap justify-content-end">
                                                    <a href="{{ route('bookings.show', $bk) }}" class="btn btn-sm btn-outline-primary" title="বিস্তারিত দেখুন">বিস্তারিত</a>
                                                    <a href="{{ route('bookings.invoice', $bk) }}" class="btn btn-sm btn-outline-secondary" title="ইনভয়েস">ইনভয়েস</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="avatar avatar-lg bg-success-lt mx-auto mb-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                                </div>
                                                <div class="h5 mb-1">কোনো আসন্ন বুকিং নেই</div>
                                                <p class="text-body-secondary small mb-3">নতুন ইভেন্ট বুক করতে ক্যালেন্ডার থেকে তারিখ বেছে নিন</p>
                                                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">নতুন বুকিং করুন</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($upcomingBookings->isNotEmpty())
                        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center small text-body-secondary">
                            <span>মোট {{ $upcomingBookings->count() }}টি আসন্ন ইভেন্ট দেখানো হচ্ছে</span>
                            <a href="{{ route('bookings.index') }}" class="text-decoration-none fw-semibold">সব বুকিং →</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
