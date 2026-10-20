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
        'capacity',
        'price_per_slot',
        'default_server_rate',
        'invoice_conditions',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'capacity' => 'integer',
        'price_per_slot' => 'decimal:2',
        'default_server_rate' => 'decimal:2',
    ];

    protected $appends = ['name'];

    /**
     * Backward-compatibility alias: $setting->name maps to $setting->company_name
     */
    public function getNameAttribute(): string
    {
        return $this->company_name ?? 'মমতা কমিউনিটি সেন্টার';
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
            'company_name' => 'মমতা কমিউনিটি সেন্টার',
            'phone' => '01700000000',
            'address' => 'ঢাকা, বাংলাদেশ',
            'capacity' => 500,
            'price_per_slot' => 30000,
            'default_server_rate' => 500,
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
