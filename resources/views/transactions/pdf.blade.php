<!DOCTYPE html>
<html lang="bn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transaction Report - {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</title>
    <style>
        * {
            font-family: 'hind_siliguri', sans-serif;
        }
        body, td, th, div {
            font-family: 'hind_siliguri', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.4 !important;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
            margin: 0 0 5px 0;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #0056b3;
            color: #fff;
            padding: 8px;
            text-align: left;
            border: 1px solid #fff;
        }
        .items-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .fw-bold {
            font-weight: bold;
        }
        .bg-light {
            background-color: #f8f9fa;
        }
        .items-table th.text-right{
            text-align: right!important;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1 class="title">মাসিক লেনদেন রিপোর্ট (Cashbook)</h1>
            <p class="subtitle">
                তারিখ: {{ $startDate->format('d M, Y') }} থেকে {{ $endDate->format('d M, Y') }}<br>
                @if($type)
                    ধরণ: 
                    @if($type == 'income') আয় (Income) 
                    @elseif($type == 'expense') ব্যয় (Expense)
                    @elseif($type == 'salary') বেতন (Salary)
                    @elseif($type == 'commission') কমিশন (Commission)
                    @endif
                @endif
            </p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 15%">তারিখ</th>
                    <th style="width: 15%">ধরণ</th>
                    <th style="width: 40%">বিবরণ</th>
                    <th style="width: 15%" class="text-right">আয় (In)</th>
                    <th style="width: 15%" class="text-right">ব্যয় (Out)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-light">
                    <td colspan="3" class="text-right fw-bold">প্রারম্ভিক জের (Opening Balance)</td>
                    <td colspan="2" class="text-right fw-bold">
                        ৳ {{ number_format($openingBalance, 0) }}
                    </td>
                </tr>
                
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                        <td>
                            @if($transaction->type == 'income')
                                আয়
                            @elseif($transaction->type == 'expense')
                                ব্যয়
                            @elseif($transaction->type == 'salary')
                                বেতন
                            @elseif($transaction->type == 'commission')
                                কমিশন
                            @endif
                        </td>
                        <td>
                            {{ $transaction->description ?: 'বিবরণ নেই' }}
                            @if($transaction->type == 'income' && $transaction->transactionable && isset($transaction->transactionable->booking))
                                (বুকিং #{{ $transaction->transactionable->booking->id }})
                            @endif
                        </td>
                        <td class="text-right">
                            @if($transaction->type == 'income')
                                {{ number_format($transaction->amount, 0) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if(in_array($transaction->type, ['expense', 'salary', 'commission']))
                                {{ number_format($transaction->amount, 0) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">এই সময়ে কোনো লেনদেন পাওয়া যায়নি।</td>
                    </tr>
                @endforelse

                <tr class="bg-light">
                    <td colspan="3" class="text-right fw-bold">মোট (Period Total)</td>
                    <td class="text-right fw-bold">{{ number_format($periodIncomes, 0) }}</td>
                    <td class="text-right fw-bold">{{ number_format($periodExpenses, 0) }}</td>
                </tr>

                <tr class="bg-light">
                    <td colspan="3" class="text-right fw-bold" style="font-size: 16px;">সমাপনী জের (Closing Balance)</td>
                    <td colspan="2" class="text-right fw-bold" style="font-size: 16px;">
                        ৳ {{ number_format($closingBalance, 0) }}
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; font-size: 11px; text-align: center; color: #777;">
            জেনারেট করা হয়েছে: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
        </div>
    </div>
</body>
</html>
