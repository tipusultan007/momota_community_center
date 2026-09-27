<x-tabler-layout :title="'বুকিং ক্যালেন্ডার'">
    @push('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        #calendar, .card-title, .list-group-item, .page-title, #bn-today-display-box {
            font-family: 'Anek Bangla', 'Inter', sans-serif !important;
        }
        .fc-event { cursor: pointer; }
        .fc-toolbar-title { font-size: 1.5rem !important; font-weight: 700; }
        
        /* English Date Styling */
        .fc-daygrid-day-number {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            color: #1d273b !important;
            padding: 8px !important;
        }
        
        .bn-date {
            font-size: 0.95rem;
            color: #626976;
            position: absolute;
            bottom: 8px;
            right: 8px;
            font-weight: 700;
            background: rgba(241, 243, 245, 0.9);
            padding: 2px 6px;
            border-radius: 4px;
            line-height: 1.2;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .fc-day-today .bn-date { 
            color: #206bc4; 
            background: rgba(32, 107, 196, 0.1);
            border-color: rgba(32, 107, 196, 0.2);
        }
        .fc-daygrid-day-top {
            flex-direction: row !important;
        }
    </style>
    @endpush

    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">বুকিং ক্যালেন্ডার</h2>
                </div>
                <!-- Bengali Today Display -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <span class="btn btn-outline-info disabled" id="bn-today-display-box">
                            আজ: <span id="bn-today-display">...</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="row row-cards">
        <!-- Calendar (Full Width) -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings (Below Calendar) -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">আসন্ন বুকিংসমূহ</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row row-cards p-3">
                        @php
                            $colors = ['blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime', 'green', 'teal', 'cyan'];
                        @endphp
                        @forelse($upcomingBookings as $ub)
                            @php
                                $color = $colors[$loop->index % count($colors)];
                            @endphp
                            <div class="col-md-4 col-xl-3">
                                <div class="card card-sm bg-{{ $color }}-lt border-{{ $color }}-subtle shadow-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    <a href="{{ route('bookings.show', $ub->booking_id) }}" class="text-{{ $color }} fw-bold">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                                                        {{ \Carbon\Carbon::parse($ub->event_date)->format('d-m-Y') }}
                                                    </a>
                                                </div>
                                                <div class="mt-1 fw-bold text-dark">
                                                    {{ $ub->event_type }} - {{ $ub->booking?->customer?->name ?? 'N/A' }}
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <span class="badge badge-outline text-{{ $color }}">{{ $ub->hall?->name }}</span>
                                                    <span class="ms-1">| @if($ub->slot == 'morning') সকালে @elseif($ub->slot == 'evening') রাতে @else সারাদিন @endif</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">কোনো আসন্ন বুকিং নেই।</div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer small text-muted">সর্বশেষ ৫টি বুকিং দেখানো হচ্ছে।</div>
            </div>
        </div>
    </div>

    <!-- Booking Detail Modal -->
    <div class="modal modal-blur fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">বুকিং বিস্তারিত</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Dynamic content will be injected here -->
                </div>
                <div class="modal-footer">
                    <a href="#" id="viewBtn" class="btn btn-primary">বিস্তারিত দেখুন</a>
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        function getBanglaDate(gregDate) {
            const bn_months = ["বৈশাখ", "জ্যৈষ্ঠ", "আষাঢ়", "শ্রাবণ", "ভাদ্র", "আশ্বিন", "কার্তিক", "অগ্রহায়ণ", "পৌষ", "মাঘ", "ফাল্গুন", "চৈত্র"];
            const bn_numbers = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
            const convertToBnNum = (n) => String(n).split('').map(d => bn_numbers[d] || d).join('');
            
            const date = new Date(Date.UTC(gregDate.getFullYear(), gregDate.getMonth(), gregDate.getDate()));
            const year = date.getUTCFullYear();
            const isLeapYear = (y) => (y % 4 === 0 && y % 100 !== 0) || (y % 400 === 0);
            
            // West Bengal (India) Civil Rule:
            // Year usually starts on April 15 (Poila Baisakh)
            let startOfBnYear = new Date(Date.UTC(year, 3, 15));
            let bn_year;
            if (date < startOfBnYear) {
                bn_year = year - 594;
                startOfBnYear = new Date(Date.UTC(year - 1, 3, 15));
            } else {
                bn_year = year - 593;
            }
            
            let diffDays = Math.floor((date.getTime() - startOfBnYear.getTime()) / 86400000);
            
            // West Bengal Standard Approximation:
            // First 5 months (Baishakh - Bhadra) = 31 days
            // Following 7 months (Ashwin - Chaitra) = 30 days
            const lengths = [31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30, 30];
            
            // Leap year adjustment for standard civil Indian Bengali calendar:
            if (isLeapYear(startOfBnYear.getUTCFullYear())) {
                lengths[11] = 31; // Chaitra 31 in leap year
            }
            
            let bn_month = 0;
            while (diffDays >= lengths[bn_month]) {
                diffDays -= lengths[bn_month];
                bn_month++;
                if (bn_month > 11) break;
            }
            let bn_day = diffDays + 1;

            return {
                day: convertToBnNum(bn_day),
                dayNum: bn_day,
                month: bn_months[bn_month],
                year: convertToBnNum(bn_year),
                full: `${convertToBnNum(bn_day)} ${bn_months[bn_month]} ${convertToBnNum(bn_year)}`
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const todayBn = getBanglaDate(new Date());
            const displayEl = document.getElementById('bn-today-display');
            if (displayEl) displayEl.innerText = todayBn.full;

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'bn',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listMonth'
                },
                buttonText: {
                    month: 'মাসিক',
                    week: 'সাপ্তাহিক',
                    list: 'তালিকা'
                },
                dayCellContent: function(arg) {
                    return { html: `<div class="fc-daygrid-day-number">${arg.date.getDate()}</div>` };
                },
                events: "{{ route('calendar.events') }}",
                datesSet: function(info) {
                    const start = info.view.activeStart;
                    const end = new Date(info.view.activeEnd.getTime() - 1);
                    
                    const bnStart = getBanglaDate(start);
                    const bnEnd = getBanglaDate(end);
                    
                    let bnTitle = "";
                    if (bnStart.month === bnEnd.month) {
                        bnTitle = `${bnStart.month} ${bnStart.year}`;
                    } else {
                        if (bnStart.year === bnEnd.year) {
                            bnTitle = `${bnStart.month} - ${bnEnd.month} ${bnStart.year}`;
                        } else {
                            bnTitle = `${bnStart.month} ${bnStart.year} - ${bnEnd.month} ${bnEnd.year}`;
                        }
                    }
                    
                    const titleEl = document.querySelector('.fc-toolbar-title');
                    if (titleEl) {
                        // Use a more robust way to set the title. 
                        // FullCalendar sets this title, we want to append to it once.
                        // We store the original 'clean' title in a data attribute if needed,
                        // but usually info.view.title is what we want.
                        setTimeout(() => {
                            titleEl.innerText = `${info.view.title} / ${bnTitle}`;
                        }, 1);
                    }
                },
                dayCellDidMount: function(info) {
                    const bn = getBanglaDate(info.date);
                    const dot = document.createElement('div');
                    dot.className = 'bn-date';
                    // Show month name if it's the 1st of the Bengali month
                    dot.innerText = bn.dayNum === 1 ? `${bn.day} ${bn.month}` : bn.day;
                    info.el.querySelector('.fc-daygrid-day-frame').appendChild(dot);
                },
                eventClick: function(info) {
                    var props = info.event.extendedProps;
                    var statusColor = props.status === 'Confirmed' ? 'success' : (props.status === 'Cancelled' ? 'danger' : 'warning');
                    var content = `
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-1"><span class="badge bg-${statusColor}-lt">${props.status}</span></div>
                                <h3 class="mb-0">${props.customer}</h3>
                                <div class="text-muted">${props.phone}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">হল ও স্লট</div>
                                <div><strong>${props.hall}</strong></div>
                                <div class="small">${(props.slot === 'day' || props.slot === 'morning') ? 'দিন (Day)' : ((props.slot === 'night' || props.slot === 'evening') ? 'রাত (Night)' : 'সারাদিন')}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="small text-muted">ইভেন্ট ও অতিথি</div>
                                <div><strong>${props.event_type}</strong></div>
                                <div class="small">${props.guest_count} জন মেহমান | ${props.server_count} জন ওয়েটার</div>
                            </div>
                            <div class="col-12"><hr class="my-0"></div>
                            <div class="col-4">
                                <div class="small text-muted font-weight-bold">মোট টাকা</div>
                                <div>৳ ${props.total_amount}</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted font-weight-bold text-success">পরিশোধিত</div>
                                <div class="text-success">৳ ${props.paid_amount}</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="small text-muted font-weight-bold text-danger">বাকি</div>
                                <div class="text-danger">৳ ${props.due_amount}</div>
                            </div>
                        </div>
                    `;
                    document.getElementById('modalContent').innerHTML = content;
                    document.getElementById('viewBtn').href = "{{ url('bookings') }}/" + props.booking_id;
                    
                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                }
            });
            calendar.render();
        });
    </script>
    @endpush
</x-tabler-layout>
