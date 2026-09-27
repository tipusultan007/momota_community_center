class Transaction {
  final int id;
  final String type; // income, expense
  final int categoryId;
  final String categoryName;
  final double amount;
  final String date;
  final String? description;
  final int? hallId;
  final int? bookingId;

  Transaction({
    required this.id,
    required this.type,
    required this.categoryId,
    required this.categoryName,
    required this.amount,
    required this.date,
    this.description,
    this.hallId,
    this.bookingId,
  });

  factory Transaction.fromJson(Map<String, dynamic> json, [String? explicitType]) {
    final type = (explicitType ?? json['type']?.toString().toLowerCase()) ?? 'expense';
    final isIncome = type == 'income';
    final catKey = isIncome ? 'income_category' : 'expense_category';
    final catIdKey = isIncome ? 'income_category_id' : 'expense_category_id';
    
    String catName = 'N/A';
    if (json[catKey] != null && json[catKey] is Map && json[catKey]['name'] != null) {
      catName = json[catKey]['name'].toString();
    } else if (json['category'] != null && json['category'].toString().isNotEmpty) {
      catName = json['category'].toString();
    }
    
    final catId = int.tryParse(json[catIdKey]?.toString() ?? '') ??
        int.tryParse(json['category_id']?.toString() ?? '') ??
        0;

    return Transaction(
      id: int.tryParse(json['id']?.toString() ?? '') ?? 0,
      type: type,
      categoryId: catId,
      categoryName: catName,
      amount: _toDouble(json['amount']),
      date: json['date']?.toString() ?? 'N/A',
      description: json['description']?.toString(),
      hallId: int.tryParse(json['hall_id']?.toString() ?? ''),
      bookingId: int.tryParse(json['booking_id']?.toString() ?? ''),
    );
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }
}

class TransactionCategory {
  final int id;
  final String name;
  final String? description;
  final String type; // income, expense

  TransactionCategory({
    required this.id,
    required this.name,
    this.description,
    required this.type,
  });

  factory TransactionCategory.fromJson(Map<String, dynamic> json, String type) {
    return TransactionCategory(
      id: int.tryParse(json['id']?.toString() ?? '') ?? 0,
      name: json['name']?.toString() ?? 'N/A',
      description: json['description']?.toString(),
      type: type,
    );
  }
}
