import 'booking_item.dart';

class Booking {
  final int id;
  final int customerId;
  final String customerName;
  final String customerPhone;
  final String? customerAddress;
  final double totalAmount;
  final double paidAmount;
  final String status;
  final String? notes;
  final List<BookingItem> items;
  final List<Map<String, dynamic>> payments;
  final String serverPayoutStatus;
  final String? serverPayoutDate;
  final double totalServerCost;
  final double excludedServerCost;
  final double allServerCost;

  Booking({
    required this.id,
    required this.customerId,
    required this.customerName,
    required this.customerPhone,
    this.customerAddress,
    required this.totalAmount,
    this.paidAmount = 0,
    required this.status,
    this.notes,
    required this.items,
    this.payments = const [],
    this.serverPayoutStatus = 'pending',
    this.serverPayoutDate,
    this.totalServerCost = 0,
    this.excludedServerCost = 0,
    this.allServerCost = 0,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    final parsedItems = (json['items'] as List?)?.map((i) => BookingItem.fromJson(Map<String, dynamic>.from(i))).toList() ?? [];
    return Booking(
      id: _toInt(json['id']),
      customerId: _toInt(json['customer_id']),
      customerName: json['customer']?['name'] ?? 'N/A',
      customerPhone: json['customer']?['phone'] ?? 'N/A',
      customerAddress: json['customer']?['address'],
      totalAmount: _toDouble(json['total_amount']),
      paidAmount: _toDouble(json['advance_amount'] ?? json['paid_amount']),
      status: json['status'] ?? 'pending',
      notes: json['notes'],
      items: parsedItems,
      payments: (json['incomes'] as List?)?.map((i) => Map<String, dynamic>.from(i)).toList() ?? [],
      serverPayoutStatus: json['server_payout_status']?.toString() ?? 'pending',
      serverPayoutDate: json['server_payout_date']?.toString(),
      totalServerCost: json['total_server_cost'] != null
          ? _toDouble(json['total_server_cost'])
          : parsedItems.where((i) => i.isServerIncluded).fold(0.0, (sum, i) => sum + (i.serverCount * i.serverRate)),
      excludedServerCost: json['excluded_server_cost'] != null
          ? _toDouble(json['excluded_server_cost'])
          : parsedItems.where((i) => !i.isServerIncluded).fold(0.0, (sum, i) => sum + (i.serverCount * i.serverRate)),
      allServerCost: json['all_server_cost'] != null
          ? _toDouble(json['all_server_cost'])
          : parsedItems.fold(0.0, (sum, i) => sum + (i.serverCount * i.serverRate)),
    );
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  static int _toInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  double get dueAmount => totalAmount - paidAmount;

  int get totalGuests => items.fold(0, (sum, item) => sum + item.guestCount);
  int get totalTables => items.fold(0, (sum, item) => sum + item.tableCount);
  int get totalServers => items.fold(0, (sum, item) => sum + item.serverCount);
}
