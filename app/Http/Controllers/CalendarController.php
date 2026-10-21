<?php

namespace App\Http\Controllers;

use App\Models\BookingItem;
use App\Models\WeddingDate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $upcomingBookings = BookingItem::with(['booking.customer'])
            ->whereHas('booking.customer')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
        $businessSetting = \App\Models\BusinessSetting::get();
            
        return view('calendar.index', compact('upcomingBookings', 'businessSetting'));
    }

    public function events(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $businessName = \App\Models\BusinessSetting::get()->name;

        $items = BookingItem::with(['booking.customer'])
            ->whereHas('booking.customer')
            ->whereBetween('event_date', [
                Carbon::parse($start)->toDateString(),
                Carbon::parse($end)->toDateString()
            ])
            ->whereHas('booking', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->get();

        $events = $items->map(function($item) use ($businessName) {
            $color = '#206bc4'; // default blue
            if ($item->slot === 'day' || $item->slot === 'morning') $color = '#4299e1';
            if ($item->slot === 'night' || $item->slot === 'evening') $color = '#4c51bf';
            if ($item->slot === 'full_day') $color = '#2d3748';

            return [
                'id' => $item->id,
                'title' => $item->event_type . ' - ' . $item->booking->customer->name,
                'start' => $item->event_date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'customer' => $item->booking->customer->name,
                    'phone' => $item->booking->customer->phone,
                    'venue' => $businessName,
                    'slot' => $item->slot,
                    'event_type' => $item->event_type,
                    'guest_count' => $item->guest_count,
                    'server_count' => $item->server_count,
                    'booking_id' => $item->booking_id,
                    'status' => ucfirst($item->booking->status),
                    'total_amount' => number_format($item->booking->total_amount, 2),
                    'paid_amount' => number_format($item->booking->paid_amount, 2),
                    'due_amount' => number_format($item->booking->total_amount - $item->booking->paid_amount, 2),
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get auspicious Hindu wedding dates for the calendar range or month.
     */
    public function weddingDates(Request $request)
    {
        $start = $request->get('start') ? Carbon::parse($request->get('start'))->toDateString() : now()->startOfMonth()->toDateString();
        $end = $request->get('end') ? Carbon::parse($request->get('end'))->toDateString() : now()->endOfMonth()->toDateString();

        $weddingDates = WeddingDate::whereBetween('date', [$start, $end])
            ->where('is_active', true)
            ->orderBy('date', 'asc')
            ->get();

        $data = $weddingDates->map(function($wd) {
            $avail = $wd->getAvailabilityStatus();
            $cDate = Carbon::parse($wd->date);
            $bothBn = \App\Helpers\BanglaDateHelper::getBothDates($wd->date);

            return [
                'id' => $wd->id,
                'date' => $cDate->format('Y-m-d'),
                'day' => $cDate->format('d'),
                'month_name' => $cDate->locale('bn')->translatedFormat('F'),
                'day_name' => $cDate->locale('bn')->translatedFormat('l'),
                'formatted_date' => $cDate->format('d M, Y'),
                'title' => $wd->title,
                'bengali_date' => $wd->bengali_date ?: $bothBn['panjika']['full'],
                'bengali_date_academy' => $bothBn['academy']['full'],
                'bengali_date_panjika' => $bothBn['panjika']['full'],
                'tithi' => $wd->tithi,
                'notes' => $wd->notes,
                'availability' => $avail,
                'create_booking_url' => route('bookings.create', ['date' => $cDate->format('Y-m-d')])
            ];
        });

        return response()->json($data);
    }

    /**
     * Store or update an auspicious wedding date.
     */
    public function storeWeddingDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'bengali_date' => 'nullable|string|max:255',
            'tithi' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $weddingDate = WeddingDate::updateOrCreate(
            ['date' => Carbon::parse($request->date)->format('Y-m-d')],
            [
                'title' => $request->title ?: 'শুভ বিবাহ লগ্ন',
                'bengali_date' => $request->bengali_date,
                'tithi' => $request->tithi ?: 'রাত্রিলগ্ন',
                'notes' => $request->notes,
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'শুভ বিবাহের তারিখ সফলভাবে যুক্ত করা হয়েছে।',
            'wedding_date' => $weddingDate
        ]);
    }

    /**
     * Delete an auspicious wedding date.
     */
    public function destroyWeddingDate($id)
    {
        $weddingDate = WeddingDate::findOrFail($id);
        $weddingDate->delete();

        return response()->json([
            'success' => true,
            'message' => 'শুভ বিবাহের তারিখ মুছে ফেলা হয়েছে।'
        ]);
    }
}
