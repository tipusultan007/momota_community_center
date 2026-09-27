<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .business-info { margin-bottom: 20px; }
        .report-info { margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; color: #111827; }
        .income { color: #059669; }
        .expense { color: #dc2626; }
        .summary-box { background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .summary-item { margin-bottom: 10px; font-size: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <h3>{{ $tenant->name }}</h3>
    </div>

    <div class="business-info">
        <p>{{ $tenant->address }}</p>
        <p>Phone: {{ $tenant->phone }}</p>
    </div>

    <div class="report-info">
        <p>Period: {{ $start_date }} to {{ $end_date }}</p>
    </div>

    <h3>Income Entries</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
            <tr>
                <td>{{ $income->date }}</td>
                <td>{{ $income->category }}</td>
                <td>{{ $income->description }}</td>
                <td style="text-align: right;" class="income">{{ number_format($income->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Expense Entries</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->date }}</td>
                <td>{{ $expense->category }}</td>
                <td>{{ $expense->description }}</td>
                <td style="text-align: right;" class="expense">{{ number_format($expense->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-item"><strong>Total Income:</strong> <span class="income">{{ number_format($total_income, 2) }}</span></div>
        <div class="summary-item"><strong>Total Expense:</strong> <span class="expense">{{ number_format($total_expense, 2) }}</span></div>
        <div class="summary-item" style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px;">
            <strong>Net Profit:</strong> <span>{{ number_format($total_income - $total_expense, 2) }}</span>
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 12px; color: #6b7280; text-align: center;">
        Generated on {{ now()->toDateTimeString() }} by PranganHQ
    </div>
</body>
</html>
