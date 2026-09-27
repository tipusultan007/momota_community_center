<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\BookingItem;
use App\Models\Commission;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::latest()
            ->paginate($request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $vendors,
        ]);
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'phone' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $vendor = Vendor::create($request->only(['name', 'type', 'phone', 'commission_rate']));

        return response()->json([
            'status' => 'success',
            'message' => 'ভেন্ডর সফলভাবে যুক্ত করা হয়েছে।',
            'data' => $vendor,
        ], 201);
    }

    /**
     * Display the specified vendor with full history and financial summary.
     */
    public function show(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        // 1. Get all bookings where this vendor was used (Sound, Generator, or Decoration)
        $bookingItems = BookingItem::where(function($query) use ($vendor) {
                $query->where('sound_vendor_id', $vendor->id)
                      ->orWhere('generator_vendor_id', $vendor->id)
                      ->orWhere('decoration_vendor_id', $vendor->id);
            })
            ->with(['booking.customer', 'hall'])
            ->orderBy('event_date', 'desc')
            ->get();

        // 2. Map items to clarify which service they provided
        $history = $bookingItems->map(function($item) use ($vendor) {
            $services = [];
            if ($item->sound_vendor_id == $vendor->id) $services[] = ['type' => 'Sound', 'price' => (float)$item->sound_price];
            if ($item->generator_vendor_id == $vendor->id) $services[] = ['type' => 'Generator', 'price' => (float)$item->generator_price];
            if ($item->decoration_vendor_id == $vendor->id) $services[] = ['type' => 'Decoration', 'price' => (float)$item->decoration_price];
            
            return [
                'booking_id' => $item->booking?->id,
                'customer_name' => $item->booking?->customer?->name,
                'event_date' => $item->event_date,
                'hall_name' => $item->hall?->name,
                'services' => $services,
                'total_price' => collect($services)->sum('price')
            ];
        });

        // 3. Get Commissions and Payouts
        $commissions = Commission::where('vendor_id', $vendor->id)
            ->with('booking')
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Financial Calculations (Same logic as Web)
        $totalServiceValue = (float)$history->sum('total_price');
        $totalCommissionPaid = (float)$commissions->where('status', 'paid')->sum('amount');
        $totalCommissionPending = (float)$commissions->where('status', 'pending')->sum('amount');
        $totalPayoutPaid = (float)$commissions->where('payout_status', 'paid')->sum('net_payout');
        $totalPayoutPending = (float)$commissions->where('payout_status', 'pending')->sum('net_payout');
        
        $netEarnings = $totalServiceValue - ($totalCommissionPaid + $totalCommissionPending);

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor' => $vendor,
                'summary' => [
                    'total_service_value' => $totalServiceValue,
                    'total_commission_paid' => $totalCommissionPaid,
                    'total_commission_pending' => $totalCommissionPending,
                    'total_payout_paid' => $totalPayoutPaid,
                    'total_payout_pending' => $totalPayoutPending,
                    'net_earnings' => $netEarnings,
                ],
                'history' => $history,
                'commissions' => $commissions,
            ],
        ]);
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'phone' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $vendor->update($request->only(['name', 'type', 'phone', 'commission_rate']));

        return response()->json([
            'status' => 'success',
            'message' => 'ভেন্ডর সফলভাবে আপডেট করা হয়েছে।',
            'data' => $vendor,
        ]);
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        // Check for usage before deleting
        $isUsed = BookingItem::where('sound_vendor_id', $vendor->id)
            ->orWhere('generator_vendor_id', $vendor->id)
            ->orWhere('decoration_vendor_id', $vendor->id)
            ->exists();

        if ($isUsed) {
            return response()->json([
                'status' => 'error',
                'message' => 'এই ভেন্ডরটির বুকিং রেকর্ড থাকায় এটি মুছে ফেলা সম্ভব নয়।',
            ], 422);
        }

        $vendor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ভেন্ডর সফলভাবে মুছে ফেলা হয়েছে।',
        ]);
    }
}
