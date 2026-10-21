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
            'phone' => '01829321686',
            'address' => 'দক্ষিণ কাট্টলী, প্রাণ হরি দাশ রোড, সমতা আবাসিক এলাকা, পাহাড়তলী, চট্টগ্রাম',
            'capacity' => 500,
            'price_per_slot' => 30000,
            'default_server_rate' => 500,
            'invoice_conditions' => implode("\n", [
                '১. বুকিং ও মূল্য পরিশোধ: ৫০% টাকা অগ্রিম দিতে হবে এবং বাকি টাকা অনুষ্ঠানের দিনেই (দিনের প্রোগ্রামের জন্য বিকাল ৪:০০ টা এবং রাতের জন্য রাত ১০:০০ টার মধ্যে) পরিশোধ করা বাধ্যতামূলক।',
                '২. বাতিল ও তারিখ পরিবর্তন: বাতিলকৃত অগ্রিম টাকা অফেরতযোগ্য; তারিখ পরিবর্তন হলের ফাঁকা থাকা ও কর্তৃপক্ষের অনুমতি সাপেক্ষ।',
                '৩. নির্ধারিত সময়সীমা: দিন (সকাল ১১:০০ - বিকাল ৪:০০) ও রাত (সন্ধ্যা ৬:৩০ - রাত ১২:০০) এর অতিরিক্ত সময়ের জন্য প্রতি ঘণ্টায় অতিরিক্ত চার্জ প্রযোজ্য হবে।',
                '৪. ক্ষতিপূরণ: অনুষ্ঠান চলাকালীন হলের সরঞ্জাম বা সম্পত্তির কোনো ক্ষতি হলে গ্রাহক তার সম্পূর্ণ জরিমানা দিতে বাধ্য থাকবেন।',
                '৫. বিদ্যুৎ বিভ্রাট: সাধারণ বিদ্যুৎ বিভ্রাটে হলের নিয়ম অনুযায়ী জেনারেটর ব্যাকআপ প্রদান করা হবে; তবে উচ্চমাত্রার লাইটিংয়ের ক্ষেত্রে আলাদা জ্বালানি খরচ প্রযোজ্য।',
                '৬. অতিরিক্ত টেবিল ও বয় চার্জ: নির্ধারিত টেবিল সংখ্যা থেকে পরে বাড়াতে চাইলে টেবিল প্রতি ১,০০০ টাকা এবং বয় চার্জ বাবদ ৫০০ টাকা যোগ হবে।',
                '৭. নিরাপত্তা ও শৃঙ্খলা: পটকা, আগ্নেয়াস্ত্র, মাদক ও অসামাজিক কার্যকলাপ নিষিদ্ধ এবং ব্যক্তিগত মালামাল হারানোর দায় কর্তৃপক্ষের নয়।',
                '৮. অনিবার্য পরিস্থিতি: প্রাকৃতিক দুর্যোগ বা সরকারি কোনো নিষেধাজ্ঞার কারণে অনুষ্ঠান স্থগিত হলে সমঝোতার মাধ্যমে পরবর্তী সিদ্ধান্ত হবে।',
                '৯. হলে প্রবেশের সময়সূচী: দিনের অনুষ্ঠানের জন্য আগের দিন রাত ১০:০০ টার পর এবং রাতের অনুষ্ঠানের জন্য অনুষ্ঠানের দিন সকাল ৮:০০ টার পর ক্লাবে প্রবেশ করা যাবে।',
                '১০. জেনারেটর চার্জ: প্রথম ১ ঘণ্টা জেনারেটর ব্যবহার সম্পূর্ণ ফ্রি এবং পরবর্তী প্রতি ঘণ্টার জন্য ১,০০০ টাকা চার্জ প্রযোজ্য হবে।',
            ]),
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
