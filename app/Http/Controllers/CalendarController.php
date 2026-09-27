<?php

namespace App\Http\Controllers;

use App\Models\BookingItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $upcomingBookings = BookingItem::with(['booking.customer', 'hall'])
            ->whereHas('booking.customer')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();
            
        return view('calendar.index', compact('upcomingBookings'));
    }

    public function events(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $items = BookingItem::with(['booking.customer', 'hall'])
            ->whereHas('booking.customer')
            ->whereBetween('event_date', [
                Carbon::parse($start)->toDateString(),
                Carbon::parse($end)->toDateString()
            ])
            ->whereHas('booking', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->get();

        $events = $items->map(function($item) {
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
                    'hall' => $item->hall?->name ?? 'মমতা কমিউনিটি সেন্টার',
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
}
