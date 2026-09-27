<x-tabler-layout :title="'বুকিং এডিট - #' . $booking->id">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">বুকিং এডিট - #{{ $booking->id }}</h2>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body" x-data="bookingForm()">
        <div class="container-fluid">
            <form action="{{ route('bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row row-cards">
                    <!-- Customer Section -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">গ্রাহকের তথ্য</h3></div>
                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label required">গ্রাহকের নাম</label>
                                            <input type="text" name="customer_name" class="form-control" value="{{ $booking->customer->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label required">মোবাইল নম্বর</label>
                                            <input type="text" name="customer_phone" class="form-control" value="{{ $booking->customer->phone }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">ঠিকানা</label>
                                            <input type="text" name="customer_address" class="form-control" value="{{ $booking->customer->address }}" placeholder="বাড়ি/অফিস ঠিকানা">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">বুকিং দিন এবং হলের বিস্তারিত</h3>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="addItem()">+ আরেকটি দিন যোগ করুন</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter table-mobile-md card-table">
                                    <thead>
                                        <tr>
                                            <th>তারিখ ও হল</th>
                                            <th>অতিথি ও সরঞ্জাম</th>
                                            <th>অতিরিক্ত সুবিধা (এসি, সাউন্ড ইত্যাদি)</th>
                                            <th>মূল্য (৳)</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td>
                                                    <div class="mb-2">
                                                        <label class="form-label small">বুকিং তারিখ</label>
                                                        <input type="date" :name="'items['+index+'][event_date]'" class="form-control" x-model="item.event_date" @change="checkAllAvailability()" @input="checkAllAvailability()" required>
                                                    </div>
                                                    <input type="hidden" :name="'items['+index+'][hall_id]'" :value="item.hall_id">
                                                    <div class="mb-2">
                                                        <label class="form-label small">স্লট</label>
                                                        <select :name="'items['+index+'][slot]'" class="form-control" x-model="item.slot" @change="checkAllAvailability()" required>
                                                            <option value="day">Day (দিন)</option>
                                                            <option value="night">Night (রাত)</option>
                                                        </select>
                                                    </div>
                                                    <div x-show="item.conflict_error" class="text-danger small mb-2 fw-bold" style="display: none;">
                                                        ⚠️ <span x-text="item.conflict_error"></span>
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label small">ইভেন্ট টাইপ</label>
                                                        <select :name="'items['+index+'][event_type]'" class="form-control" x-model="item.event_type" required>
                                                            <template x-if="item.event_type && !['বিয়ে (Wedding Reception)', 'গায়ে হলুদ (Gaye Holud)', 'মেহেদি নাইট (Mehendi Night)', 'আকদ / এনগেজমেন্ট (Akht / Engagement)', 'বৌভাত / ওয়ালিমা (Bou Bhat / Walima)', 'জন্মদিন (Birthday Party)', 'আকিকা (Aqiqa)', 'বিবাহবার্ষিকী (Anniversary)', 'পারিবারিক পুনর্মিলনী (Family Reunion)', 'কর্পোরেট এজিএম (AGM / Annual General Meeting)', 'প্রডাক্ট লঞ্চ (Product Launch)', 'কনফারেন্স ও সেমিনার (Conferences & Seminars)', 'কর্পোরেট ডিনার ও গ্যালা নাইট (Corporate Dinner & Gala Night)', 'মেলা ও প্রদর্শনী (Trade Fairs & Exhibitions)', 'সমাবর্তন ও র্যাগ ডে (Graduation & Rag Day)', 'অ্যালামনাই রিইউনিয়ন (Alumni Reunion)', 'সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট (Cultural Shows & Concerts)', 'ইফতার মাহফিল (Iftar Mahfil)', 'দোয়া ও মিলাদ মাহফিল (Prayer Gatherings)', 'প্রেস কনফারেন্স ও মিটিং (Press Conferences)', 'অন্যান্য (Other)'].includes(item.event_type)">
                                                                <option :value="item.event_type" x-text="item.event_type"></option>
                                                            </template>
                                                            <option value="বিয়ে (Wedding Reception)">১. বিয়ে (Wedding Reception)</option>
                                                            <option value="গায়ে হলুদ (Gaye Holud)">২. গায়ে হলুদ (Gaye Holud)</option>
                                                            <option value="মেহেদি নাইট (Mehendi Night)">৩. মেহেদি নাইট (Mehendi Night)</option>
                                                            <option value="আকদ / এনগেজমেন্ট (Akht / Engagement)">৪. আকদ / এনগেজমেন্ট (Akht / Engagement)</option>
                                                            <option value="বৌভাত / ওয়ালিমা (Bou Bhat / Walima)">৫. বৌভাত / ওয়ালিমা (Bou Bhat / Walima)</option>
                                                            <option value="জন্মদিন (Birthday Party)">৬. জন্মদিন (Birthday Party)</option>
                                                            <option value="আকিকা (Aqiqa)">৭. আকিকা (Aqiqa)</option>
                                                            <option value="বিবাহবার্ষিকী (Anniversary)">৮. বিবাহবার্ষিকী (Anniversary)</option>
                                                            <option value="পারিবারিক পুনর্মিলনী (Family Reunion)">৯. পারিবারিক পুনর্মিলনী (Family Reunion)</option>
                                                            <option value="কর্পোরেট এজিএম (AGM / Annual General Meeting)">১০. কর্পোরেট এজিএম (AGM / Annual General Meeting)</option>
                                                            <option value="প্রডাক্ট লঞ্চ (Product Launch)">১১. প্রডাক্ট লঞ্চ (Product Launch)</option>
                                                            <option value="কনফারেন্স ও সেমিনার (Conferences & Seminars)">১২. কনফারেন্স ও সেমিনার (Conferences & Seminars)</option>
                                                            <option value="কর্পোরেট ডিনার ও গ্যালা নাইট (Corporate Dinner & Gala Night)">১৩. কর্পোরেট ডিনার ও গ্যালা নাইট (Corporate Dinner & Gala Night)</option>
                                                            <option value="মেলা ও প্রদর্শনী (Trade Fairs & Exhibitions)">১৪. মেলা ও প্রদর্শনী (Trade Fairs & Exhibitions)</option>
                                                            <option value="সমাবর্তন ও র্যাগ ডে (Graduation & Rag Day)">১৫. সমাবর্তন ও র্যাগ ডে (Graduation & Rag Day)</option>
                                                            <option value="অ্যালামনাই রিইউনিয়ন (Alumni Reunion)">১৬. অ্যালামনাই রিইউনিয়ন (Alumni Reunion)</option>
                                                            <option value="সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট (Cultural Shows & Concerts)">১৭. সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট (Cultural Shows & Concerts)</option>
                                                            <option value="ইফতার মাহফিল (Iftar Mahfil)">১৮. ইফতার মাহফিল (Iftar Mahfil)</option>
                                                            <option value="দোয়া ও মিলাদ মাহফিল (Prayer Gatherings)">১৯. দোয়া ও মিলাদ মাহফিল (Prayer Gatherings)</option>
                                                            <option value="প্রেস কনফারেন্স ও মিটিং (Press Conferences)">২০. প্রেস কনফারেন্স ও মিটিং (Press Conferences)</option>
                                                            <option value="অন্যান্য (Other)">অন্যান্য (Other)</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text small">মেহমান</span>
                                                        <input type="number" :name="'items['+index+'][guest_count]'" class="form-control" x-model="item.guest_count">
                                                    </div>
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text small">টেবিল</span>
                                                        <input type="number" :name="'items['+index+'][table_count]'" class="form-control" x-model="item.table_count">
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="input-group">
                                                                <span class="input-group-text small" title="পরিবেশনকারী সংখ্যা">🧑‍🍳</span>
                                                                <input type="number" :name="'items['+index+'][server_count]'" class="form-control" x-model="item.server_count" @input="calculateSubTotal(index)" placeholder="সংখ্যা">
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="input-group">
                                                                <span class="input-group-text small" title="পরিবেশন খরচ রেট">৳</span>
                                                                <input type="number" :name="'items['+index+'][server_rate]'" class="form-control" x-model="item.server_rate" @input="calculateSubTotal(index)" placeholder="রেট">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="form-check mb-0" title="চিহ্নিত থাকলে মোট বিলে সার্ভার খরচ যুক্ত হবে, অন্যথায় বিলে যুক্ত হবে না">
                                                            <input class="form-check-input" type="checkbox" :name="'items['+index+'][is_server_included]'" value="1" x-model="item.is_server_included" @change="calculateSubTotal(index)">
                                                            <span class="form-check-label small" style="font-size: 0.8rem;">সার্ভার খরচ বিলে অন্তর্ভুক্ত</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-3">
                                                        <label class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox" :name="'items['+index+'][is_ac]'" x-model="item.is_ac" @change="updatePrice(index)">
                                                            <span class="form-check-label">এসি (AC)</span>
                                                        </label>
                                                        <input type="number" :name="'items['+index+'][ac_price]'" class="form-control form-control-sm" x-model="item.ac_price" x-show="item.is_ac" @input="calculateSubTotal(index)">
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox" :name="'items['+index+'][extra_sound]'" x-model="item.extra_sound" @change="updatePrice(index)">
                                                            <span class="form-check-label">সাউন্ড সিস্টেম</span>
                                                        </label>
                                                        <div x-show="item.extra_sound">
                                                            <select :name="'items['+index+'][sound_vendor_id]'" class="form-control form-control-sm mb-1" x-model="item.sound_vendor_id">
                                                                <option value="">ভেন্ডর নির্বাচন করুন</option>
                                                                @foreach($vendors->where('type', 'sound') as $v)
                                                                    <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->commission_rate }}%)</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="number" :name="'items['+index+'][sound_price]'" class="form-control form-control-sm" x-model="item.sound_price" @input="calculateSubTotal(index)">
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox" :name="'items['+index+'][extra_generator]'" x-model="item.extra_generator" @change="updatePrice(index)">
                                                            <span class="form-check-label">জেনারেটর</span>
                                                        </label>
                                                        <div x-show="item.extra_generator">
                                                            <select :name="'items['+index+'][generator_vendor_id]'" class="form-control form-control-sm mb-1" x-model="item.generator_vendor_id">
                                                                <option value="">ভেন্ডর নির্বাচন করুন</option>
                                                                @foreach($vendors->where('type', 'generator') as $v)
                                                                    <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->commission_rate }}%)</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="number" :name="'items['+index+'][generator_price]'" class="form-control form-control-sm" x-model="item.generator_price" @input="calculateSubTotal(index)">
                                                        </div>
                                                    </div>

                                                    <div class="mb-1">
                                                        <label class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox" :name="'items['+index+'][extra_decoration]'" x-model="item.extra_decoration" @change="updatePrice(index)">
                                                            <span class="form-check-label">ডেকোরেশন</span>
                                                        </label>
                                                        <div x-show="item.extra_decoration">
                                                            <select :name="'items['+index+'][decoration_vendor_id]'" class="form-control form-control-sm mb-1" x-model="item.decoration_vendor_id">
                                                                <option value="">ভেন্ডর নির্বাচন করুন</option>
                                                                @foreach($vendors->where('type', 'decoration') as $v)
                                                                    <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->commission_rate }}%)</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="number" :name="'items['+index+'][decoration_price]'" class="form-control form-control-sm" x-model="item.decoration_price" @input="calculateSubTotal(index)">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-3">
                                                        <label class="form-label small">হল ভাড়া (৳)</label>
                                                        <input type="number" :name="'items['+index+'][base_price]'" class="form-control" x-model="item.base_price" @input="calculateSubTotal(index)">
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label small font-weight-bold">উপ-মোট (৳)</label>
                                                        <input type="number" :name="'items['+index+'][sub_total]'" class="form-control font-weight-bold" x-model="item.sub_total" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger" @click="removeItem(index)" x-show="items.length > 1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3 items-center">
                                    <div class="col-md-4">
                                        <label class="form-label font-weight-bold">মোট বিল (৳)</label>
                                        <input type="number" name="total_amount" class="form-control form-control-lg" :value="totalAmount" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-success font-weight-bold">অগ্রিম জমা (৳)</label>
                                        <input type="number" name="advance_amount" class="form-control form-control-lg" x-model="advanceAmount">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-danger font-weight-bold">বকেয়া (৳)</label>
                                        <input type="number" class="form-control form-control-lg" :value="(parseFloat(totalAmount) || 0) - (parseFloat(advanceAmount) || 0)" readonly>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">অতিরিক্ত নোট</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ $booking->notes }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-warning btn-lg" :disabled="hasConflict">বুকিং আপডেট করুন</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bookingForm() {
            return {
                items: @js($booking->items->map(fn($i) => [
                    'event_date' => $i->event_date->format('Y-m-d'),
                    'hall_id' => $i->hall_id,
                    'slot' => ($i->slot === 'night' || $i->slot === 'evening') ? 'night' : 'day',
                    'event_type' => match($i->event_type) {
                        'Wedding' => 'বিয়ে (Wedding Reception)',
                        'Holud' => 'গায়ে হলুদ (Gaye Holud)',
                        'Birthday' => 'জন্মদিন (Birthday Party)',
                        'Corporate' => 'কর্পোরেট ডিনার ও গ্যালা নাইট (Corporate Dinner & Gala Night)',
                        'Other' => 'অন্যান্য (Other)',
                        default => $i->event_type ?? 'বিয়ে (Wedding Reception)',
                    },
                    'guest_count' => $i->guest_count,
                    'table_count' => $i->table_count,
                    'server_count' => $i->server_count,
                    'server_rate' => $i->server_rate,
                    'is_server_included' => (bool)($i->is_server_included ?? true),
                    'is_ac' => (bool)$i->is_ac,
                    'ac_price' => (float)$i->ac_price,
                    'extra_sound' => (bool)$i->extra_sound,
                    'sound_vendor_id' => $i->sound_vendor_id,
                    'sound_price' => (float)$i->sound_price,
                    'extra_generator' => (bool)$i->extra_generator,
                    'generator_vendor_id' => $i->generator_vendor_id,
                    'generator_price' => (float)$i->generator_price,
                    'extra_decoration' => (bool)$i->extra_decoration,
                    'decoration_vendor_id' => $i->decoration_vendor_id,
                    'decoration_price' => (float)$i->decoration_price,
                    'base_price' => (float)($i->base_price ?? ($i->sub_total - ($i->ac_price + $i->sound_price + $i->generator_price + $i->decoration_price + (($i->is_server_included ?? true) ? ($i->server_count * $i->server_rate) : 0)))),
                    'sub_total' => (float)$i->sub_total,
                    'conflict_error' => '',
                ])),
                advanceAmount: {{ $booking->advance_amount }},
                init() {
                    this.items.forEach((_, idx) => this.calculateSubTotal(idx));
                    this.checkAllAvailability();
                },
                addItem() {
                    let defaultBasePrice = {{ $hall->price_per_slot ?? 0 }};
                    let lastItem = this.items.length > 0 ? this.items[this.items.length - 1] : null;
                    let nextSlot = 'day';
                    let nextDate = '';
                    if (lastItem && lastItem.event_date) {
                        nextDate = lastItem.event_date;
                        nextSlot = (lastItem.slot === 'day' || lastItem.slot === 'morning') ? 'night' : 'day';
                    }

                    this.items.push({
                        event_date: nextDate,
                        hall_id: {{ $hall->id }},
                        slot: nextSlot,
                        event_type: 'বিয়ে (Wedding Reception)',
                        guest_count: 0,
                        table_count: 0,
                        server_count: 0,
                        server_rate: {{ $hall->default_server_rate ?? 500 }},
                        is_server_included: true,
                        is_ac: false,
                        ac_price: 0,
                        extra_sound: false,
                        sound_vendor_id: '',
                        sound_price: 0,
                        extra_generator: false,
                        generator_vendor_id: '',
                        generator_price: 0,
                        extra_decoration: false,
                        decoration_vendor_id: '',
                        decoration_price: 0,
                        base_price: defaultBasePrice,
                        sub_total: defaultBasePrice,
                        conflict_error: '',
                    });

                    this.$nextTick(() => {
                        this.checkAllAvailability();
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.$nextTick(() => {
                        this.checkAllAvailability();
                    });
                },
                checkAllAvailability() {
                    this.items.forEach((_, idx) => this.checkAvailability(idx));
                },
                async checkAvailability(index) {
                    let item = this.items[index];
                    if (!item) return;

                    if (!item.event_date || !item.slot || !item.hall_id) {
                        item.conflict_error = '';
                        return;
                    }

                    // 1. Same date, same slot in current form
                    let duplicate = this.items.some((other, oIdx) => {
                        if (oIdx === index) return false;
                        if (!other.event_date || !other.slot) return false;
                        return other.event_date === item.event_date && other.slot === item.slot && String(other.hall_id) === String(item.hall_id);
                    });

                    if (duplicate) {
                        let slotLabel = item.slot === 'day' ? 'Day (দিন)' : 'Night (রাত)';
                        item.conflict_error = `একই তারিখে একই স্লট (${slotLabel}) একাধিকবার যোগ করা যাবে না! ভিন্ন স্লট নির্বাচন করুন।`;
                        return;
                    }

                    // Clear duplicate error if it was previously set
                    if (item.conflict_error && item.conflict_error.includes('একাধিকবার যোগ করা যাবে না')) {
                        item.conflict_error = '';
                    }

                    // 2. Check conflict against database
                    try {
                        let res = await fetch(`{{ route('bookings.check-availability') }}?hall_id=${item.hall_id}&event_date=${item.event_date}&slot=${item.slot}&exclude_booking_id={{ $booking->id }}`);
                        let data = await res.json();
                        if (!data.available) {
                            item.conflict_error = data.message;
                        } else {
                            let isDupNow = this.items.some((other, oIdx) => {
                                if (oIdx === index) return false;
                                if (!other.event_date || !other.slot) return false;
                                return other.event_date === item.event_date && other.slot === item.slot && String(other.hall_id) === String(item.hall_id);
                            });
                            if (!isDupNow) {
                                item.conflict_error = '';
                            }
                        }
                    } catch (e) {
                        console.error('Availability check error', e);
                    }
                },
                updatePrice(index) {
                    let item = this.items[index];
                    if (!item) return;

                    // Default addon prices if checked but zero/empty
                    if (item.is_ac && (!item.ac_price || parseFloat(item.ac_price) === 0)) item.ac_price = 5000;
                    if (!item.is_ac) item.ac_price = 0;

                    if (item.extra_sound && (!item.sound_price || parseFloat(item.sound_price) === 0)) item.sound_price = 2000;
                    if (!item.extra_sound) item.sound_price = 0;

                    if (item.extra_generator && (!item.generator_price || parseFloat(item.generator_price) === 0)) item.generator_price = 3000;
                    if (!item.extra_generator) item.generator_price = 0;

                    if (item.extra_decoration && (!item.decoration_price || parseFloat(item.decoration_price) === 0)) item.decoration_price = 10000;
                    if (!item.extra_decoration) item.decoration_price = 0;

                    this.calculateSubTotal(index);
                },
                calculateSubTotal(index) {
                    let item = this.items[index];
                    if (!item) return;

                    let ac = item.is_ac ? (parseFloat(item.ac_price) || 0) : 0;
                    let sound = item.extra_sound ? (parseFloat(item.sound_price) || 0) : 0;
                    let generator = item.extra_generator ? (parseFloat(item.generator_price) || 0) : 0;
                    let decoration = item.extra_decoration ? (parseFloat(item.decoration_price) || 0) : 0;
                    let server = (item.is_server_included && item.is_server_included !== 'false') ? ((parseFloat(item.server_count) || 0) * (parseFloat(item.server_rate) || 0)) : 0;

                    let extras = ac + sound + generator + decoration + server;
                    item.sub_total = (parseFloat(item.base_price) || 0) + extras;
                },
                get totalAmount() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.sub_total) || 0), 0);
                },
                get hasConflict() {
                    return this.items.some(i => i.conflict_error && i.conflict_error.length > 0);
                }
            }
        }
    </script>
</x-tabler-layout>
