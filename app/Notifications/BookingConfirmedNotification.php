<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $tenant = $this->booking->tenant;
        $settings = $tenant->settings ?? [];
        $channels = [];

        if ($settings['auto_email_confirmation'] ?? true) {
            $channels[] = 'mail';
        }
        
        if ($settings['auto_sms_confirmation'] ?? false) {
            $channels[] = \App\Channels\SmsChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('বুকিং নিশ্চিতকরণ - ' . config('app.name'))
            ->greeting('প্রিয় ' . $this->booking->customer->name . ',')
            ->line('আপনার বুকিংটি সফলভাবে সম্পন্ন হয়েছে।')
            ->line('তারিখ: ' . $this->booking->items->first()->event_date->format('d M, Y'))
            ->line('মোট বিল: ৳ ' . number_format($this->booking->total_amount))
            ->line('অগ্রিম: ৳ ' . number_format($this->booking->advance_amount ?? 0))
            ->action('বিস্তারিত দেখুন', route('bookings.show', $this->booking->id))
            ->line('আমাদের বেছে নেওয়ার জন্য ধন্যবাদ!');
    }

    /**
     * Send SMS via custom logic (called manually or via custom channel)
     */
    public function toSms($notifiable)
    {
        $smsService = new \App\Services\SmsService();
        $tenant = $this->booking->tenant;
        $settings = $tenant->settings ?? [];
        
        $template = $settings['sms_template_confirmation'] ?? "Dear [CustomerName], your booking for [EventDate] is confirmed. Total: [TotalAmount]. Thanks!";
        
        $message = $smsService->parseTemplate($template, [
            'CustomerName' => $this->booking->customer->name,
            'EventDate' => $this->booking->items->first()->event_date->format('d M, Y'),
            'TotalAmount' => $this->booking->total_amount,
        ]);

        $smsService->send($tenant, $this->booking->customer->phone, $message, $this->booking->id);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
        ];
    }
}
