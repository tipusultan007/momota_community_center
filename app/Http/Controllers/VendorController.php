<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(10);
        return view('vendors.index', compact('vendors'));
    }

    public function show(Vendor $vendor)
    {
        // 1. Get all bookings where this vendor was used (Sound, Generator, or Decoration)
        $bookingItems = \App\Models\BookingItem::where(function($query) use ($vendor) {
                $query->where('sound_vendor_id', $vendor->id)
                      ->orWhere('generator_vendor_id', $vendor->id)
                      ->orWhere('decoration_vendor_id', $vendor->id);
            })
            ->with(['booking.customer'])
            ->orderBy('event_date', 'desc')
            ->get();

        // 2. Map items to clarify which service they provided in that booking
        $history = $bookingItems->map(function($item) use ($vendor) {
            $services = [];
            if ($item->sound_vendor_id == $vendor->id) $services[] = ['type' => 'Sound', 'price' => $item->sound_price];
            if ($item->generator_vendor_id == $vendor->id) $services[] = ['type' => 'Generator', 'price' => $item->generator_price];
            if ($item->decoration_vendor_id == $vendor->id) $services[] = ['type' => 'Decoration', 'price' => $item->decoration_price];
            
            return [
                'booking' => $item->booking,
                'date' => $item->event_date,
                'services' => $services,
                'total_price' => collect($services)->sum('price')
            ];
        });

        // 3. Get Commissions
        $commissions = \App\Models\Commission::where('vendor_id', $vendor->id)
            ->with('booking')
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Financial Calculations
        $totalServiceValue = $history->sum('total_price');
        $totalCommissionPaid = $commissions->where('status', 'paid')->sum('amount');
        $totalCommissionPending = $commissions->where('status', 'pending')->sum('amount');
        $netEarnings = $totalServiceValue - ($totalCommissionPaid + $totalCommissionPending);

        return view('vendors.show', compact(
            'vendor', 
            'history', 
            'commissions', 
            'totalServiceValue', 
            'totalCommissionPaid', 
            'totalCommissionPending',
            'netEarnings'
        ));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'phone' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        Vendor::create($request->all());

        return redirect()->route('vendors.index')->with('success', 'ভেন্ডর সফলভাবে যুক্ত করা হয়েছে।');
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'phone' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $vendor->update($request->all());

        return redirect()->route('vendors.index')->with('success', 'ভেন্ডর সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'ভেন্ডর সফলভাবে ডিলিট করা হয়েছে।');
    }
}
