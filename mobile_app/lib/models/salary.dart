class Salary {
  final int id;
  final int employeeId;
  final double amount;
  final String paymentDate;
  final String month;
  final int year;
  final String? notes;

  Salary({
    required this.id,
    required this.employeeId,
    required this.amount,
    required this.paymentDate,
    required this.month,
    required this.year,
    this.notes,
  });

  factory Salary.fromJson(Map<String, dynamic> json) {
    return Salary(
      id: _toInt(json['id']),
      employeeId: _toInt(json['employee_id']),
      amount: _toDouble(json['amount'] ?? 0),
      paymentDate: json['payment_date'],
      month: json['month'],
      year: _toInt(json['year']),
      notes: json['notes'],
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
}
