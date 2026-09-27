import 'asset.dart';

class AssetAssignment {
  final int id;
  final int assetId;
  final String assetName;
  final int quantityOut;
  final int quantityIn;
  final int damagedQuantity;
  final String? notes;
  final Asset? asset;

  AssetAssignment({
    required this.id,
    required this.assetId,
    required this.assetName,
    required this.quantityOut,
    required this.quantityIn,
    required this.damagedQuantity,
    this.notes,
    this.asset,
  });

  factory AssetAssignment.fromJson(Map<String, dynamic> json) {
    return AssetAssignment(
      id: _toInt(json['id']),
      assetId: _toInt(json['asset_id']),
      assetName: json['asset']?['name'] ?? 'N/A',
      quantityOut: _toInt(json['quantity_out']),
      quantityIn: _toInt(json['quantity_in']),
      damagedQuantity: _toInt(json['damaged_quantity']),
      notes: json['notes'],
      asset: json['asset'] != null ? Asset.fromJson(json['asset']) : null,
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
