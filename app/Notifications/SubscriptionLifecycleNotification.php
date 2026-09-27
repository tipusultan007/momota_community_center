<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionLifecycleNotification extends Notification
{
    use Queueable;

    protected $tenant;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($tenant, $type)
    {
        $this->tenant = $tenant;
        $this->type = $type; // 'warning' or 'expired'
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        if ($this->type === 'warning') {
            return (new MailMessage)
                ->subject('সাবস্ক্রিপশন শেষ হতে চলেছে - ' . config('app.name'))
                ->greeting('প্রিয় ' . $this->tenant->name . ',')
                ->line('আপনার সাবস্ক্রিপশন আগামী ' . $this->tenant->subscription_ends_at->format('d M, Y') . ' তারিখে শেষ হয়ে যাবে।')
                ->line('অনুগ্রহ করে সময়মতো রিনিউ করুন যাতে আপনার সার্ভিসগুলো চালু থাকে।')
                ->action('পেমেন্ট করুন', url('/subscriptions'))
                ->line('সব সময় পাশে থাকার জন্য ধন্যবাদ!');
        }

        return (new MailMessage)
            ->subject('সাবস্ক্রিপশন শেষ হয়েছে - ' . config('app.name'))
            ->greeting('প্রিয় ' . $this->tenant->name . ',')
            ->line('আপনার সাবস্ক্রিপশন ইতিমধ্যেই শেষ হয়ে গেছে।')
            ->line('আপনার অ্যাকাউন্টের প্রিমিয়াম ফিচারগুলো এখন আর সচল নেই।')
            ->line('অব্যাহত ব্যবহারের জন্য সাবস্ক্রিপশনটি রিনিউ করুন।')
            ->action('সাবস্ক্রাইব করুন', url('/subscriptions'))
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
