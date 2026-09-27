<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-payment-reminders')]
#[Description('Command description')]
class SendPaymentReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookings = \App\Models\Booking::where('status', 'confirmed')
            ->whereRaw('total_amount > advance_amount')
            ->whereHas('items', function($q) {
                $q->where('event_date', '<=', now()->addDays(3))
                  ->where('event_date', '>=', now());
            })
            ->with(['customer', 'items'])
            ->get();

        $this->info("Found " . $bookings->count() . " bookings for payment reminder.");

        $smsService = new \App\Services\SmsService();

        foreach ($bookings as $booking) {
            $tenant = $booking->tenant;
            $settings = $tenant->settings ?? [];

            if ($settings['auto_payment_reminder'] ?? false) {
                $due = $booking->total_amount - $booking->advance_amount;
                
                $template = $settings['sms_template_reminder'] ?? "প্রিয় [CustomerName], আপনার ইভেন্টের জন্য [DueAmount] টাকা বকেয়া আছে। অনুগ্রহ করে পরিশোধ করুন। ধন্যবাদ!";
                
                $message = $smsService->parseTemplate($template, [
                    'CustomerName' => $booking->customer->name,
                    'EventDate' => $booking->items->first()->event_date->format('d M, Y'),
                    'DueAmount' => number_format($due),
                ]);

                $smsService->send($tenant, $booking->customer->phone, $message, $booking->id);
                $this->info("Sent reminder to " . $booking->customer->name);
            }
        }
    }
}
