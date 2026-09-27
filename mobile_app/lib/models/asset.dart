class Asset {
  final int id;
  final String name;
  final int totalStock;
  final int availableStock;
  final String? description;

  Asset({
    required this.id,
    required this.name,
    required this.totalStock,
    required this.availableStock,
    this.description,
  });

  factory Asset.fromJson(Map<String, dynamic> json) {
    return Asset(
      id: _toInt(json['id']),
      name: json['name'] ?? '',
      totalStock: _toInt(json['total_stock']),
      availableStock: _toInt(json['available_stock']),
      description: json['description'],
    );
  }

  static int _toInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }
}
