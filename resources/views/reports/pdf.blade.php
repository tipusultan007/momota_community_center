<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>চূড়ান্ত রিপোর্ট</title>
    <style>
        * {
            font-family: 'hind_siliguri', sans-serif;
        }
        
        body {
            font-family: 'hind_siliguri', sans-serif;
            margin: 0;
            padding: 20px;
            color: #1e293b;
            line-height: 1.4 !important;
        }
        
        table {
            border-collapse: collapse;
        }
        
        .hall-name {
            font-size: 26px;
            font-weight: bold;
            color: #2563eb;
            margin: 0 0 5px 0;
        }
        .hall-info {
            font-size: 14px;
            color: #64748b;
        }
        
        .report-badge {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }
        .date-range {
            font-size: 13px;
            color: #64748b;
            margin-top: 5px;
        }

        .stat-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
        }
        .stat-card.blue {
            background-color: #eff6ff;
        }
        .stat-label {
            font-size: 12px;
            font-weight: bold;
            color: #64748b;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .section-header {
            font-size: 18px;
            font-weight: bold;
            color: #334155;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 15px;
        }

        .data-table {
            width: 100%;
        }
        .data-table th {
            text-align: left;
            font-size: 14px;
            font-weight: bold;
            color: #475569;
            padding: 10px;
            background-color: #f1f5f9;
        }
        .data-table td {
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .profit-positive { color: #15803d; }
        .profit-negative { color: #b91c1c; }
    </style>
</head>
<body>
    @php
        $months = [
            'January' => 'জানুয়ারি', 'February' => 'ফেব্রুয়ারি', 'March' => 'মার্চ',
            'April' => 'এপ্রিল', 'May' => 'মে', 'June' => 'জুন',
            'July' => 'জুলাই', 'August' => 'আগস্ট', 'September' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর', 'November' => 'নভেম্বর', 'December' => 'ডিসেম্বর'
        ];
        
        $formatDate = function($date) use ($toBengali, $months) {
            $formatted = $date->format('d M, Y');
            foreach ($months as $en => $bn) {
                $formatted = str_replace($en, $bn, $formatted);
            }
            return $toBengali($formatted);
        };
    @endphp

    <!-- Header Table -->
    <table width="100%" style="border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 30px;">
        <tr>
            <td width="55%" valign="top">
                <div class="hall-name">{{ $activeHall->name ?? $tenant->name }}</div>
                <div class="hall-info">
                    {{ $activeHall->address ?? 'ঠিকানা উপলব্ধ নেই' }}<br>
                    ফোন: {{ $toBengali($activeHall->phone ?? 'নেই') }}
                </div>
            </td>
            <td width="45%" valign="top" align="right">
                <div class="report-badge">চূড়ান্ত হিসাব</div>
                <div class="report-title">মাসিক অডিট রিপোর্ট</div>
                <div class="date-range">
                    {{ $formatDate($startDate) }} — {{ $formatDate($endDate) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Stat Cards Table -->
    <table width="100%" style="margin-bottom: 40px;">
        <tr>
            <!-- Card 1 -->
            <td width="23%" valign="top" class="stat-card">
                <div class="stat-label">মোট বুকিং</div>
                <div class="stat-value">{{ $toBengali($bookingsCount) }} টি অনুষ্ঠান</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">মূল্য: ৳{{ $toBengali(number_format($bookingsTotalValue, 2)) }}</div>
            </td>
            <td width="2%"></td>
            <!-- Card 2 -->
            <td width="23%" valign="top" class="stat-card">
                <div class="stat-label">মোট আয়</div>
                <div class="stat-value" style="color: #2563eb;">৳{{ $toBengali(number_format($totalIncome, 2)) }}</div>
            </td>
            <td width="2%"></td>
            <!-- Card 3 -->
            <td width="23%" valign="top" class="stat-card">
                <div class="stat-label">মোট ব্যয়</div>
                <div class="stat-value" style="color: #b91c1c;">৳{{ $toBengali(number_format($totalExpense, 2)) }}</div>
            </td>
            <td width="2%"></td>
            <!-- Card 4 -->
            <td width="24%" valign="top" class="stat-card blue">
                <div class="stat-label">নীট লাভ/ক্ষতি</div>
                <div class="stat-value {{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    ৳{{ $toBengali(number_format($netProfit, 2)) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Tables -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 40px;">
        <tr>
            <td width="48%" valign="top">
                <div class="section-header">আয়ের বিবরণ (Income)</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ক্যাটাগরি</th>
                            <th class="text-right">পরিমাণ (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomeByCategory as $ic)
                        <tr>
                            <td>{{ $ic->incomeCategory->name ?? 'সরাসরি' }}</td>
                            <td class="text-right">৳{{ $toBengali(number_format($ic->total, 2)) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center" style="color: #94a3b8;">কোনো আয়ের তথ্য নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td width="4%"></td>
            <td width="48%" valign="top">
                <div class="section-header">ব্যয়ের বিবরণ (Expense)</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ক্যাটাগরি</th>
                            <th class="text-right">পরিমাণ (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenseByCategory as $ec)
                        <tr>
                            <td>{{ $ec->expenseCategory->name ?? 'সাধারণ' }}</td>
                            <td class="text-right">৳{{ $toBengali(number_format($ec->total, 2)) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center" style="color: #94a3b8;">কোনো ব্যয়ের তথ্য নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        এই রিপোর্টটি {{ $tenant->name ?? 'সিস্টেম' }} এর জন্য ইলেকট্রনিকভাবে তৈরি করা হয়েছে। 
        তারিখ: {{ $toBengali(now()->format('d/m/Y')) }} সময়: {{ $toBengali(now()->format('h:i A')) }}<br>
        উৎস: SaaS কনভেনশন হল ম্যানেজমেন্ট সিস্টেম
    </div>
</body>
</html>
