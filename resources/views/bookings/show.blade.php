<x-tabler-layout :title="'বুকিং বিস্তারিত - #' . $booking->id">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">বুকিং বিস্তারিত - #{{ $booking->id }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('bookings.invoice', $booking) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                            ইনভয়েস ডাউনলোড
                        </a>
                        <a href="{{ route('bookings.index') }}" class="btn btn-white">
                            তালিকায় ফিরে যান
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-cards">
                <!-- Customer & Financial Summary -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title">গ্রাহকের তথ্য</h3></div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>নাম:</strong> {{ $booking->customer->name }}
                            </div>
                            <div class="mb-2">
                                <strong>মোবাইল:</strong> {{ $booking->customer->phone }}
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title">পেমেন্ট সারসংক্ষেপ</h3></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>মোট বিল:</span>
                                <strong>৳ {{ number_format($booking->total_amount, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>মোট জমা:</span>
                                <strong>৳ {{ number_format($booking->advance_amount, 2) }}</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between {{ $booking->total_amount - $booking->advance_amount > 0 ? 'text-danger' : 'text-success' }}">
                                <span class="h4">বকেয়া:</span>
                                <strong class="h4">৳ {{ number_format($booking->total_amount - $booking->advance_amount, 2) }}</strong>
                            </div>

                            @if($booking->total_server_cost > 0)
                                <div class="mt-3 pt-2 border-top small">
                                    <div class="d-flex justify-content-between text-muted mb-1">
                                        <span>পরিবেশন খরচ (সার্ভারদের দেয়):</span>
                                        <span>৳ {{ number_format($booking->total_server_cost, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-primary font-weight-bold">
                                        <span>হলের প্রাক্কলিত নিট আয়:</span>
                                        <span>৳ {{ number_format($booking->hall_income_amount, 2) }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($booking->total_amount - $booking->advance_amount > 0)
                                <div class="mt-4 pt-3 border-top">
                                    <h4 class="mb-3">পেমেন্ট যোগ করুন</h4>
                                    <form action="{{ route('bookings.add-payment', $booking) }}" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label small">টাকার পরিমাণ</label>
                                            <input type="number" name="amount" class="form-control" max="{{ $booking->total_amount - $booking->advance_amount }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">তারিখ</label>
                                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small">বিবরণ (ঐচ্ছিক)</label>
                                            <input type="text" name="description" class="form-control" placeholder="যেমন: ক্যাশ / চেক">
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">পেমেন্ট জমা দিন</button>
                                    </form>
                                </div>
                            @else
                                <div class="mt-4 p-2 bg-success-lt text-center rounded">
                                    <strong class="text-success small">সম্পূর্ণ পেমেন্ট পরিশোধিত</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Server Payout Card -->
                    @if($booking->all_server_cost > 0)
                        <div class="card mb-3 border-orange">
                            <div class="card-header bg-orange-lt py-2">
                                <h3 class="card-title text-orange font-weight-bold mb-0">🧑‍🍳 পরিবেশনকারী বিল ব্যবস্থাপনা</h3>
                            </div>
                            <div class="card-body">
                                @if($booking->total_server_cost > 0)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">বিলে অন্তর্ভুক্ত পরিবেশন খরচ:</span>
                                        <span class="h3 mb-0 font-weight-bold text-dark">৳ {{ number_format($booking->total_server_cost, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">পেমেন্ট স্ট্যাটাস:</span>
                                        @if($booking->server_payout_status === 'paid')
                                            <span class="badge bg-success-lt text-success font-weight-bold px-2 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                পরিশোধিত (Paid)
                                            </span>
                                        @else
                                            <span class="badge bg-warning-lt text-warning font-weight-bold px-2 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                                                অপরিশোধিত (Pending)
                                            </span>
                                        @endif
                                    </div>

                                    @if($booking->server_payout_status === 'paid')
                                        <div class="alert alert-success py-2 px-3 mb-0 small">
                                            <div><strong>পরিশোধের তারিখ:</strong> {{ \Carbon\Carbon::parse($booking->server_payout_date)->format('d M Y, h:i A') }}</div>
                                            <div class="text-muted mt-1">অ্যাকাউন্টিংয়ে ৳ {{ number_format($booking->total_server_cost, 2) }} খরচ (Expense) হিসেবে অন্তর্ভুক্ত করা হয়েছে।</div>
                                        </div>
                                    @else
                                        <div class="small text-muted mb-3">
                                            পরিবেশনকারীদের বিল পরিশোধ নিশ্চিত করলে এটি স্বয়ংক্রিয়ভাবে অ্যাকাউন্টিংয়ে <strong>ব্যয় (Expense)</strong> হিসেবে যুক্ত হবে।
                                        </div>
                                        <form action="{{ route('bookings.pay-servers', $booking) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে পরিবেশনকারীদের ৳ {{ number_format($booking->total_server_cost, 2) }} পরিশোধ করতে চান? এটি অ্যাকাউন্টিং খরচ হিসেবে রেকর্ড হবে।');">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100 font-weight-bold text-dark mb-2">
                                                🧑‍🍳 পরিবেশনকারীদের বিল পরিশোধ করুন
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if($booking->excluded_server_cost > 0)
                                    <div class="p-2 bg-light rounded text-muted small mt-2">
                                        <strong>* বিলে অন্তর্ভুক্ত নয়:</strong> ৳ {{ number_format($booking->excluded_server_cost, 2) }} (গ্রাহক কর্তৃক সরাসরি পরিবেশনকারীদের প্রদেয়)।
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="card mt-3">
                        <div class="card-header"><h3 class="card-title">পেমেন্ট হিস্ট্রি</h3></div>
                        <div class="card-body p-0">
                            <table class="table table-vcenter card-table small mb-0">
                                <thead>
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>পরিমাণ</th>
                                        <th>বিবরণ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->incomes as $income)
                                        <tr>
                                            <td>{{ $income->date }}</td>
                                            <td><strong>৳ {{ number_format($income->amount, 2) }}</strong></td>
                                            <td class="text-muted">{{ $income->description ?? 'বুকিং পেমেন্ট' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">কোনো পেমেন্ট রেকর্ড নেই।</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Booking Items Breakdown -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">ইভেন্ট এবং হলের বিস্তারিত breakdown</h3></div>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-mobile-md card-table">
                                <thead>
                                    <tr>
                                        <th>তারিখ ও ইভেন্ট ধরণ</th>
                                        <th>সরঞ্জাম ও স্লট</th>
                                        <th>অতিরিক্ত সুবিধাসমূহ</th>
                                        <th class="text-end">উপ-মোট (৳)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($booking->items as $item)
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold">{{ $item->event_date }}</div>
                                                <div class="badge bg-teal-lt text-teal mt-1">{{ $item->event_type ?? 'ইভেন্ট' }}</div>
                                            </td>
                                            <td>
                                                <div>স্লট: 
                                                    @if($item->slot == 'day' || $item->slot == 'morning') দিন (Day)
                                                    @elseif($item->slot == 'night' || $item->slot == 'evening') রাত (Night)
                                                    @else সারাদিন @endif
                                                </div>
                                                <div class="small text-muted">
                                                    মেহমান: {{ $item->guest_count }}, 
                                                    টেবিল: {{ $item->table_count }},
                                                    পরিবেশনকারী: {{ $item->server_count }} (@ ৳ {{ number_format($item->server_rate, 2) }})
                                                    @if(!$item->is_server_included && $item->server_count > 0)
                                                        <span class="text-danger small font-weight-bold">*(গ্রাহক সরাসরি প্রদেয়)</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled mb-0 small">
                                                    @if($item->is_ac) <li>এসি: ৳ {{ number_format($item->ac_price, 2) }}</li> @endif
                                                    @if($item->extra_sound) <li>সাউন্ড: ৳ {{ number_format($item->sound_price, 2) }}</li> @endif
                                                    @if($item->extra_generator) <li>জেনারেটর: ৳ {{ number_format($item->generator_price, 2) }}</li> @endif
                                                    @if($item->extra_decoration) <li>ডেকোরেশন: ৳ {{ number_format($item->decoration_price, 2) }}</li> @endif
                                                </ul>
                                            </td>
                                            <td class="text-end">
                                                <div class="small text-muted">হল ভাড়া: ৳ {{ number_format($item->base_price, 2) }}</div>
                                                @if($item->server_count > 0)
                                                    <div class="small text-muted">
                                                        পরিবেশন খরচ: ৳ {{ number_format($item->server_count * $item->server_rate, 2) }}
                                                        @if($item->is_server_included)
                                                            <span class="badge bg-success-lt" style="font-size: 0.65rem;">বিলে অন্তর্ভুক্ত</span>
                                                        @else
                                                            <span class="badge bg-danger-lt" style="font-size: 0.65rem;">বিলে অন্তর্ভুক্ত নয়</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <strong>৳ {{ number_format($item->sub_total, 2) }}</strong>
                                            </td>
                                        </tr>
                                            <td colspan="4" class="bg-light p-3 border-top">
                                                <div class="row g-3">
                                                    <!-- Assets Assignment -->
                                                    <div class="col-md-12">
                                                        <div class="card card-sm">
                                                            <div class="card-header bg-orange-lt py-1">
                                                                <h4 class="card-title small">মালামাল বরাদ্দ ও স্টক ট্র্যাকিং</h4>
                                                            </div>
                                                            <div class="card-body py-2">
                                                                <form action="{{ route('bookings.assign-asset', $item) }}" method="POST" class="row g-2 align-items-end mb-2">
                                                                    @csrf
                                                                    <div class="col">
                                                                        <label class="form-label small mb-1">মালামাল</label>
                                                                        <select name="asset_id" class="form-select form-select-sm" required>
                                                                            <option value="">নির্বাচন করুন</option>
                                                                            @foreach(\App\Models\Asset::all() as $asset)
                                                                                <option value="{{ $asset->id }}">{{ $asset->name }} (স্টক: {{ $asset->available_stock }})</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col">
                                                                        <label class="form-label small mb-1">পরিমাণ</label>
                                                                        <input type="number" name="quantity" class="form-control form-control-sm" required>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <button type="submit" class="btn btn-sm btn-orange text-white">বরাদ্দ দিন</button>
                                                                    </div>
                                                                </form>

                                                                <div class="table-responsive">
                                                                    <table class="table table-vcenter table-sm card-table">
                                                                        <thead>
                                                                            <tr class="small">
                                                                                 <th>নাম</th>
                                                                                <th>আউট</th>
                                                                                <th>ইন</th>
                                                                                <th>ক্ষতি</th>
                                                                                <th>অ্যাকশন</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($item->assetAssignments as $assign)
                                                                                <tr class="small">
                                                                                    <td>{{ $assign->asset->name }}</td>
                                                                                    <td>{{ $assign->quantity_out }}</td>
                                                                                    <td>{{ $assign->quantity_in ?: '-' }}</td>
                                                                                    <td class="text-danger">{{ $assign->damaged_quantity ?: '-' }}</td>
                                                                                    <td class="text-end">
                                                                                        @if(!$assign->quantity_in && !$assign->damaged_quantity)
                                                                                            <button class="btn btn-xs btn-outline-success py-0 px-1" data-bs-toggle="modal" data-bs-target="#returnModal{{ $assign->id }}">ফেরত নিন</button>
                                                                                            
                                                                                            <!-- Inline Modal for Return -->
                                                                                            <div class="modal modal-blur fade" id="returnModal{{ $assign->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                                                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                                                                    <form action="{{ route('bookings.return-asset', $assign) }}" method="POST" class="modal-content">
                                                                                                        @csrf
                                                                                                        <div class="modal-header"><h5 class="modal-title">মালামাল ফেরত নিন</h5></div>
                                                                                                        <div class="modal-body">
                                                                                                            <div class="mb-2">
                                                                                                                <label class="form-label small">ভালো অবস্থায় (ইন)</label>
                                                                                                                <input type="number" name="quantity_in" class="form-control" max="{{ $assign->quantity_out }}" required>
                                                                                                            </div>
                                                                                                            <div class="mb-2">
                                                                                                                <label class="form-label small">ক্ষতিগ্রস্থ/হারানো</label>
                                                                                                                <input type="number" name="damaged_quantity" class="form-control" max="{{ $assign->quantity_out }}" value="0" required>
                                                                                                            </div>
                                                                                                            <div class="mb-0">
                                                                                                                <label class="form-label small">নোট</label>
                                                                                                                <input type="text" name="notes" class="form-control">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="modal-footer">
                                                                                                            <button type="submit" class="btn btn-success w-100">ফেরত নিশ্চিত করুন</button>
                                                                                                        </div>
                                                                                                    </form>
                                                                                                </div>
                                                                                            </div>
                                                                                        @else
                                                                                            <span class="text-success small"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg></span>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($booking->notes)
                            <div class="card-footer">
                                <strong>নোট:</strong>
                                <p class="text-muted small mt-1">{{ $booking->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
