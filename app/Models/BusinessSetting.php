<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'company_name',
        'slug',
        'address',
        'phone',
        'email',
        'logo_path',
        'invoice_conditions',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Backward-compatibility alias: $setting->name maps to $setting->company_name
     */
    public function getNameAttribute(): string
    {
        return $this->company_name ?? 'কনভেনশন হল';
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['company_name'] = $value;
    }

    /**
     * Get or create the single instance of business settings.
     */
    public static function get(): self
    {
        return static::firstOrCreate([], [
            'company_name' => 'কনভেনশন হল ম্যানেজমেন্ট',
            'phone' => '01700000000',
            'address' => 'ঢাকা, বাংলাদেশ',
            'invoice_conditions' => "১. বুকিং নিশ্চিত করার জন্য অগ্রিম অর্থ প্রদান বাধ্যতামূলক।\n২. অনুষ্ঠান শুরুর পূর্বে সম্পূর্ণ বিল পরিশোধ করতে হবে।",
            'settings' => [
                'sms_enabled' => false,
                'auto_email_confirmation' => true,
                'auto_sms_confirmation' => false,
                'auto_payment_reminder' => false,
                'auto_event_greeting' => false,
            ],
        ]);
    }
}
