<?php

namespace App\Traits;

use App\Models\Commission;
use App\Models\Vendor;

trait HandlesCommissions
{
    /**
     * Records commissions and payouts for vendor services in a booking item.
     *
     * @param \App\Models\Booking $booking
     * @param array $itemData
     * @return void
     */
    protected function recordCommissions($booking, $itemData)
    {
        $services = [
            'decoration' => ['vendor_id' => 'decoration_vendor_id', 'price' => 'decoration_price'],
            'sound' => ['vendor_id' => 'sound_vendor_id', 'price' => 'sound_price'],
            'generator' => ['vendor_id' => 'generator_vendor_id', 'price' => 'generator_price'],
        ];

        foreach ($services as $type => $fields) {
            $vendorId = $itemData[$fields['vendor_id']] ?? null;
            $price = $itemData[$fields['price']] ?? 0;

            if ($vendorId && $price > 0) {
                $vendor = Vendor::find($vendorId);
                if ($vendor && $vendor->commission_rate > 0) {
                    $commissionAmount = ($price * $vendor->commission_rate) / 100;
                    $netPayout = $price - $commissionAmount;

                    Commission::create([
                        'vendor_id' => $vendorId,
                        'booking_id' => $booking->id,
                        'amount' => $commissionAmount,
                        'net_payout' => $netPayout,
                        'payout_status' => 'pending',
                        'status' => 'pending',
                        'notes' => ucfirst($type) . " service commission for booking #{$booking->id}",
                    ]);
                }
            }
        }
    }
}
