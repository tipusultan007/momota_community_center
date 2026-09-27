<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bookings Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .business-info { margin-bottom: 20px; }
        .report-info { margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; color: #111827; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .status-confirmed { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bookings Report</h1>
        <h3>{{ $tenant->name }}</h3>
    </div>

    <div class="business-info">
        <p>{{ $tenant->address }}</p>
        <p>Phone: {{ $tenant->phone }}</p>
    </div>

    <div class="report-info">
        <p>Period: {{ $start_date }} to {{ $end_date }}</p>
    </div>

    <h3>Booking Details</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Hall</th>
                <th>Event Type</th>
                <th>Guests</th>
                <th>Status</th>
                <th style="text-align: right;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->booking_date }}</td>
                <td>{{ $booking->customer_name }}</td>
                <td>{{ $booking->hall->name ?? 'N/A' }}</td>
                <td>{{ $booking->event_type }}</td>
                <td>{{ $booking->total_guests }}</td>
                <td>
                    <span class="status status-{{ $booking->status }}">
                        {{ strtoupper($booking->status) }}
                    </span>
                </td>
                <td style="text-align: right;">{{ number_format($booking->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; font-size: 12px; color: #6b7280; text-align: center;">
        Generated on {{ now()->toDateTimeString() }} by PranganHQ
    </div>
</body>
</html>
