<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WeddingDate extends Model
{
    protected $fillable = [
        'date',
        'title',
        'bengali_date',
        'tithi',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Check booking status for a specific hall on this date.
     */
    public function getAvailabilityStatus($hallId = null): array
    {
        $hallId = $hallId ?? session('active_hall_id') ?? Hall::first()?->id;

        $items = BookingItem::where('hall_id', $hallId)
            ->whereDate('event_date', $this->date)
            ->whereHas('booking', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->get();

        if ($items->isEmpty()) {
            return [
                'status' => 'available',
                'label' => 'সম্পূর্ণ খালি',
                'badge_class' => 'bg-success',
                'day_available' => true,
                'night_available' => true,
                'items_count' => 0,
            ];
        }

        $hasFullDay = $items->contains(fn($i) => in_array($i->slot, ['full_day', 'all_day']));
        $hasDay = $items->contains(fn($i) => in_array($i->slot, ['day', 'morning']));
        $hasNight = $items->contains(fn($i) => in_array($i->slot, ['night', 'evening']));

        if ($hasFullDay || ($hasDay && $hasNight)) {
            return [
                'status' => 'booked',
                'label' => 'সম্পূর্ণ বুকড',
                'badge_class' => 'bg-danger',
                'day_available' => false,
                'night_available' => false,
                'items_count' => $items->count(),
            ];
        }

        return [
            'status' => 'partial',
            'label' => $hasDay ? 'রাতের স্লট খালি' : 'দিনের স্লট খালি',
            'badge_class' => 'bg-warning',
            'day_available' => !$hasDay,
            'night_available' => !$hasNight,
            'items_count' => $items->count(),
        ];
    }
}
