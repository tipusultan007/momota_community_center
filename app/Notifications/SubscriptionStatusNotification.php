<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionStatusNotification extends Notification
{
    use Queueable;

    protected $history;

    /**
     * Create a new notification instance.
     */
    public function __construct($history)
    {
        $this->history = $history;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Could also add 'sms' via SmsChannel if needed
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->history->status === 'active' ? 'অনুমোদিত' : 'বাতিল';
        $subject = 'সাবস্ক্রিপশন স্ট্যাটাস - ' . $status;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('প্রিয় ' . $this->history->tenant->name . ',')
            ->line('আপনার সাবস্ক্রিপশন পেমেন্ট রিকোয়েস্টটি ' . $status . ' করা হয়েছে।')
            ->line('প্ল্যান: ' . ucfirst($this->history->plan))
            ->line('অ্যামাউন্ট: ৳ ' . number_format($this->history->amount));

        if ($this->history->status === 'active') {
            $mail->line('আপনার সাবস্ক্রিপশন এখন অ্যাক্টিভ এবং মেয়াদ ' . $this->history->ends_at->format('d M, Y') . ' পর্যন্ত।');
        } else {
            $mail->line('বাতিল করার কারণ: ' . ($this->history->reject_reason ?? 'কারণ উল্লেখ করা হয়নি'));
        }

        return $mail->action('ড্যাশবোর্ড দেখুন', url('/dashboard'))
                    ->line('ধন্যবাদ!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
