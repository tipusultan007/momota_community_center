<!DOCTYPE html>
<html lang="bn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>বুকিং ইনভয়েস #{{ $booking->id }}</title>
    <style>
        * {
            font-family: 'hind_siliguri', sans-serif;
        }
        body, td, th, div {
            font-family: 'hind_siliguri', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.8;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            padding: 30px;
            border: 1px solid #eee;
            background: #fff;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 15px;
        }
        .hall-name {
            font-size: 28px;
            font-weight: bold;
            color: #0056b3;
            margin: 0;
        }
        .hall-address {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        .section-title {
            background: #f8f9fa;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 4px solid #0056b3;
            margin: 20px 0 10px;
        }
        .customer-info, .booking-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #0056b3;
            color: #fff;
            padding: 10px;
            text-align: left;
            border: 1px solid #0056b3;
            font-family: 'hind_siliguri', sans-serif; /* Explicit for DomPDF th bug */
        }
        .items-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            font-family: 'hind_siliguri', sans-serif;
        }
        .summary-box {
            float: right;
            width: 250px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px;
            text-align: right;
        }
        .summary-table .label {
            text-align: left;
        }
        .summary-table .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        .in-words {
            font-style: italic;
            margin-top: 20px;
            padding: 10px;
            background: #fdfdfe;
            border: 1px dashed #ccc;
        }
        .terms {
            margin-top: 40px;
            font-size: 11px;
            color: #555;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 60px;
            width: 100%;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        /* Force strict line height for PDF to counteract hind_siliguri's huge baseline */
        p, div, td, th, h1, h2, h3, h4, span {
            line-height: 1.3 !important;
        }
        
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table width="100%">
                <tr>
                    <td style="width: 70%;">
                        @if($booking->tenant->logo_path)
                            <img src="{{ public_path('storage/' . $booking->tenant->logo_path) }}" alt="Logo" style="max-height: 80px; margin-bottom: 10px;">
                        @endif
                        <h1 class="hall-name">{{ $booking->hall->name ?? $booking->tenant->name }}</h1>
                        <p class="hall-address">{{ $booking->hall->address ?? 'কমিউনিটি সেন্টার রোড, ঢাকা, বাংলাদেশ' }}</p>
                        <p class="hall-address">মোবাইল: {{ $booking->hall->phone ?? '01xxxxxxxxx' }}</p>
                    </td>
                    <td style="width: 30%; text-align: right;">
                        <h2 class="invoice-title">ইনভয়েস / রশিদ</h2>
                        <p>ID: #BK-{{ \App\Helpers\AmountHelper::toBengaliNumber(str_pad($booking->id, 5, '0', STR_PAD_LEFT)) }}</p>
                        <p>তারিখ: {{ \App\Helpers\AmountHelper::toBengaliNumber(date('d/m/Y')) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">গ্রাহকের তথ্য</div>
        <table class="customer-info" width="100%">
            <tr>
                <td width="50%">
                    <strong>নাম:</strong> {{ $booking->customer->name }}<br>
                    <strong>মোবাইল:</strong> {{ \App\Helpers\AmountHelper::toBengaliNumber($booking->customer->phone) }}
                </td>
                <td width="50%">
                    <strong>ঠিকানা:</strong> {{ $booking->customer->address ?? 'প্রযোজ্য নয়' }}
                </td>
            </tr>
        </table>

        <div class="section-title">বুকিং বিবরণ</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="font-family: 'hind_siliguri', sans-serif;">তারিখ ও হল</th>
                    <th colspan="2" style="font-family: 'hind_siliguri', sans-serif; text-align: center;">বিবরণ</th>
                    <th style="text-align: right; font-family: 'hind_siliguri', sans-serif;">মোট (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->items as $item)
                    <tr> 
                        <td>
                            <strong>{{ \App\Helpers\AmountHelper::toBengaliNumber(\Carbon\Carbon::parse($item->event_date)->format('d/m/Y')) }}</strong><br>
                            <strong>অনুষ্ঠানের ধরনঃ</strong> 
                            @if($item->event_type == 'Wedding') বিবাহ
                            @elseif($item->event_type == 'Holud') হলুদ
                            @elseif($item->event_type == 'Birthday') জন্মদিন
                            @elseif($item->event_type == 'Corporate') কর্পোরেট
                            @else অন্যান্য @endif
                            <br>
                            <strong>সময়ঃ</strong> 
                            @if($item->slot == 'day' || $item->slot == 'morning') দিন (Day)
                            @elseif($item->slot == 'night' || $item->slot == 'evening') রাত (Night)
                            @else সারাদিন @endif
                        </td>
                        <td>
                            
                            <div style="font-size: 12px; margin-top: 5px;">
                                <ul>
                                    <li style="margin-bottom: 0px;">মেহমান: <strong>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->guest_count) }}</strong> জন</li>
                                    <li style="margin-bottom: 0px;">টেবিল: <strong>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->table_count) }}</strong> টি</li>
                                    <li style="margin-bottom: 0px;">সার্ভার: <strong>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->server_count) }}</strong> জন 
                                    (রেট: <strong>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->server_rate) }}</strong>৳)</li>
                                </ul>
                                
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 11px;">
                                • হল ভাড়া: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->base_price) }}</b> <br>
                                @if($item->server_count > 0)
                                    • পরিবেশন খরচ: ৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->server_count * $item->server_rate) }}</b>
                                    @if($item->is_server_included)
                                        <span style="font-size: 10px; color: #047857;">*(বিলে অন্তর্ভুক্ত)</span>
                                    @else
                                        <span style="font-size: 10px; color: #b91c1c;">*(বিলে অন্তর্ভুক্ত নয়)</span>
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
                                
                                @if(!($item->ac_price > 0 || $item->sound_price > 0 || $item->generator_price > 0 || $item->decoration_price > 0))
                                @endif
                            </div>
                        </td>
                        <td style="text-align: right;">৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($item->sub_total, 0) }}/=</b></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-box">   
            <table class="summary-table">
                <tr>
                    <td class="label">মোট বিল:</td>
                    <td>৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($booking->total_amount, 0) }}</b>/=</td>
                </tr>
                <tr>
                    <td class="label">মোট জমা:</td>
                    <td style="color: green;">৳ <b>{{ \App\Helpers\AmountHelper::toBengaliNumber($booking->advance_amount, 0) }}</b>/=</td>
                </tr>
                <tr class="grand-total">
                    <td class="label">বকেয়া:</td>
                    <td style="color: red;"><strong>৳ {{ \App\Helpers\AmountHelper::toBengaliNumber($booking->total_amount - $booking->advance_amount, 0) }}/=</strong></td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>

        @php
            $hasIncludedServers = $booking->items->contains(fn($i) => $i->server_count > 0 && $i->is_server_included);
            $hasExcludedServers = $booking->items->contains(fn($i) => $i->server_count > 0 && !$i->is_server_included);
        @endphp
        @if($hasIncludedServers || $hasExcludedServers)
            <div style="font-size: 11px; margin-top: 8px; margin-bottom: 8px; padding: 4px 8px; background-color: #f8fafc; border-left: 3px solid #64748b;">
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

        <div class="terms">
            <strong>শর্তাবলী:</strong>
            <div style="font-size: 11px;">
                @if($booking->tenant->invoice_conditions)
                    {!! nl2br(e($booking->tenant->invoice_conditions)) !!}
                @else
                    
                @endif
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box float-left">গ্রাহকের স্বাক্ষর</div>
            <div class="signature-box float-right">কর্তৃপক্ষের স্বাক্ষর</div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
