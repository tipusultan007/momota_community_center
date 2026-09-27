<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

use App\Traits\HandlesCommissions;

class BookingController extends Controller
{
    use HandlesCommissions;
    public function index(Request $request)
    {
        $query = Booking::with(['items.hall', 'customer']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            if ($request->payment_status === 'due') {
                $query->whereRaw('total_amount > advance_amount');
            } elseif ($request->payment_status === 'paid') {
                $query->whereRaw('total_amount <= advance_amount');
            }
        }

        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereHas('items', function ($iq) use ($date) {
                $iq->whereDate('event_date', $date);
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        // Summary Statistics
        $totalCount = Booking::count();
        $confirmedCount = Booking::where('status', 'confirmed')->count();
        $pendingCount = Booking::where('status', 'pending')->count();
        $totalDue = Booking::whereRaw('total_amount > advance_amount')
            ->selectRaw('SUM(total_amount - advance_amount) as total_due')
            ->value('total_due') ?? 0;

        return view('bookings.index', compact('bookings', 'totalCount', 'confirmedCount', 'pendingCount', 'totalDue'));
    }

    public function create()
    {
        $hall = Hall::firstOrCreate(
            ['id' => 1],
            ['name' => 'মমতা কমিউনিটি সেন্টার', 'capacity' => 500, 'price_per_slot' => 30000, 'is_active' => true]
        );
        $customers = Customer::all();
        $vendors = \App\Models\Vendor::where('is_active', true)->get();
        return view('bookings.create', compact('hall', 'customers', 'vendors'));
    }

    public function store(Request $request)
    {
        $defaultHall = Hall::firstOrCreate(
            ['id' => 1],
            ['name' => 'মমতা কমিউনিটি সেন্টার', 'capacity' => 500, 'price_per_slot' => 30000, 'is_active' => true]
        );

        $items = $request->items ?? [];
        foreach ($items as $k => $item) {
            if (empty($item['hall_id'])) {
                $items[$k]['hall_id'] = $defaultHall->id;
            }
        }
        $request->merge(['items' => $items]);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.hall_id' => 'nullable|exists:halls,id',
            'items.*.event_date' => 'required|date',
            'items.*.slot' => 'required|in:day,night',
            'total_amount' => 'required|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // 1. Check duplicate slots within the same request
        $seenSlots = [];
        foreach ($request->items as $item) {
            $slotKey = $item['hall_id'] . '_' . $item['event_date'] . '_' . $item['slot'];
            if (isset($seenSlots[$slotKey])) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return back()->withErrors(['items' => "একই বুকিংয়ে একই তারিখ ({$item['event_date']}) ও স্লট ({$slotLabel}) একাধিকবার যোগ করা যাবে না।"])->withInput();
            }
            $seenSlots[$slotKey] = true;
        }

        // 2. Conflict Detection against DB
        foreach ($request->items as $item) {
            $conflict = \App\Models\BookingItem::where('hall_id', $item['hall_id'])
                ->whereDate('event_date', $item['event_date'])
                ->where('slot', $item['slot'])
                ->whereHas('booking', function($q) {
                    $q->where('status', '!=', 'cancelled');
                })
                ->exists();

            if ($conflict) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return back()->withErrors(['items' => "দুঃখিত, {$item['event_date']} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই বুকড রয়েছে।"])->withInput();
            }
        }

        $booking = DB::transaction(function () use ($request, $defaultHall) {
            // 1. Create/Get Customer
            $customer = Customer::updateOrCreate(
                ['phone' => $request->customer_phone],
                ['name' => $request->customer_name, 'address' => $request->customer_address]
            );

            // 2. Create Parent Booking
            $booking = Booking::create([
                'hall_id' => $defaultHall->id,
                'customer_id' => $customer->id,
                'total_amount' => $request->total_amount,
                'advance_amount' => $request->advance_amount ?? 0,
                'notes' => $request->notes,
                'status' => 'confirmed',
            ]);

            // 3. Create Booking Items
            foreach ($request->items as $item) {
                $booking->items()->create([
                    'hall_id' => $item['hall_id'],
                    'event_date' => $item['event_date'],
                    'slot' => $item['slot'],
                    'event_type' => $item['event_type'] ?? 'Wedding',
                    'guest_count' => $item['guest_count'] ?? 0,
                    'table_count' => $item['table_count'] ?? 0,
                    'server_count' => $item['server_count'] ?? 0,
                    'server_rate' => $item['server_rate'] ?? 0,
                    'is_server_included' => !empty($item['is_server_included']) && $item['is_server_included'] !== 'false',
                    'base_price' => $item['base_price'] ?? 0,
                    'is_ac' => !empty($item['is_ac']) && $item['is_ac'] !== 'false',
                    'ac_price' => (!empty($item['is_ac']) && $item['is_ac'] !== 'false') ? ($item['ac_price'] ?? 0) : 0,
                    'extra_sound' => !empty($item['extra_sound']) && $item['extra_sound'] !== 'false',
                    'sound_vendor_id' => (!empty($item['extra_sound']) && $item['extra_sound'] !== 'false') ? ($item['sound_vendor_id'] ?? null) : null,
                    'sound_price' => (!empty($item['extra_sound']) && $item['extra_sound'] !== 'false') ? ($item['sound_price'] ?? 0) : 0,
                    'extra_generator' => !empty($item['extra_generator']) && $item['extra_generator'] !== 'false',
                    'generator_vendor_id' => (!empty($item['extra_generator']) && $item['extra_generator'] !== 'false') ? ($item['generator_vendor_id'] ?? null) : null,
                    'generator_price' => (!empty($item['extra_generator']) && $item['extra_generator'] !== 'false') ? ($item['generator_price'] ?? 0) : 0,
                    'extra_decoration' => !empty($item['extra_decoration']) && $item['extra_decoration'] !== 'false',
                    'decoration_vendor_id' => (!empty($item['extra_decoration']) && $item['extra_decoration'] !== 'false') ? ($item['decoration_vendor_id'] ?? null) : null,
                    'decoration_price' => (!empty($item['extra_decoration']) && $item['extra_decoration'] !== 'false') ? ($item['decoration_price'] ?? 0) : 0,
                    'sub_total' => $item['sub_total'],
                ]);

                // 3b. Calculate and Record Commissions
                $this->recordCommissions($booking, $item);
            }

            // 4. Record Income
            if ($request->advance_amount > 0) {
                \App\Models\Income::create([
                    'hall_id' => $booking->hall_id,
                    'amount' => $request->advance_amount,
                    'category' => 'booking',
                    'booking_id' => $booking->id,
                    'description' => 'বুকিং বাবদ অগ্রিম পেমেন্ট - ' . $customer->name,
                    'date' => now(),
                ]);
            }

            return $booking;
        });

        // 5. Send Notification
        try {
            $booking = Booking::with(['customer', 'items'])->find($booking->id);
            $booking->customer->notify(new \App\Notifications\BookingConfirmedNotification($booking));
        } catch (\Exception $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        return redirect()->route('bookings.index')->with('success', 'বুকিং সফলভাবে সম্পন্ন হয়েছে।');
    }

    public function show(Booking $booking)
    {
        $booking->load(['items.hall', 'customer']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $booking->load(['items.hall', 'customer']);
        $hall = Hall::firstOrCreate(
            ['id' => 1],
            ['name' => 'মমতা কমিউনিটি সেন্টার', 'capacity' => 500, 'price_per_slot' => 30000, 'is_active' => true]
        );
        $customers = Customer::all();
        $vendors = \App\Models\Vendor::where('is_active', true)->get();
        return view('bookings.edit', compact('booking', 'hall', 'customers', 'vendors'));
    }

    public function update(Request $request, Booking $booking)
    {
        $defaultHall = Hall::firstOrCreate(
            ['id' => 1],
            ['name' => 'মমতা কমিউনিটি সেন্টার', 'capacity' => 500, 'price_per_slot' => 30000, 'is_active' => true]
        );

        $items = $request->items ?? [];
        foreach ($items as $k => $item) {
            if (empty($item['hall_id'])) {
                $items[$k]['hall_id'] = $defaultHall->id;
            }
        }
        $request->merge(['items' => $items]);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.hall_id' => 'nullable|exists:halls,id',
            'items.*.event_date' => 'required|date',
            'items.*.slot' => 'required|in:day,night',
            'total_amount' => 'required|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // 1. Check duplicate slots within the same request
        $seenSlots = [];
        foreach ($request->items as $item) {
            $slotKey = $item['hall_id'] . '_' . $item['event_date'] . '_' . $item['slot'];
            if (isset($seenSlots[$slotKey])) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return back()->withErrors(['items' => "একই বুকিংয়ে একই তারিখ ({$item['event_date']}) ও স্লট ({$slotLabel}) একাধিকবার যোগ করা যাবে না।"])->withInput();
            }
            $seenSlots[$slotKey] = true;
        }

        // 2. Conflict Detection (excluding current booking items)
        foreach ($request->items as $item) {
            $query = \App\Models\BookingItem::where('hall_id', $item['hall_id'])
                ->whereDate('event_date', $item['event_date'])
                ->where('slot', $item['slot'])
                ->whereHas('booking', function($q) use ($booking) {
                    $q->where('status', '!=', 'cancelled')->where('id', '!=', $booking->id);
                });

            if ($query->exists()) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return back()->withErrors(['items' => "দুঃখিত, {$item['event_date']} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই অন্য একটি বুকিংয়ে রিজার্ভ করা আছে।"])->withInput();
            }
        }

        DB::transaction(function () use ($request, $booking) {
            // 1. Update Customer
            $booking->customer->update([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'address' => $request->customer_address,
            ]);

            // 2. Update Parent Booking
            $booking->update([
                'total_amount' => $request->total_amount,
                'advance_amount' => $request->advance_amount ?? 0,
                'notes' => $request->notes,
            ]);

            // 3. Sync Booking Items (Delete and Recreate is easier for dynamic arrays)
            $booking->items()->delete();
            foreach ($request->items as $item) {
                $booking->items()->create([
                    'hall_id' => $item['hall_id'],
                    'event_date' => $item['event_date'],
                    'slot' => $item['slot'],
                    'event_type' => $item['event_type'] ?? 'Wedding',
                    'guest_count' => $item['guest_count'] ?? 0,
                    'table_count' => $item['table_count'] ?? 0,
                    'server_count' => $item['server_count'] ?? 0,
                    'server_rate' => $item['server_rate'] ?? 0,
                    'is_server_included' => !empty($item['is_server_included']) && $item['is_server_included'] !== 'false',
                    'base_price' => $item['base_price'] ?? 0,
                    'is_ac' => !empty($item['is_ac']) && $item['is_ac'] !== 'false',
                    'ac_price' => (!empty($item['is_ac']) && $item['is_ac'] !== 'false') ? ($item['ac_price'] ?? 0) : 0,
                    'extra_sound' => !empty($item['extra_sound']) && $item['extra_sound'] !== 'false',
                    'sound_vendor_id' => (!empty($item['extra_sound']) && $item['extra_sound'] !== 'false') ? ($item['sound_vendor_id'] ?? null) : null,
                    'sound_price' => (!empty($item['extra_sound']) && $item['extra_sound'] !== 'false') ? ($item['sound_price'] ?? 0) : 0,
                    'extra_generator' => !empty($item['extra_generator']) && $item['extra_generator'] !== 'false',
                    'generator_vendor_id' => (!empty($item['extra_generator']) && $item['extra_generator'] !== 'false') ? ($item['generator_vendor_id'] ?? null) : null,
                    'generator_price' => (!empty($item['extra_generator']) && $item['extra_generator'] !== 'false') ? ($item['generator_price'] ?? 0) : 0,
                    'extra_decoration' => !empty($item['extra_decoration']) && $item['extra_decoration'] !== 'false',
                    'decoration_vendor_id' => (!empty($item['extra_decoration']) && $item['extra_decoration'] !== 'false') ? ($item['decoration_vendor_id'] ?? null) : null,
                    'decoration_price' => (!empty($item['extra_decoration']) && $item['extra_decoration'] !== 'false') ? ($item['decoration_price'] ?? 0) : 0,
                    'sub_total' => $item['sub_total'],
                ]);
            }

            // 4. Update Commissions
            \App\Models\Commission::where('booking_id', $booking->id)->delete();
            foreach ($request->items as $item) {
                $this->recordCommissions($booking, $item);
            }
        });

        return redirect()->route('bookings.index')->with('success', 'বুকিং সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            foreach ($booking->incomes as $income) {
                if ($income->transaction) {
                    $income->transaction()->delete();
                }
                $income->delete();
            }
            \App\Models\Income::where('booking_id', $booking->id)->delete();
            \App\Models\Commission::where('booking_id', $booking->id)->delete();
            $booking->items()->delete();
            $booking->delete();
        });

        return redirect()->route('bookings.index')->with('success', 'বুকিং এবং সংশ্লিষ্ট সকল লেনদেন মুছে ফেলা হয়েছে।');
    }

    public function downloadInvoice(Booking $booking)
    {
        $booking->load(['items.hall', 'customer', 'hall']);
        $pdf = Pdf::loadView('bookings.invoice', ['booking' => $booking], [], [
            'default_font' => 'hind_siliguri'
        ]);
        return $pdf->download('invoice-' . $booking->id . '.pdf');
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $booking) {
            // 1. Record Income
            $booking->incomes()->create([
                'hall_id' => $booking->hall_id,
                'amount' => $request->amount,
                'category' => 'booking',
                'description' => $request->description ?? 'বুকিং পেমেন্ট',
                'date' => $request->date,
            ]);

            // 2. Update Booking Advance (total paid)
            $booking->increment('advance_amount', $request->amount);
        });

        return back()->with('success', 'পেমেন্ট সফলভাবে যোগ করা হয়েছে।');
    }

    public function payServers(Booking $booking)
    {
        if ($booking->server_payout_status === 'paid') {
            return back()->with('error', 'এই বুকিংয়ের পরিবেশনকারীদের বিল ইতিপূর্বেই পরিশোধ করা হয়েছে।');
        }

        $serverCost = $booking->total_server_cost;
        if ($serverCost <= 0) {
            return back()->with('error', 'এই বুকিংয়ে কোনো পরিবেশনকারী বিল নেই।');
        }

        DB::transaction(function () use ($booking, $serverCost) {
            $category = \App\Models\ExpenseCategory::firstOrCreate(
                ['name' => 'পরিবেশনকারী খরচ'],
                ['is_active' => true]
            );

            $expense = \App\Models\Expense::create([
                'hall_id' => $booking->hall_id,
                'amount' => $serverCost,
                'category' => $category->name,
                'expense_category_id' => $category->id,
                'description' => "বুকিং #{$booking->id} ({$booking->customer->name}) - পরিবেশনকারীদের বিল পরিশোধ",
                'date' => now()->toDateString(),
            ]);

            $booking->update([
                'server_payout_status' => 'paid',
                'server_payout_date' => now(),
                'server_expense_id' => $expense->id,
            ]);
        });

        return back()->with('success', 'পরিবেশনকারীদের বিল (৳ ' . number_format($serverCost, 2) . ') সফলভাবে পরিশোধ করা হয়েছে এবং খরচ (Expense) হিসেবে রেকর্ড করা হয়েছে।');
    }
    public function assignAsset(Request $request, \App\Models\BookingItem $item)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $asset = \App\Models\Asset::findOrFail($request->asset_id);

        if ($asset->available_stock < $request->quantity) {
            return back()->with('error', 'পর্যাপ্ত স্টক নেই। বর্তমানে আছে: ' . $asset->available_stock);
        }

        DB::transaction(function () use ($request, $item, $asset) {
            $item->assetAssignments()->create([
                'asset_id' => $request->asset_id,
                'quantity_out' => $request->quantity,
            ]);

            $asset->decrement('available_stock', $request->quantity);
        });

        return back()->with('success', 'মালামাল বরাদ্দ সম্পন্ন হয়েছে।');
    }

    public function returnAsset(Request $request, \App\Models\AssetAssignment $assignment)
    {
        $request->validate([
            'quantity_in' => 'required|integer|min:0|max:' . $assignment->quantity_out,
            'damaged_quantity' => 'required|integer|min:0|max:' . $assignment->quantity_out,
        ]);

        DB::transaction(function () use ($request, $assignment) {
            $assignment->update([
                'quantity_in' => $request->quantity_in,
                'damaged_quantity' => $request->damaged_quantity,
                'notes' => $request->notes,
            ]);

            // Put back into available stock only the non-damaged returned items
            $assignment->asset->increment('available_stock', $request->quantity_in);
            
            // Optionally: decrement total_stock if damaged means "lost"
            if ($request->damaged_quantity > 0) {
                $assignment->asset->decrement('total_stock', $request->damaged_quantity);
            }
        });

        return back()->with('success', 'মালামাল রিটার্ন সফলভাবে রেকর্ড করা হয়েছে।');
    }

    public function checkAvailability(Request $request)
    {
        $hallId = $request->hall_id ?: (Hall::first()?->id ?? 1);
        $request->merge(['hall_id' => $hallId]);

        $request->validate([
            'hall_id' => 'required|exists:halls,id',
            'event_date' => 'required|date',
            'slot' => 'required|in:day,night',
        ]);

        $conflict = \App\Models\BookingItem::where('hall_id', $request->hall_id)
            ->whereDate('event_date', $request->event_date)
            ->where('slot', $request->slot)
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('status', '!=', 'cancelled');
                if ($request->filled('exclude_booking_id')) {
                    $q->where('id', '!=', $request->exclude_booking_id);
                }
            })
            ->exists();

        $slotLabel = $request->slot === 'day' ? 'দিন (Day)' : 'রাত (Night)';

        if ($conflict) {
            return response()->json([
                'available' => false,
                'message' => "দুঃখিত, {$request->event_date} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই বুকড রয়েছে।",
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'স্লটটি ফাঁকা রয়েছে।',
        ]);
    }
}
