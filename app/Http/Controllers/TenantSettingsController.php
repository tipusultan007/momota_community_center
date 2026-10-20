<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantSettingsController extends Controller
{
    public function index()
    {
        $tenant = \App\Models\BusinessSetting::get();

        return view('tenants.settings', compact('tenant'));
    }

    public function update(Request $request)
    {
        $business = \App\Models\BusinessSetting::get();

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'invoice_conditions' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price_per_slot' => 'required|numeric|min:0',
            'default_server_rate' => 'required|numeric|min:0',
        ]);

        $businessData = [
            'company_name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'capacity' => $request->capacity,
            'price_per_slot' => $request->price_per_slot,
            'default_server_rate' => $request->default_server_rate,
            'invoice_conditions' => $request->invoice_conditions,
        ];

        // Notification Settings
        $currentSettings = $business->settings ?? [];
        $notificationSettings = [
            'auto_email_confirmation' => $request->has('auto_email_confirmation'),
            'auto_sms_confirmation' => $request->has('auto_sms_confirmation'),
            'auto_payment_reminder' => $request->has('auto_payment_reminder'),
            'auto_event_greeting' => $request->has('auto_event_greeting'),
            'sms_template_confirmation' => $request->sms_template_confirmation,
            'sms_template_reminder' => $request->sms_template_reminder,
            'sms_template_greeting' => $request->sms_template_greeting,
        ];
        $businessData['settings'] = array_merge($currentSettings, $notificationSettings);

        if ($request->hasFile('logo')) {
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }
            $businessData['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $business->update($businessData);

        return redirect()->back()->with('success', 'ব্যবসার তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }
}
