<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewTenantNotification extends Notification
{
    use Queueable;

    protected $tenant;

    /**
     * Create a new notification instance.
     */
    public function __construct($tenant)
    {
        $this->tenant = $tenant;
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('নতুন ভেন্ডর রেজিস্ট্রেসন - ' . config('app.name'))
            ->greeting('হ্যালো এডমিন,')
            ->line('সিস্টেমে একটি নতুন কনভেনশন হল রেজিস্টার করেছে।')
            ->line('ভেন্ডরের নাম: ' . $this->tenant->name)
            ->line('তারিখ: ' . $this->tenant->created_at->format('d M, Y'))
            ->action('ইউজার ডিটেইলস দেখুন', url('/admin/tenants/' . $this->tenant->id))
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
