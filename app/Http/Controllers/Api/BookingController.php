<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

use App\Traits\HandlesCommissions;

class BookingController extends Controller
{
    use HandlesCommissions;
    public function index(Request $request)
    {
        $hallId = $request->header('X-Hall-Id');

        $bookings = Booking::with(['customer', 'items.hall', 'items.decorationVendor', 'items.soundVendor', 'items.generatorVendor', 'items.assetAssignments.asset', 'incomes'])
            ->when($hallId, fn($q) => $q->where('hall_id', $hallId))
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ]);
    }

    public function store(Request $request)
    {
        $defaultHall = \App\Models\Hall::firstOrCreate(
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
            'items.*.is_server_included' => 'nullable|boolean',
            'total_amount' => 'required|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
        ]);

        // 1. Check duplicate slots within the same request
        $seenSlots = [];
        foreach ($request->items as $item) {
            $slotKey = $item['hall_id'] . '_' . $item['event_date'] . '_' . $item['slot'];
            if (isset($seenSlots[$slotKey])) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return response()->json([
                    'status' => 'error',
                    'message' => "একই বুকিংয়ে একই তারিখ ({$item['event_date']}) ও স্লট ({$slotLabel}) একাধিকবার যোগ করা যাবে না।",
                    'errors' => [
                        'slot' => ["একই বুকিংয়ে একই তারিখ ({$item['event_date']}) ও স্লট ({$slotLabel}) একাধিকবার যোগ করা যাবে না।"]
                    ]
                ], 422);
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
                return response()->json([
                    'status' => 'error',
                    'message' => "দুঃখিত, {$item['event_date']} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই বুকড রয়েছে।",
                    'errors' => [
                        'slot' => ["দুঃখিত, {$item['event_date']} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই বুকড রয়েছে।"]
                    ]
                ], 422);
            }
        }

        return \DB::transaction(function () use ($request, $defaultHall) {
            $customer = \App\Models\Customer::updateOrCreate(
                ['phone' => $request->customer_phone],
                ['name' => $request->customer_name, 'address' => $request->customer_address]
            );

            $booking = Booking::create([
                'hall_id' => $defaultHall->id,
                'customer_id' => $customer->id,
                'total_amount' => $request->total_amount,
                'advance_amount' => $request->advance_amount ?? 0,
                'notes' => $request->notes,
                'status' => 'confirmed',
            ]);

            foreach ($request->items as $item) {
                $booking->items()->create([
                    'hall_id' => $item['hall_id'],
                    'event_date' => $item['event_date'],
                    'slot' => $item['slot'],
                    'event_type' => $item['event_type'] ?? 'বিয়ে (Wedding Reception)',
                    'guest_count' => $item['guest_count'] ?? 0,
                    'table_count' => $item['table_count'] ?? 0,
                    'server_count' => $item['server_count'] ?? 0,
                    'server_rate' => $item['server_rate'] ?? 0,
                    'is_server_included' => isset($item['is_server_included']) ? filter_var($item['is_server_included'], FILTER_VALIDATE_BOOLEAN) : true,
                    'base_price' => $item['base_price'] ?? 0,
                    'is_ac' => $item['is_ac'] ?? false,
                    'ac_price' => $item['ac_price'] ?? 0,
                    'extra_decoration' => $item['extra_decoration'] ?? false,
                    'decoration_price' => $item['decoration_price'] ?? 0,
                    'decoration_vendor_id' => $item['decoration_vendor_id'] ?? null,
                    'extra_sound' => $item['extra_sound'] ?? false,
                    'sound_price' => $item['sound_price'] ?? 0,
                    'sound_vendor_id' => $item['sound_vendor_id'] ?? null,
                    'extra_generator' => $item['extra_generator'] ?? false,
                    'generator_price' => $item['generator_price'] ?? 0,
                    'generator_vendor_id' => $item['generator_vendor_id'] ?? null,
                    'sub_total' => $item['sub_total'] ?? 0,
                ]);

                // Record Commissions (from Trait)
                $this->recordCommissions($booking, $item);
            }

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

            return response()->json([
                'status' => 'success',
                'message' => 'বুকিং সফলভাবে সম্পন্ন হয়েছে।',
                'data' => $booking->load('items', 'customer', 'incomes'),
            ], 201);
        });
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::with(['customer', 'items.hall', 'items.decorationVendor', 'items.soundVendor', 'items.generatorVendor', 'items.assetAssignments.asset', 'incomes'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $booking,
        ]);
    }

    public function update(Request $request, $id)
    {
        $defaultHall = \App\Models\Hall::firstOrCreate(
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
            'items.*.is_server_included' => 'nullable|boolean',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $booking = Booking::findOrFail($id);

        // 1. Check duplicate slots within the same request
        $seenSlots = [];
        foreach ($request->items as $item) {
            $slotKey = $item['hall_id'] . '_' . $item['event_date'] . '_' . $item['slot'];
            if (isset($seenSlots[$slotKey])) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return response()->json([
                    'status' => 'error',
                    'message' => "একই বুকিংয়ে একই তারিখ ({$item['event_date']}) ও স্লট ({$slotLabel}) একাধিকবার যোগ করা যাবে না।",
                    'errors' => [
                        'slot' => ["একই বুকিংয়ে একই তারিখ ({$item['event_date']}) ও স্লট ({$slotLabel}) একাধিকবার যোগ করা যাবে না।"]
                    ]
                ], 422);
            }
            $seenSlots[$slotKey] = true;
        }

        // 2. Conflict Detection (excluding current booking items)
        foreach ($request->items as $item) {
            $conflict = \App\Models\BookingItem::where('hall_id', $item['hall_id'])
                ->whereDate('event_date', $item['event_date'])
                ->where('slot', $item['slot'])
                ->whereHas('booking', function($q) use ($booking) {
                    $q->where('status', '!=', 'cancelled')->where('id', '!=', $booking->id);
                })
                ->exists();

            if ($conflict) {
                $slotLabel = $item['slot'] === 'day' ? 'দিন (Day)' : 'রাত (Night)';
                return response()->json([
                    'status' => 'error',
                    'message' => "দুঃখিত, {$item['event_date']} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই অন্য একটি বুকিংয়ে রিজার্ভ করা আছে।",
                    'errors' => [
                        'slot' => ["দুঃখিত, {$item['event_date']} তারিখে {$slotLabel} স্লটটি ইতিমধ্যেই অন্য একটি বুকিংয়ে রিজার্ভ করা আছে।"]
                    ]
                ], 422);
            }
        }

        return \DB::transaction(function () use ($request, $booking, $defaultHall) {
            $booking->customer->update([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'address' => $request->customer_address,
            ]);

            $booking->update([
                'total_amount' => $request->total_amount,
                'hall_id' => $defaultHall->id,
                'notes' => $request->notes,
            ]);

            // Delete old commissions before re-recording
            \App\Models\Commission::where('booking_id', $booking->id)->delete();

            $booking->items()->delete();
            foreach ($request->items as $item) {
                $booking->items()->create([
                    'hall_id' => $item['hall_id'],
                    'event_date' => $item['event_date'],
                    'slot' => $item['slot'],
                    'event_type' => $item['event_type'] ?? 'বিয়ে (Wedding Reception)',
                    'guest_count' => $item['guest_count'] ?? 0,
                    'table_count' => $item['table_count'] ?? 0,
                    'server_count' => $item['server_count'] ?? 0,
                    'server_rate' => $item['server_rate'] ?? 0,
                    'is_server_included' => isset($item['is_server_included']) ? filter_var($item['is_server_included'], FILTER_VALIDATE_BOOLEAN) : true,
                    'base_price' => $item['base_price'] ?? 0,
                    'is_ac' => $item['is_ac'] ?? false,
                    'ac_price' => $item['ac_price'] ?? 0,
                    'extra_decoration' => $item['extra_decoration'] ?? false,
                    'decoration_price' => $item['decoration_price'] ?? 0,
                    'decoration_vendor_id' => $item['decoration_vendor_id'] ?? null,
                    'extra_sound' => $item['extra_sound'] ?? false,
                    'sound_price' => $item['sound_price'] ?? 0,
                    'sound_vendor_id' => $item['sound_vendor_id'] ?? null,
                    'extra_generator' => $item['extra_generator'] ?? false,
                    'generator_price' => $item['generator_price'] ?? 0,
                    'generator_vendor_id' => $item['generator_vendor_id'] ?? null,
                    'sub_total' => $item['sub_total'] ?? 0,
                ]);

                // Record Commissions (from Trait)
                $this->recordCommissions($booking, $item);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'বুকিং আপডেট করা হয়েছে।',
                'data' => $booking->load('items', 'customer', 'incomes'),
            ]);
        });
    }

    public function destroy(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($booking) {
            // Delete associated incomes & transactions
            foreach ($booking->incomes as $income) {
                if ($income->transaction) {
                    $income->transaction()->delete();
                }
                $income->delete();
            }
            \App\Models\Income::where('booking_id', $booking->id)->delete();

            // Delete commissions & items
            \App\Models\Commission::where('booking_id', $booking->id)->delete();
            $booking->items()->delete();

            // Delete booking
            $booking->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'বুকিং এবং সংশ্লিষ্ট সকল লেনদেন মুছে ফেলা হয়েছে।',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'বুকিং স্ট্যাটাস আপডেট করা হয়েছে।',
            'data' => $booking,
        ]);
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
        ]);

        $booking = Booking::findOrFail($id);

        \DB::transaction(function () use ($request, $booking) {
            \App\Models\Income::create([
                'hall_id' => $booking->hall_id,
                'booking_id' => $booking->id,
                'amount' => $request->amount,
                'category' => 'booking',
                'description' => $request->description ?? 'বুকিং পেমেন্ট',
                'date' => $request->date,
            ]);

            $booking->increment('advance_amount', $request->amount);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'পেমেন্ট সফলভাবে যোগ করা হয়েছে।',
        ]);
    }

    public function payServers($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->server_payout_status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'এই বুকিংয়ের পরিবেশনকারীদের বিল ইতিপূর্বেই পরিশোধ করা হয়েছে।',
            ], 422);
        }

        $serverCost = $booking->total_server_cost;
        if ($serverCost <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'এই বুকিংয়ে কোনো পরিবেশনকারী বিল নেই।',
            ], 422);
        }

        \DB::transaction(function () use ($booking, $serverCost) {
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

        return response()->json([
            'status' => 'success',
            'message' => 'পরিবেশনকারীদের বিল সফলভাবে পরিশোধ করা হয়েছে।',
            'server_cost' => $serverCost,
        ]);
    }

    public function downloadInvoice(Request $request, $id)
    {
        $booking = Booking::with(['hall', 'incomes', 'customer', 'items'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('bookings.invoice', ['booking' => $booking], [], [
            'default_font' => 'hind_siliguri'
        ]);
        return $pdf->download('invoice-' . $booking->id . '.pdf');
    }

    public function assignAsset(Request $request, $itemId)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = \App\Models\BookingItem::findOrFail($itemId);
        $asset = \App\Models\Asset::findOrFail($request->asset_id);

        if ($asset->available_stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'পর্যাপ্ত স্টক নেই। বর্তমানে আছে: ' . $asset->available_stock,
            ], 400);
        }

        return \DB::transaction(function () use ($request, $item, $asset) {
            $assignment = $item->assetAssignments()->create([
                'hall_id' => $item->hall_id,
                'asset_id' => $request->asset_id,
                'quantity_out' => $request->quantity,
            ]);

            $asset->decrement('available_stock', $request->quantity);

            return response()->json([
                'status' => 'success',
                'message' => 'মালামাল বরাদ্দ সম্পন্ন হয়েছে।',
                'data' => $assignment->load('asset'),
            ]);
        });
    }

    public function returnAsset(Request $request, $assignmentId)
    {
        $assignment = \App\Models\AssetAssignment::findOrFail($assignmentId);

        $request->validate([
            'quantity_in' => 'required|integer|min:0|max:' . $assignment->quantity_out,
            'damaged_quantity' => 'required|integer|min:0|max:' . $assignment->quantity_out,
        ]);

        return \DB::transaction(function () use ($request, $assignment) {
            $assignment->update([
                'quantity_in' => $request->quantity_in,
                'damaged_quantity' => $request->damaged_quantity,
                'notes' => $request->notes,
            ]);

            // Put back into available stock only the non-damaged returned items
            $assignment->asset->increment('available_stock', $request->quantity_in);
            
            // Adjust total stock if items are damaged
            if ($request->damaged_quantity > 0) {
                $assignment->asset->decrement('total_stock', $request->damaged_quantity);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'মালামাল রিটার্ন সফলভাবে রেকর্ড করা হয়েছে।',
            ]);
        });
    }

    public function checkAvailability(Request $request)
    {
        $hallId = $request->hall_id ?: (\App\Models\Hall::first()?->id ?? 1);
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
