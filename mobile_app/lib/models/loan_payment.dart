class LoanPayment {
  final int id;
  final int loanId;
  final double amount;
  final String paymentDate;
  final String paymentMethod;
  final String? notes;
  final String? createdAt;

  LoanPayment({
    required this.id,
    required this.loanId,
    required this.amount,
    required this.paymentDate,
    required this.paymentMethod,
    this.notes,
    this.createdAt,
  });

  factory LoanPayment.fromJson(Map<String, dynamic> json) {
    return LoanPayment(
      id: int.tryParse(json['id']?.toString() ?? '') ?? 0,
      loanId: int.tryParse(json['loan_id']?.toString() ?? '') ?? 0,
      amount: _toDouble(json['amount']),
      paymentDate: json['payment_date']?.toString() ?? '',
      paymentMethod: json['payment_method']?.toString() ?? 'Cash',
      notes: json['notes']?.toString(),
      createdAt: json['created_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'loan_id': loanId,
      'amount': amount,
      'payment_date': paymentDate,
      'payment_method': paymentMethod,
      'notes': notes,
    };
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }
}
