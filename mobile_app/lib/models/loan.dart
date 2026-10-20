import 'loan_payment.dart';

class Loan {
  final int id;
  final String personName;
  final String? phone;
  final double amount;
  final double paidAmount;
  final double dueAmount;
  final String date;
  final String? dueDate;
  final String? purpose;
  final String status; // pending, partial, paid
  final String? notes;
  final List<LoanPayment> payments;

  Loan({
    required this.id,
    required this.personName,
    this.phone,
    required this.amount,
    required this.paidAmount,
    required this.dueAmount,
    required this.date,
    this.dueDate,
    this.purpose,
    required this.status,
    this.notes,
    this.payments = const [],
  });

  factory Loan.fromJson(Map<String, dynamic> json) {
    final amount = _toDouble(json['amount']);
    final paidAmount = _toDouble(json['paid_amount']);
    final dueAmount = json['due_amount'] != null
        ? _toDouble(json['due_amount'])
        : (amount - paidAmount > 0 ? amount - paidAmount : 0.0);

    final rawPayments = json['payments'];
    final List<LoanPayment> paymentsList = [];
    if (rawPayments is List) {
      for (var item in rawPayments) {
        if (item is Map<String, dynamic>) {
          paymentsList.add(LoanPayment.fromJson(item));
        }
      }
    }

    return Loan(
      id: int.tryParse(json['id']?.toString() ?? '') ?? 0,
      personName: json['person_name']?.toString() ?? '',
      phone: json['phone']?.toString(),
      amount: amount,
      paidAmount: paidAmount,
      dueAmount: dueAmount,
      date: json['date']?.toString() ?? '',
      dueDate: json['due_date']?.toString(),
      purpose: json['purpose']?.toString(),
      status: json['status']?.toString() ?? 'pending',
      notes: json['notes']?.toString(),
      payments: paymentsList,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'person_name': personName,
      'phone': phone,
      'amount': amount,
      'paid_amount': paidAmount,
      'date': date,
      'due_date': dueDate,
      'purpose': purpose,
      'status': status,
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
