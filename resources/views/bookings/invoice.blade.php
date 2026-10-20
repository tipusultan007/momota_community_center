<!DOCTYPE html>
<html lang="bn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>বুকিং ইনভয়েস #{{ $booking->id }}</title>
    <style>
        @page {
            margin: 6mm 8mm 6mm 8mm;
        }
        * {
            font-family: 'solaiman_lipi', sans-serif;
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'solaiman_lipi', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.35;
        }
        .invoice-box {
            padding: 10px 14px;
            background: #fff;
        }
        .header {
            margin-bottom: 6px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 6px;
        }
        .venue-name {
            font-size: 18px;
            font-weight: bold;
            color: #0056b3;
            margin: 0;
            line-height: 1.2;
        }
        .venue-address {
            font-size: 10.5px;
            color: #475569;
            margin: 1px 0;
            line-height: 1.25;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px;
            line-height: 1.1;
        }
        .invoice-meta {
            font-size: 10.5px;
            color: #334155;
            margin: 1px 0;
            line-height: 1.2;
        }

        /* Customer info compact bar */
        .customer-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .customer-header-cell {
            background: #0056b3;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            padding: 4px 6px;
            width: 12%;
            text-align: center;
            border-right: 1px solid #0056b3;
            vertical-align: middle;
        }
        .customer-data-cell {
            padding: 4px 8px;
            font-size: 11px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #1e293b;
        }

        /* Section Title */
        .section-title {
            background: #f1f5f9;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 11px;
            border-left: 3px solid #0056b3;
            margin: 4px 0 3px;
            color: #0f172a;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .items-table th {
            background: #0056b3;
            color: #fff;
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #0056b3;
            font-size: 10.5px;
            font-family: 'solaiman_lipi', sans-serif;
        }
        .items-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            font-family: 'solaiman_lipi', sans-serif;
            vertical-align: top;
            line-height: 1.3;
        }
        .items-table ul {
            margin: 1px 0;
            padding-left: 12px;
        }

        /* Summary */
        .summary-box {
            float: right;
            width: 220px;
            margin-top: 2px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 2px 4px;
            text-align: right;
            font-size: 11px;
        }
        .summary-table .label {
            text-align: left;
        }
        .summary-table .grand-total {
            font-size: 12.5px;
            font-weight: bold;
            border-top: 1px solid #0f172a;
            padding-top: 2px;
        }

        .clear { clear: both; }

        /* Notes & In Words */
        .special-note {
            font-size: 9.5px;
            margin: 3px 0;
            padding: 2px 6px;
            background-color: #f8fafc;
            border-left: 3px solid #64748b;
            line-height: 1.25;
        }
        .in-words {
            font-style: italic;
            margin-top: 3px;
            padding: 3px 6px;
            background: #fdfdfe;
            border: 1px dashed #cbd5e1;
            font-size: 9.5px;
            line-height: 1.3;
        }

        /* Terms & Conditions */
        .terms {
            margin-top: 4px;
            border-top: 1px solid #e2e8f0;
            padding-top: 2px;
        }
        .terms-title {
            font-weight: bold;
            font-size: 9.5px;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .terms-content {
            font-size: 8px;
            line-height: 1.2;
            color: #475569;
        }

        /* Signatures */
        .signature-section {
            margin-top: 18px;
            width: 100%;
        }
        .signature-box {
            width: 35%;
            text-align: center;
            border-top: 1px solid #0f172a;
            padding-top: 2px;
            font-size: 10px;
        }
        .float-left { float: left; }
        .float-right { float: right; }

        /* Strict line height override for Bengali font baseline */
        p, div, td, th, h1, h2, h3, h4, span {
            line-height: 1.25 !important;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Optimized Header with Side-by-Side Logo and Venue Info -->
        <div class="header">
            <table width="100%" style="border-collapse: collapse;">
                <tr>
                    <td style="width: 72%; vertical-align: middle;">
                        <table style="border-collapse: collapse;">
                            <tr>
                                @php
                                    $logoPath = $booking->tenant->logo_path ?? null;
                                @endphp
                                @if($logoPath && file_exists(public_path('storage/' . $logoPath)))
                                    <td style="vertical-align: middle; padding-right: 10px; width: 62px;">
                                        <img src="{{ public_path('storage/' . $logoPath) }}" alt="Logo" style="max-height: 60px; max-width: 62px; object-fit: contain;">
                                    </td>
                                @endif
                                <td style="vertical-align: middle;">
                                    <h1 class="venue-name">{{ $booking->tenant->name }}</h1>
                                    <p class="venue-address">{{ $booking->tenant->address ?: 'কমিউনিটি সেন্টার রোড, ঢাকা, বাংলাদেশ' }}</p>
                                    <p class="venue-address">মোবাইল: {{ $booking->tenant->phone ?: '01xxxxxxxxx' }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 28%; text-align: right; vertical-align: top;">
                        <h2 class="invoice-title">রশিদ</h2>
                        <p class="invoice-meta">ID: #BK-{{ \App\Helpers\AmountHelper::toBengaliDigits(str_pad($booking->id, 5, '0', STR_PAD_LEFT)) }}</p>
                        <p class="invoice-meta">তারিখ: {{ \App\Helpers\AmountHelper::toBengaliDigits(date('d/m/Y')) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Optimized Customer Info in a Single Compact Row -->
        <table class="customer-table" width="100%">
            <tr>
                <td class="customer-header-cell">গ্রাহকের তথ্য</td>
                <td class="customer-data-cell" style="width: 32%;">
                    <strong>নাম:</strong> {{ $booking->customer->name }}
                </td>
                <td class="customer-data-cell" style="width: 26%;">
                    <strong>মোবাইল:</strong> {{ \App\Helpers\AmountHelper::toBengaliDigits($booking->customer->phone) }}
                </td>
                <td class="customer-data-cell" style="width: 30%; border-right: none;">
                    <strong>ঠিকানা:</strong> {{ $booking->customer->address ?? 'প্রযোজ্য নয়' }}
                </td>
            </tr>
        </table>

        <!-- Booking Details -->
        <div class="section-title">বুকিং বিবরণ</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 28%;">তারিখ ও হল</th>
                    <th colspan="2" style="text-align: center;">বিবরণ</th>
                    <th style="text-align: right; width: 18%;">মোট (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->items as $item)
                    <tr> 
                        <td>
                            <strong>{{ \App\Helpers\AmountHelper::toBengaliDigits(\Carbon\Carbon::parse($item->event_date)->format('d/m/Y')) }}</strong><br>
                            <strong>অনুষ্ঠান:</strong> 
                            @if($item->event_type == 'Wedding') বিবাহ
                            @elseif($item->event_type == 'Holud') গায়ে হলুদ
                            @elseif($item->event_type == 'Birthday') জন্মদিন
                            @elseif($item->event_type == 'Corporate') কর্পোরেট
                            @else {{ $item->event_type ?? 'অন্যান্য' }} @endif
                            <br>
                            <strong>সময়:</strong> 
                            @if($item->slot == 'day' || $item->slot == 'morning') দিন (Day)
                            @elseif($item->slot == 'night' || $item->slot == 'evening') রাত (Night)
                            @else সারাদিন @endif
                        </td>
                        <td style="width: 34%;">
                            <div>
                                <ul>
                                    <li>মেহমানঃ <strong>{{ \App\Helpers\AmountHelper::toBengaliDigits($item->guest_count) }}</strong> জন</li>
                                    <li>টেবিলঃ <strong>{{ \App\Helpers\AmountHelper::toBengaliDigits($item->table_count) }}</strong> টি</li>
                                    <li>পরিবেশনকারীঃ <strong>{{ \App\Helpers\AmountHelper::toBengaliDigits($item->server_count) }}</strong> জন 
                                    (রেটঃ <strong>{{ \App\Helpers\AmountHelper::toBengaliDigits($item->server_rate) }}</strong>৳)</li>
                                </ul>
                            </div>
                        </td>
                        <td style="width: 20%;">
                            <div>
                                • হল ভাড়া: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->base_price) }}</b> <br>
                                @if($item->server_count > 0)
                                    • পরিবেশন: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->server_count * $item->server_rate) }}</b>
                                    @if($item->is_server_included)
                                        <span style="font-size: 8.5px; color: #047857;">*(অন্তর্ভুক্ত)</span>
                                    @else
                                        <span style="font-size: 8.5px; color: #b91c1c;">*(অন্তর্ভুক্ত নয়)</span>
                                    @endif
                                    <br>
                                @endif
                                @if($item->ac_price > 0) 
                                    • এসি: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->ac_price) }}</b> <br>
                                @endif
                                @if($item->sound_price > 0) 
                                    • সাউন্ড: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->sound_price) }}</b> <br>
                                @endif
                                @if($item->generator_price > 0) 
                                    • জেনারেটর: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->generator_price) }}</b> <br>
                                @endif
                                @if($item->decoration_price > 0) 
                                    • ডেকোরেশন: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->decoration_price) }}</b>
                                @endif
                            </div>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->sub_total, 0) }}/=</b>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-box">   
            <table class="summary-table">
                <tr>
                    <td class="label">মোট বিল:</td>
                    <td>৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($booking->total_amount, 0) }}</b>/=</td>
                </tr>
                <tr>
                    <td class="label">মোট জমা:</td>
                    <td style="color: #15803d;">৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($booking->advance_amount, 0) }}</b>/=</td>
                </tr>
                <tr class="grand-total">
                    <td class="label">বকেয়া:</td>
                    <td style="color: #b91c1c;">৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($booking->total_amount - $booking->advance_amount, 0) }}</b>/=</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>

        @php
            $hasIncludedServers = $booking->items->contains(fn($i) => $i->server_count > 0 && $i->is_server_included);
            $hasExcludedServers = $booking->items->contains(fn($i) => $i->server_count > 0 && !$i->is_server_included);
        @endphp
        @if($hasIncludedServers || $hasExcludedServers)
            <div class="special-note">
                @if($hasIncludedServers && $hasExcludedServers)
                    <strong>* বিশেষ দ্রষ্টব্য:</strong> নির্দিষ্ট স্লটে পরিবেশন খরচ মোট বিলের অন্তর্ভুক্ত এবং অন্যান্য স্লটে পরিবেশন খরচ বিলে অন্তর্ভুক্ত নয় (গ্রাহক সরাসরি প্রদেয়)।
                @elseif($hasIncludedServers)
                    <strong>* বিশেষ দ্রষ্টব্য:</strong> পরিবেশন খরচ মোট বুকিং বিলের অন্তর্ভুক্ত।
                @else
                    <strong>* বিশেষ দ্রষ্টব্য:</strong> পরিবেশন খরচ মোট বুকিং বিলের অন্তর্ভুক্ত নয় (গ্রাহক কর্তৃক সরাসরি পরিবেশনকারীদের প্রদেয়)।
                @endif
            </div>
        @endif

        <div class="in-words">
            <strong>কথায় (English):</strong> {{ strtoupper(\App\Helpers\AmountHelper::toWords($booking->total_amount)) }} Taka Only.<br>
            <strong>কথায় (বাংলা):</strong> {{ \App\Helpers\AmountHelper::toBengaliWords($booking->total_amount) }} টাকা মাত্র।
        </div>

        <!-- Optimized Terms & Conditions -->
        <div class="terms">
            <div class="terms-title">শর্তাবলী:</div>
            <div class="terms-content">
                @if($booking->tenant->invoice_conditions)
                    {!! nl2br(e($booking->tenant->invoice_conditions)) !!}
                @endif
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box float-left">গ্রাহকের স্বাক্ষর</div>
            <div class="signature-box float-right">কর্তৃপক্ষের স্বাক্ষর</div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
