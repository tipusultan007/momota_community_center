<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-event-greetings')]
#[Description('Command description')]
class SendEventGreetings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->format('Y-m-d');

        $bookings = \App\Models\Booking::where('status', 'confirmed')
            ->whereHas('items', function($q) use ($tomorrow) {
                $q->whereDate('event_date', $tomorrow);
            })
            ->with(['customer', 'items'])
            ->get();

        $this->info("Found " . $bookings->count() . " bookings for event greetings.");

        $smsService = new \App\Services\SmsService();

        foreach ($bookings as $booking) {
            $tenant = $booking->tenant;
            $settings = $tenant->settings ?? [];

            if ($settings['auto_event_greeting'] ?? false) {
                $template = $settings['sms_template_greeting'] ?? "প্রিয় [CustomerName], আগামীকাল আপনার ইভেন্টের জন্য আমরা প্রস্তুত। দেখা হবে ইনশাআল্লাহ! - [HallName]";
                
                $message = $smsService->parseTemplate($template, [
                    'CustomerName' => $booking->customer->name,
                    'EventDate' => $booking->items->first()->event_date->format('d M, Y'),
                    'HallName' => $booking->items->first()->hall->name ?? $tenant->name,
                ]);

                $smsService->send($tenant, $booking->customer->phone, $message, $booking->id);
                $this->info("Sent greeting to " . $booking->customer->name);
            }
        }
    }
}
