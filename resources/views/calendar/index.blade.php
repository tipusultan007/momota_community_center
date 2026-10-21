<x-tabler-layout :title="'বুকিং ক্যালেন্ডার'">
    @push('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        #calendar, .card-title, .list-group-item, .page-title, #bn-today-display-box, .wedding-date-card {
            font-family: 'Anek Bangla', 'Inter', sans-serif !important;
        }
        .fc-event { cursor: pointer; }
        .fc-toolbar-title { font-size: 1.35rem !important; font-weight: 700; }
        
        /* English Date Styling */
        .fc-daygrid-day-number {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #1d273b !important;
            padding: 6px 8px !important;
        }
        
        /* Both Bangla Academy & Traditional Panjika Date Badges */
        .bn-date-box {
            position: absolute;
            bottom: 4px;
            right: 4px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
            z-index: 2;
            pointer-events: none;
        }
        .bn-date-ba {
            font-size: 0.72rem;
            font-weight: 600;
            color: #495057;
            background: rgba(241, 243, 245, 0.95);
            padding: 1px 4px;
            border-radius: 3px;
            line-height: 1.15;
            border: 1px solid rgba(0,0,0,0.08);
            white-space: nowrap;
        }
        .bn-date-pj {
            font-size: 0.72rem;
            font-weight: 700;
            color: #92400e;
            background: rgba(254, 243, 199, 0.95);
            padding: 1px 4px;
            border-radius: 3px;
            line-height: 1.15;
            border: 1px solid rgba(245, 158, 11, 0.3);
            white-space: nowrap;
        }
        .fc-day-today .bn-date-ba { 
            color: #206bc4; 
            background: rgba(32, 107, 196, 0.12);
            border-color: rgba(32, 107, 196, 0.25);
        }
        .fc-daygrid-day.shuvo-bibaho-cell .bn-date-pj {
            background: #f59e0b;
            color: #ffffff !important;
            border-color: #d97706;
            box-shadow: 0 1px 3px rgba(217, 119, 6, 0.35);
        }
        .fc-daygrid-day-top {
            flex-direction: row !important;
        }

        /* Auspicious Hindu Wedding Date (Shuvo Bibaho) Cell Highlight */
        .fc-daygrid-day.shuvo-bibaho-cell {
            background: linear-gradient(145deg, rgba(254, 243, 199, 0.55) 0%, rgba(253, 230, 138, 0.3) 100%) !important;
            box-shadow: inset 0 0 0 2px #f59e0b !important;
            position: relative;
        }
        .fc-daygrid-day.shuvo-bibaho-cell:hover {
            background: linear-gradient(145deg, rgba(254, 243, 199, 0.85) 0%, rgba(253, 230, 138, 0.5) 100%) !important;
        }
        .fc-daygrid-day.shuvo-bibaho-cell .fc-daygrid-day-number {
            color: #b45309 !important;
        }

        /* Wedding Badge inside the Calendar Day Cell */
        .wedding-day-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: linear-gradient(135deg, #d97706, #b45309);
            color: #ffffff !important;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 4px;
            margin: 2px 4px 4px 4px;
            box-shadow: 0 1px 3px rgba(180, 83, 9, 0.35);
            line-height: 1.15;
            cursor: pointer;
            z-index: 3;
            max-width: 95%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .wedding-day-badge:hover {
            opacity: 0.95;
            transform: scale(1.02);
        }
        .wedding-avail-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 2px;
        }

        /* Sidebar wedding date item card */
        .wedding-date-card {
            border: 1px solid #fcd34d;
            background: #fffbeb;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .wedding-date-card:hover {
            border-color: #f59e0b;
            box-shadow: 0 4px 8px rgba(245, 158, 11, 0.15);
            transform: translateY(-1px);
        }
        .wedding-date-card.is-booked {
            background: #fef2f2;
            border-color: #fca5a5;
        }
        .wedding-date-card.is-partial {
            background: #fff7ed;
            border-color: #fdba74;
        }

        /* Color utilities */
        .text-amber { color: #d97706 !important; }
        .bg-amber { background-color: #f59e0b !important; color: #ffffff !important; }
        .bg-amber-lt { color: #b45309 !important; background-color: rgba(245, 158, 11, 0.12) !important; }
        .border-amber { border-color: #f59e0b !important; }

        .text-azure { color: #4299e1 !important; }
        .bg-azure { background-color: #4299e1 !important; color: #ffffff !important; }
        .bg-azure-lt { color: #206bc4 !important; background-color: rgba(66, 153, 225, 0.12) !important; }
        .border-azure { border-color: #4299e1 !important; }
    </style>
    @endpush

    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">বুকিং ক্যালেন্ডার</h2>
                </div>
                <!-- Bengali Today Display & Add Wedding Date Button -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <span class="btn btn-outline-info disabled" id="bn-today-display-box">
                            আজ: <span id="bn-today-display">...</span>
                        </span>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-coreui-toggle="modal" data-bs-target="#addWeddingDateModal" data-coreui-target="#addWeddingDateModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                            + শুভ বিবাহের তারিখ যোগ করুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="row row-cards">
        <!-- Main Calendar Column (col-lg-8 col-xl-9) -->
        <div class="col-lg-8 col-xl-9">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-2 px-3 bg-light-subtle gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-warning-lt text-warning fw-bold border border-warning px-2 py-1">
                            💍 শুভ বিবাহের লগ্ন হাইলাইট
                        </span>
                        <span class="badge bg-success-lt text-success px-2 py-1">
                            <span class="wedding-avail-dot bg-success"></span> সম্পূর্ণ খালি
                        </span>
                        <span class="badge bg-danger-lt text-danger px-2 py-1">
                            <span class="wedding-avail-dot bg-danger"></span> সম্পূর্ণ বুকড
                        </span>
                        <span class="badge bg-secondary-lt text-dark px-2 py-1" title="সরকারি বাংলা একাডেমি ক্যালেন্ডার">
                            <span class="badge bg-secondary text-white py-0 px-1 me-1" style="font-size: 0.65rem;">বা</span> বাংলা একাডেমি
                        </span>
                        <span class="badge px-2 py-1" style="background: rgba(254, 243, 199, 0.95); color: #92400e; border: 1px solid #f59e0b;" title="বিশুদ্ধ সিদ্ধান্ত পঞ্জিকা (বিবাহ লগ্ন ও পূজা - প্রোকেরালা)">
                            <span class="badge py-0 px-1 me-1" style="background: #f59e0b; color: white; font-size: 0.65rem;">বি</span> বিশুদ্ধ সিদ্ধান্ত (পঞ্জিকা)
                        </span>
                    </div>
                    <div class="small text-muted">
                        * সোনালী বক্স ও <strong>বি:</strong> বিশুদ্ধ সিদ্ধান্ত পঞ্জিকা (প্রোকেরালা) অনুসারে শুভ বিবাহের লগ্ন চিহ্নিত
                    </div>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div id='calendar'></div>
                </div>
            </div>

            <!-- Upcoming Bookings (Below Calendar) -->
            <div class="card shadow-sm border-0">
                <div class="card-header py-2 px-3">
                    <h3 class="card-title fw-bold">আসন্ন বুকিংসমূহ</h3>
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
                            <div class="col-md-6 col-xl-4">
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
                                                    <span class="badge badge-outline text-{{ $color }}">{{ ($businessSetting ?? null)?->company_name ?? config('app.name', 'মমতা কমিউনিটি সেন্টার') }}</span>
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
                <div class="card-footer small text-muted py-2 px-3">সর্বশেষ ৫টি বুকিং দেখানো হচ্ছে।</div>
            </div>
        </div>

        <!-- Shuvo Bibaho (Hindu Wedding Dates) Sidebar Column (col-lg-4 col-xl-3) -->
        <div class="col-lg-4 col-xl-3">
            <div class="card shadow-sm border-0 sticky-top" style="top: 1rem;">
                <div class="card-header bg-warning-subtle text-dark py-2 px-3 border-bottom border-warning">
                    <div>
                        <h3 class="card-title fw-bold mb-0 d-flex align-items-center gap-2" style="font-size: 0.98rem; color: #92400e;">
                            <span>💍</span>
                            <span>শুভ বিবাহের লগ্নসমূহ</span>
                        </h3>
                        <div class="small text-muted mt-1" id="sidebar-month-title">
                            চলতি মাসের তালিকা
                        </div>
                    </div>
                    <div class="card-actions ms-auto">
                        <button type="button" class="btn btn-sm btn-warning btn-icon" data-bs-toggle="modal" data-coreui-toggle="modal" data-bs-target="#addWeddingDateModal" data-coreui-target="#addWeddingDateModal" title="নতুন শুভ বিবাহের তারিখ যোগ করুন">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="card-body p-2" id="wedding-dates-sidebar-list" style="max-height: 560px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-warning mb-2" role="status"></div>
                        <div>তথ্য লোড হচ্ছে...</div>
                    </div>
                </div>

                <div class="card-footer py-2 px-3 bg-light-subtle d-flex justify-content-between align-items-center small text-muted" id="sidebar-summary-footer">
                    <span id="sidebar-count-text">০টি লগ্ন</span>
                    <button class="btn btn-xs btn-outline-warning" data-bs-toggle="modal" data-coreui-toggle="modal" data-bs-target="#addWeddingDateModal" data-coreui-target="#addWeddingDateModal">+ নতুন যোগ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Detail Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">বুকিং বিস্তারিত</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
                <div class="modal-footer">
                    <a href="#" id="viewBtn" class="btn btn-primary">বিস্তারিত দেখুন</a>
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal" data-coreui-dismiss="modal">বন্ধ করুন</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Wedding Date Detail Modal -->
    <div class="modal fade" id="weddingDateDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title text-dark fw-bold d-flex align-items-center gap-2">
                        <span>💍</span>
                        <span id="wd-detail-title">শুভ বিবাহ লগ্ন</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="wdDetailContent"></div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="wdDeleteBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        তারিখটি মুছুন
                    </button>
                    <div>
                        <a href="#" id="wdBookNowBtn" class="btn btn-success">এই তারিখে বুকিং তৈরি করুন</a>
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal" data-coreui-dismiss="modal">বন্ধ করুন</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Wedding Date Modal -->
    <div class="modal fade" id="addWeddingDateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="addWeddingDateForm">
                    @csrf
                    <div class="modal-header bg-warning-subtle">
                        <h5 class="modal-title text-dark fw-bold d-flex align-items-center gap-2">
                            <span>💍</span>
                            <span>নতুন শুভ বিবাহের তারিখ যুক্ত করুন</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">ইংরেজি তারিখ</label>
                            <input type="date" name="date" id="wd-input-date" class="form-control" required value="{{ date('Y-m-d') }}">
                            <!-- Dual Date Calculation Preview -->
                            <div class="p-2 rounded mt-2 border" style="background: #fafbfc;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted"><span class="badge bg-secondary-lt me-1">বা</span> বাংলা একাডেমি (সরকারি):</span>
                                    <span class="fw-bold small text-dark" id="modal-preview-academy">—</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small" style="color: #92400e;"><span class="badge bg-warning-lt text-dark me-1">বি</span> বিশুদ্ধ সিদ্ধান্ত (প্রোকেরালা):</span>
                                    <span class="fw-bold small" style="color: #b45309;" id="modal-preview-panjika">—</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">শিরোনাম</label>
                            <input type="text" name="title" class="form-control" value="শুভ বিবাহ লগ্ন" placeholder="যেমন: শুভ বিবাহ লগ্ন">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">লগ্ন / তিথি</label>
                                <select name="tithi" class="form-select">
                                    <option value="রাত্রিলগ্ন" selected>রাত্রিলগ্ন</option>
                                    <option value="দিবালগ্ন">দিবালগ্ন</option>
                                    <option value="সন্ধ্যার লগ্ন">সন্ধ্যার লগ্ন</option>
                                    <option value="অমৃতযোগ">অমৃতযোগ</option>
                                    <option value="সারাদিন শুভলগ্ন">সারাদিন শুভলগ্ন</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">বাংলা তারিখ (ঐচ্ছিক)</label>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-xs btn-outline-warning py-0 px-1" id="btn-use-panjika" title="বিশুদ্ধ সিদ্ধান্ত তারিখ বসান">বিশুদ্ধ সিদ্ধান্ত</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" id="btn-use-academy" title="বাংলা একাডেমি তারিখ বসান">একাডেমি</button>
                                    </div>
                                </div>
                                <input type="text" name="bengali_date" id="wd-input-bn-date" class="form-control" placeholder="যেমন: ৬ কার্তিক ১৪৩৩">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">নোট (ঐচ্ছিক)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="পঞ্জিকা বা বিশেষ কোনো বিবরণ থাকলে লিখুন..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal" data-coreui-dismiss="modal">বাতিল</button>
                        <button type="submit" class="btn btn-warning" id="submitWdBtn">সংরক্ষণ করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        // Calculate both Bangla Academy & Traditional Hindu Panjika dates
        function getBothBanglaDates(gregDate) {
            const bn_months = ["বৈশাখ", "জ্যৈষ্ঠ", "আষাঢ়", "শ্রাবণ", "ভাদ্র", "আশ্বিন", "কার্তিক", "অগ্রহায়ণ", "পৌষ", "মাঘ", "ফাল্গুন", "চৈত্র"];
            const bn_numbers = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
            const convertToBnNum = (n) => String(n).split('').map(d => bn_numbers[d] || d).join('');
            const isLeapYear = (y) => (y % 4 === 0 && y % 100 !== 0) || (y % 400 === 0);

            const year = gregDate.getFullYear();
            const month = gregDate.getMonth();
            const day = gregDate.getDate();
            const curUtc = new Date(Date.UTC(year, month, day));

            // 1. BANGLA ACADEMY (Official Bangladesh Government Reformed Calendar)
            // Starts April 14 = 1 Boishakh
            let baStart = new Date(Date.UTC(year, 3, 14));
            let baYear = year - 593;
            if (curUtc < baStart) {
                baYear = year - 594;
                baStart = new Date(Date.UTC(year - 1, 3, 14));
            }
            let baDiff = Math.floor((curUtc.getTime() - baStart.getTime()) / 86400000);
            const baLengths = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, isLeapYear(year) ? 30 : 29, 30];
            let baM = 0;
            while (baDiff >= baLengths[baM] && baM < 11) {
                baDiff -= baLengths[baM];
                baM++;
            }
            const baDay = baDiff + 1;
            const academy = {
                day: convertToBnNum(baDay),
                dayNum: baDay,
                month: bn_months[baM],
                monthIndex: baM,
                year: convertToBnNum(baYear),
                full: `${convertToBnNum(baDay)} ${bn_months[baM]} ${convertToBnNum(baYear)}`
            };

            // 2. TRADITIONAL HINDU PANJIKA (Bisuddha Siddhanta / Prokerala based)
            // Month ingress dates:
            // Magh (Jan 16), Falgun (Feb 14), Chaitra (Mar 15), Boishakh (Apr 15), Jyeshtha (May 16),
            // Ashadha (Jun 16), Srabon (Jul 18), Bhadra (Aug 19), Ashwin (Sep 19), Kartik (Oct 19),
            // Agrahayan (Nov 18), Poush (Dec 17)
            const panjikaIngress = [
                { gM: 0, gD: 16, bM: 9 },  // Jan 16 -> Magh (ends Feb 13)
                { gM: 1, gD: 14, bM: 10 }, // Feb 14 -> Falgun (ends Mar 14)
                { gM: 2, gD: 15, bM: 11 }, // Mar 15 -> Chaitra (ends Apr 14)
                { gM: 3, gD: 15, bM: 0 },  // Apr 15 -> Boishakh (ends May 15)
                { gM: 4, gD: 16, bM: 1 },  // May 16 -> Jyeshtha (ends Jun 15)
                { gM: 5, gD: 16, bM: 2 },  // Jun 16 -> Ashadha (ends Jul 17)
                { gM: 6, gD: 18, bM: 3 },  // Jul 18 -> Srabon (ends Aug 18)
                { gM: 7, gD: 19, bM: 4 },  // Aug 19 -> Bhadra (ends Sep 18)
                { gM: 8, gD: 19, bM: 5 },  // Sep 19 -> Ashwin (ends Oct 18, so Oct 7 is 19 Ashwin)
                { gM: 9, gD: 19, bM: 6 },  // Oct 19 -> Kartik (ends Nov 17, so Oct 24 is 6 Kartik)
                { gM: 10, gD: 18, bM: 7 }, // Nov 18 -> Agrahayan (ends Dec 16)
                { gM: 11, gD: 17, bM: 8 }, // Dec 17 -> Poush (ends Jan 15)
            ];

            let pjM = 0;
            let pjStart = null;
            let pjYear = year - 593;

            for (let i = 0; i < panjikaIngress.length; i++) {
                const item = panjikaIngress[i];
                const ingDate = new Date(Date.UTC(year, item.gM, item.gD));
                if (curUtc >= ingDate) {
                    pjM = item.bM;
                    pjStart = ingDate;
                }
            }

            if (!pjStart) {
                pjM = 8; // Poush
                pjStart = new Date(Date.UTC(year - 1, 11, 17));
                pjYear = year - 594;
            } else if (pjM >= 9) {
                pjYear = year - 594;
            }

            const pjDiff = Math.floor((curUtc.getTime() - pjStart.getTime()) / 86400000);
            const pjDay = pjDiff + 1;
            const panjika = {
                day: convertToBnNum(pjDay),
                dayNum: pjDay,
                month: bn_months[pjM],
                monthIndex: pjM,
                year: convertToBnNum(pjYear),
                full: `${convertToBnNum(pjDay)} ${bn_months[pjM]} ${convertToBnNum(pjYear)}`
            };

            return { academy, panjika };
        }

        // Global map to keep track of wedding dates for current view
        let weddingDatesMap = {};
        let currentWeddingDatesList = [];
        let calendarInstance = null;

        // Auto calculate & preview Bengali dates when date input changes in modal
        function updateModalBanglaDates(dateStr) {
            if (!dateStr) return;
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                const bothBn = getBothBanglaDates(d);
                
                const previewBa = document.getElementById('modal-preview-academy');
                const previewPj = document.getElementById('modal-preview-panjika');
                if (previewBa) previewBa.innerText = bothBn.academy.full;
                if (previewPj) previewPj.innerText = bothBn.panjika.full;

                // Pre-fill input with Bisuddhasiddhanta date by default (used for wedding dates)
                const inputBn = document.getElementById('wd-input-bn-date');
                if (inputBn) {
                    inputBn.value = `${bothBn.panjika.full} (বিশুদ্ধ সিদ্ধান্ত)`;
                }
            }
        }

        document.getElementById('wd-input-date').addEventListener('change', function() {
            updateModalBanglaDates(this.value);
        });

        // Quick buttons to switch between Bisuddhasiddhanta and Academy date in modal
        const btnUsePanjika = document.getElementById('btn-use-panjika');
        if (btnUsePanjika) {
            btnUsePanjika.addEventListener('click', function(e) {
                e.preventDefault();
                const dVal = document.getElementById('wd-input-date').value;
                if (dVal) {
                    const parts = dVal.split('-');
                    const d = new Date(parts[0], parts[1] - 1, parts[2]);
                    const bothBn = getBothBanglaDates(d);
                    document.getElementById('wd-input-bn-date').value = `${bothBn.panjika.full} (বিশুদ্ধ সিদ্ধান্ত)`;
                }
            });
        }

        const btnUseAcademy = document.getElementById('btn-use-academy');
        if (btnUseAcademy) {
            btnUseAcademy.addEventListener('click', function(e) {
                e.preventDefault();
                const dVal = document.getElementById('wd-input-date').value;
                if (dVal) {
                    const parts = dVal.split('-');
                    const d = new Date(parts[0], parts[1] - 1, parts[2]);
                    const bothBn = getBothBanglaDates(d);
                    document.getElementById('wd-input-bn-date').value = `${bothBn.academy.full} (একাডেমি)`;
                }
            });
        }

        // Fetch and refresh wedding dates for active calendar view
        function loadWeddingDates(startStr, endStr) {
            const listEl = document.getElementById('wedding-dates-sidebar-list');
            const summaryFooter = document.getElementById('sidebar-count-text');

            fetch(`{{ route('calendar.wedding-dates') }}?start=${startStr}&end=${endStr}`)
                .then(res => res.json())
                .then(data => {
                    currentWeddingDatesList = data;
                    weddingDatesMap = {};
                    data.forEach(item => {
                        weddingDatesMap[item.date] = item;
                    });

                    renderSidebarWeddingDates(data);
                    highlightCalendarWeddingDates();
                })
                .catch(err => {
                    console.error('Failed to load wedding dates', err);
                    if (listEl) {
                        listEl.innerHTML = `<div class="text-danger small text-center py-3">তথ্য লোড করতে সমস্যা হয়েছে।</div>`;
                    }
                });
        }

        // Render wedding dates in the sidebar
        function renderSidebarWeddingDates(dates) {
            const listEl = document.getElementById('wedding-dates-sidebar-list');
            const countEl = document.getElementById('sidebar-count-text');

            if (!dates || dates.length === 0) {
                listEl.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <div class="mb-2" style="font-size: 1.8rem;">💍</div>
                        <div class="fw-bold">এই মাসে কোনো শুভ লগ্ন যুক্ত নেই</div>
                        <div class="small mt-1">পঞ্জিকা অনুযায়ী নতুন লগ্ন যুক্ত করতে নিচের বাটনে চাপ দিন।</div>
                        <button class="btn btn-sm btn-outline-warning mt-3" data-bs-toggle="modal" data-coreui-toggle="modal" data-bs-target="#addWeddingDateModal" data-coreui-target="#addWeddingDateModal">
                            + শুভ বিবাহের তারিখ যোগ করুন
                        </button>
                    </div>
                `;
                if (countEl) countEl.innerText = `০টি লগ্ন`;
                return;
            }

            let availableCount = dates.filter(d => d.availability.status === 'available').length;
            if (countEl) countEl.innerText = `মোট ${dates.length}টি লগ্ন (${availableCount}টি খালি)`;

            let html = '';
            dates.forEach(wd => {
                const avail = wd.availability;
                let statusBadgeClass = 'bg-success text-white';
                let cardClass = '';
                if (avail.status === 'booked') {
                    statusBadgeClass = 'bg-danger text-white';
                    cardClass = 'is-booked';
                } else if (avail.status === 'partial') {
                    statusBadgeClass = 'bg-warning text-dark';
                    cardClass = 'is-partial';
                }

                html += `
                    <div class="wedding-date-card ${cardClass}" onclick="showWeddingDateDetail('${wd.date}')">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <span class="badge ${statusBadgeClass} px-2 py-1" style="font-size: 0.75rem;">
                                    ${avail.label}
                                </span>
                                ${wd.tithi ? `<span class="badge bg-amber-lt text-amber border border-amber ms-1" style="font-size: 0.72rem;">${wd.tithi}</span>` : ''}
                            </div>
                            <span class="text-muted small fw-bold">${wd.day_name}</span>
                        </div>
                        <div class="d-flex align-items-center mt-2">
                            <div class="text-center bg-white border rounded px-2 py-1 shadow-sm me-2" style="min-width: 46px;">
                                <div class="fw-bolder fs-3 lh-1 text-dark">${wd.day}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">${wd.month_name}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark lh-sm">${wd.title}</div>
                                <div class="small mt-1">
                                    <div class="fw-bold" style="color: #92400e;">
                                        <span class="badge py-0 px-1 me-1" style="background:#f59e0b; color:white; font-size:0.65rem;" title="বিশুদ্ধ সিদ্ধান্ত (প্রোকেরালা)">বি</span> ${wd.bengali_date_panjika || wd.bengali_date || ''}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        <span class="badge bg-secondary text-white py-0 px-1 me-1" style="font-size:0.65rem;" title="বাংলা একাডেমি (সরকারি)">বা</span> ${wd.bengali_date_academy || ''}
                                    </div>
                                </div>
                            </div>
                            <div class="ms-1">
                                ${avail.status !== 'booked' ? `
                                    <a href="${wd.create_booking_url}" class="btn btn-sm btn-outline-success px-2 py-1" onclick="event.stopPropagation();" title="বুকিং তৈরি করুন">
                                        বুকিং
                                    </a>
                                ` : `
                                    <span class="badge bg-secondary-lt text-muted px-2 py-1">ফুল</span>
                                `}
                            </div>
                        </div>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        // Apply highlights & badges to calendar day cells
        function highlightCalendarWeddingDates() {
            document.querySelectorAll('.fc-daygrid-day').forEach(cell => {
                const dateStr = cell.getAttribute('data-date');
                if (!dateStr) return;

                // Remove previous wedding badge/classes if any
                cell.classList.remove('shuvo-bibaho-cell');
                const existingBadge = cell.querySelector('.wedding-day-badge');
                if (existingBadge) existingBadge.remove();

                const wd = weddingDatesMap[dateStr];
                if (wd) {
                    cell.classList.add('shuvo-bibaho-cell');
                    const frame = cell.querySelector('.fc-daygrid-day-frame');
                    if (frame) {
                        const badge = document.createElement('div');
                        badge.className = 'wedding-day-badge';
                        
                        let dotColor = '#10b981'; // green
                        if (wd.availability.status === 'booked') dotColor = '#ef4444';
                        else if (wd.availability.status === 'partial') dotColor = '#f59e0b';

                        badge.innerHTML = `<span class="wedding-avail-dot" style="background-color: ${dotColor};"></span> 💍 শুভ বিবাহ ${wd.tithi ? `(${wd.tithi})` : ''}`;
                        badge.title = `${wd.title} - ${wd.tithi || ''} (${wd.availability.label})`;
                        badge.addEventListener('click', function(e) {
                            e.stopPropagation();
                            showWeddingDateDetail(dateStr);
                        });

                        // Insert after day top
                        const dayTop = frame.querySelector('.fc-daygrid-day-top');
                        if (dayTop) {
                            dayTop.after(badge);
                        } else {
                            frame.prepend(badge);
                        }
                    }
                }
            });
        }

        // Show Wedding Date Detail in Modal
        function showWeddingDateDetail(dateStr) {
            const wd = weddingDatesMap[dateStr];
            if (!wd) return;

            document.getElementById('wd-detail-title').innerText = `${wd.title} (${wd.formatted_date})`;
            
            const avail = wd.availability;
            let statusBadge = `<span class="badge bg-success text-white">${avail.label}</span>`;
            if (avail.status === 'booked') statusBadge = `<span class="badge bg-danger text-white">${avail.label}</span>`;
            else if (avail.status === 'partial') statusBadge = `<span class="badge bg-warning text-dark">${avail.label}</span>`;

            let contentHtml = `
                <div class="row g-3">
                    <div class="col-12 text-center p-3 rounded" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fcd34d;">
                        <div class="fs-1">💍</div>
                        <h3 class="fw-bold text-dark mb-1">${wd.title}</h3>
                        <div class="text-muted fw-bold">${wd.formatted_date} (${wd.day_name})</div>
                        <div class="row g-2 mt-2">
                            <div class="col-6 p-2 rounded border text-start" style="background: rgba(254, 243, 199, 0.4); border-color: #fcd34d !important;">
                                <div class="small fw-bold" style="color: #92400e;">
                                    <span class="badge py-0 px-1 me-1" style="background: #f59e0b; color: white; font-size: 0.65rem;">বি</span> বিশুদ্ধ সিদ্ধান্ত (প্রোকেরালা)
                                </div>
                                <div class="fw-bold mt-1" style="color: #b45309; font-size: 0.95rem;">${wd.bengali_date_panjika || wd.bengali_date || '—'}</div>
                            </div>
                            <div class="col-6 bg-white p-2 rounded border text-start">
                                <div class="small text-muted fw-bold">
                                    <span class="badge bg-secondary text-white py-0 px-1 me-1" style="font-size: 0.65rem;">বা</span> বাংলা একাডেমি (সরকারি)
                                </div>
                                <div class="fw-bold text-dark mt-1" style="font-size: 0.95rem;">${wd.bengali_date_academy || '—'}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted">শুভ তিথি / লগ্ন</div>
                        <div class="fw-bold">${wd.tithi || 'সাধারণ লগ্ন'}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="small text-muted">হলের অবস্থা</div>
                        <div class="mt-1">${statusBadge}</div>
                    </div>
                    ${wd.notes ? `
                    <div class="col-12">
                        <div class="small text-muted">নোট</div>
                        <div class="p-2 bg-light rounded text-dark">${wd.notes}</div>
                    </div>
                    ` : ''}
                </div>
            `;

            document.getElementById('wdDetailContent').innerHTML = contentHtml;
            document.getElementById('wdBookNowBtn').href = wd.create_booking_url;

            // Setup delete action
            const deleteBtn = document.getElementById('wdDeleteBtn');
            deleteBtn.onclick = function() {
                if (confirm('আপনি কি নিশ্চিত যে এই শুভ বিবাহের তারিখটি মুছে ফেলতে চান?')) {
                    fetch(`{{ url('calendar/wedding-dates') }}/${wd.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(result => {
                        safelyHideModal('weddingDateDetailModal');
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'মুছে ফেলা হয়েছে!',
                                text: 'শুভ বিবাহের তারিখ সফলভাবে মুছে ফেলা হয়েছে।',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }

                        // Reload dates
                        const currentStart = calendarInstance.view.activeStart.toISOString().split('T')[0];
                        const currentEnd = calendarInstance.view.activeEnd.toISOString().split('T')[0];
                        loadWeddingDates(currentStart, currentEnd);
                    })
                    .catch(err => alert('মুছে ফেলতে সমস্যা হয়েছে।'));
                }
            };

            const detailModalEl = document.getElementById('weddingDateDetailModal');
            const ModalClass = window.coreui?.Modal || window.bootstrap?.Modal;
            if (ModalClass) {
                ModalClass.getOrCreateInstance(detailModalEl).show();
            }
        }

        // Safe modal close helper to prevent stuck backdrops in CoreUI and Bootstrap
        function safelyHideModal(modalId) {
            const modalEl = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
            if (!modalEl) return;

            // 1. Try CoreUI / Bootstrap API
            try {
                const ModalClass = window.coreui?.Modal || window.bootstrap?.Modal;
                if (ModalClass) {
                    const inst = ModalClass.getInstance(modalEl) || ModalClass.getOrCreateInstance(modalEl);
                    if (inst) {
                        inst.hide();
                    }
                }
            } catch (err) {
                console.warn('Modal hide error:', err);
            }

            // 2. Click dismiss button as fallback
            const dismissBtn = modalEl.querySelector('[data-coreui-dismiss="modal"], [data-bs-dismiss="modal"]');
            if (dismissBtn) {
                dismissBtn.click();
            }

            // 3. Fallback inline hide
            setTimeout(() => {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
            }, 100);

            // 4. Force remove all stuck backdrops & restore body scroll
            const forceCleanup = () => {
                if (!document.querySelector('.modal.show')) {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }
            };

            setTimeout(forceCleanup, 150);
            setTimeout(forceCleanup, 350);
            setTimeout(forceCleanup, 500);
        }

        // Submit form for adding wedding date
        document.getElementById('addWeddingDateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('submitWdBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'সংরক্ষণ হচ্ছে...';

            const formData = new FormData(this);

            fetch(`{{ route('calendar.wedding-dates.store') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'সংরক্ষণ করুন';
                if (data.success) {
                    safelyHideModal('addWeddingDateModal');
                    document.getElementById('addWeddingDateForm').reset();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'সফল!',
                            text: 'শুভ বিবাহের তারিখ সফলভাবে যুক্ত হয়েছে।',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }

                    // Reload dates in calendar and sidebar
                    const currentStart = calendarInstance.view.activeStart.toISOString().split('T')[0];
                    const currentEnd = calendarInstance.view.activeEnd.toISOString().split('T')[0];
                    loadWeddingDates(currentStart, currentEnd);
                } else {
                    alert('তারিখ যোগ করতে সমস্যা হয়েছে।');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'সংরক্ষণ করুন';
                alert('ত্রুটি দেখা দিয়েছে। অনুগ্রহ করে ইনপুট চেক করুন।');
            });
        });

        // Initialize Calendar
        document.addEventListener('DOMContentLoaded', function() {
            const todayBn = getBothBanglaDates(new Date());
            const displayEl = document.getElementById('bn-today-display');
            if (displayEl) {
                displayEl.innerHTML = `<span class="badge bg-secondary-lt me-1">বা</span> ${todayBn.academy.full} &nbsp;|&nbsp; <span class="badge bg-warning-lt text-dark me-1" style="color: #92400e;">বি</span> <strong style="color: #b45309;" title="বিশুদ্ধ সিদ্ধান্ত (প্রোকেরালা)">${todayBn.panjika.full}</strong>`;
            }

            // Pre-fill Bengali date on input
            const initialDateInput = document.getElementById('wd-input-date').value;
            if (initialDateInput) {
                updateModalBanglaDates(initialDateInput);
            }

            var calendarEl = document.getElementById('calendar');
            calendarInstance = new FullCalendar.Calendar(calendarEl, {
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
                    
                    const bnStart = getBothBanglaDates(start);
                    const bnEnd = getBothBanglaDates(end);
                    
                    let bnTitle = "";
                    if (bnStart.academy.month === bnEnd.academy.month) {
                        bnTitle = `${bnStart.academy.month} (বিশুদ্ধ সিদ্ধান্ত: ${bnStart.panjika.month}) ${bnStart.academy.year}`;
                    } else {
                        bnTitle = `${bnStart.academy.month} - ${bnEnd.academy.month} (বিশুদ্ধ সিদ্ধান্ত: ${bnStart.panjika.month})`;
                    }
                    
                    const titleEl = document.querySelector('.fc-toolbar-title');
                    if (titleEl) {
                        setTimeout(() => {
                            titleEl.innerText = `${info.view.title} / ${bnTitle}`;
                        }, 1);
                    }

                    // Update sidebar title with current month name
                    const sidebarMonthLabel = document.getElementById('sidebar-month-title');
                    if (sidebarMonthLabel) {
                        sidebarMonthLabel.innerText = `${info.view.title} (${bnTitle})`;
                    }

                    // Fetch wedding dates for the active date window
                    const startStr = info.view.activeStart.toISOString().split('T')[0];
                    const endStr = info.view.activeEnd.toISOString().split('T')[0];
                    loadWeddingDates(startStr, endStr);
                },
                dayCellDidMount: function(info) {
                    const bothBn = getBothBanglaDates(info.date);
                    const box = document.createElement('div');
                    box.className = 'bn-date-box';

                    const baText = bothBn.academy.dayNum === 1 ? `বা: ${bothBn.academy.day} ${bothBn.academy.month}` : `বা: ${bothBn.academy.day}`;
                    const pjText = bothBn.panjika.dayNum === 1 ? `বি: ${bothBn.panjika.day} ${bothBn.panjika.month}` : `বি: ${bothBn.panjika.day}`;

                    box.innerHTML = `
                        <span class="bn-date-ba" title="বাংলা একাডেমি (সরকারি): ${bothBn.academy.full}">${baText}</span>
                        <span class="bn-date-pj" title="বিশুদ্ধ সিদ্ধান্ত পঞ্জিকা (বিবাহ লগ্ন - প্রোকেরালা): ${bothBn.panjika.full}">${pjText}</span>
                    `;
                    info.el.querySelector('.fc-daygrid-day-frame').appendChild(box);

                    // If wedding dates already loaded for this date, highlight immediately
                    const dateStr = info.date.toISOString().split('T')[0];
                    if (weddingDatesMap[dateStr]) {
                        highlightCalendarWeddingDates();
                    }
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
                                <div class="small text-muted">স্লট</div>
                                <div><strong>${props.venue}</strong></div>
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
                    
                    const eventModalEl = document.getElementById('eventModal');
                    const EventModalClass = window.coreui?.Modal || window.bootstrap?.Modal;
                    if (EventModalClass) {
                        EventModalClass.getOrCreateInstance(eventModalEl).show();
                    }
                }
            });
            calendarInstance.render();
        });
    </script>
    @endpush
</x-tabler-layout>
