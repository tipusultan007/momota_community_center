import 'salary.dart';

class Staff {
  final int id;
  final String name;
  final String designation;
  final String phone;
  final String? address;
  final double salaryAmount;
  final String? joinDate;
  final bool isActive;
  final String status;
  final List<Salary> salaries;

  Staff({
    required this.id,
    required this.name,
    required this.designation,
    required this.phone,
    this.address,
    required this.salaryAmount,
    this.joinDate,
    required this.isActive,
    required this.status,
    this.salaries = const [],
  });

  factory Staff.fromJson(Map<String, dynamic> json) {
    return Staff(
      id: _toInt(json['id']),
      name: json['name'] ?? 'N/A',
      designation: json['designation'] ?? json['role'] ?? 'Staff',
      phone: json['phone'] ?? 'N/A',
      address: json['address'],
      salaryAmount: _toDouble(json['salary_amount'] ?? 0),
      joinDate: json['join_date'],
      isActive: _toBool(json['is_active']),
      status: json['status'] ?? 'inactive',
      salaries: (json['salaries'] as List?)
          ?.map((s) => Salary.fromJson(Map<String, dynamic>.from(s)))
          .toList() ?? [],
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

  static bool _toBool(dynamic value) {
    if (value == null) return false;
    if (value is bool) return value;
    if (value is int) return value == 1;
    if (value is String) return value == '1' || value.toLowerCase() == 'true';
    return false;
  }
}
