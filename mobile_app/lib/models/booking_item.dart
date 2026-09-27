import 'asset_assignment.dart';

class BookingItem {
  final int? id;
  final int hallId;
  final String hallName;
  final String eventDate;
  final String slot;
  final String eventType;
  final int guestCount;
  final int tableCount;
  final int serverCount;
  final double serverRate;
  final bool isServerIncluded;
  final bool isAc;
  final double acPrice;
  final bool extraSound;
  final double soundPrice;
  final bool extraGenerator;
  final double generatorPrice;
  final bool extraDecoration;
  final double decorationPrice;
  final int? decorationVendorId;
  final String? decorationVendorName;
  final int? soundVendorId;
  final String? soundVendorName;
  final int? generatorVendorId;
  final String? generatorVendorName;
  final double basePrice;
  final double subTotal;
  final List<AssetAssignment> assetAssignments;

  BookingItem({
    this.id,
    required this.hallId,
    this.hallName = '',
    required this.eventDate,
    required this.slot,
    this.eventType = 'বিয়ে (Wedding Reception)',
    this.guestCount = 0,
    this.tableCount = 0,
    this.serverCount = 0,
    this.serverRate = 0,
    this.isServerIncluded = true,
    this.isAc = false,
    this.acPrice = 0,
    this.extraSound = false,
    this.soundPrice = 0,
    this.extraGenerator = false,
    this.generatorPrice = 0,
    this.extraDecoration = false,
    this.decorationPrice = 0,
    this.decorationVendorId,
    this.decorationVendorName,
    this.soundVendorId,
    this.soundVendorName,
    this.generatorVendorId,
    this.generatorVendorName,
    required this.basePrice,
    required this.subTotal,
    this.assetAssignments = const [],
  });

  factory BookingItem.fromJson(Map<String, dynamic> json) {
    return BookingItem(
      id: _toInt(json['id']),
      hallId: _toInt(json['hall_id']),
      hallName: json['hall']?['name'] ?? 'N/A',
      eventDate: _parseDate(json['event_date']),
      slot: json['slot'] ?? 'day',
      eventType: json['event_type'] ?? 'বিয়ে (Wedding Reception)',
      guestCount: _toInt(json['guest_count']),
      tableCount: _toInt(json['table_count']),
      serverCount: _toInt(json['server_count']),
      serverRate: _toDouble(json['server_rate']),
      isServerIncluded: json['is_server_included'] != null ? _toBool(json['is_server_included']) : true,
      isAc: _toBool(json['is_ac']),
      acPrice: _toDouble(json['ac_price']),
      extraSound: _toBool(json['extra_sound']),
      soundPrice: _toDouble(json['sound_price']),
      extraGenerator: _toBool(json['extra_generator']),
      generatorPrice: _toDouble(json['generator_price']),
      extraDecoration: _toBool(json['extra_decoration']),
      decorationPrice: _toDouble(json['decoration_price']),
      decorationVendorId: _toInt(json['decoration_vendor_id']),
      decorationVendorName: json['decoration_vendor']?['name'],
      soundVendorId: _toInt(json['sound_vendor_id']),
      soundVendorName: json['sound_vendor']?['name'],
      generatorVendorId: _toInt(json['generator_vendor_id']),
      generatorVendorName: json['generator_vendor']?['name'],
      basePrice: _toDouble(json['base_price']),
      subTotal: _toDouble(json['sub_total']),
      assetAssignments: (json['asset_assignments'] as List?)
          ?.map((a) => AssetAssignment.fromJson(a))
          .toList() ?? [],
    );
  }

  BookingItem copyWith({
    int? id,
    int? hallId,
    String? hallName,
    String? eventDate,
    String? slot,
    String? eventType,
    int? guestCount,
    int? tableCount,
    int? serverCount,
    double? serverRate,
    bool? isServerIncluded,
    bool? isAc,
    double? acPrice,
    bool? extraSound,
    double? soundPrice,
    bool? extraGenerator,
    double? generatorPrice,
    bool? extraDecoration,
    double? decorationPrice,
    int? decorationVendorId,
    String? decorationVendorName,
    int? soundVendorId,
    String? soundVendorName,
    int? generatorVendorId,
    String? generatorVendorName,
    double? basePrice,
    double? subTotal,
    List<AssetAssignment>? assetAssignments,
  }) {
    return BookingItem(
      id: id ?? this.id,
      hallId: hallId ?? this.hallId,
      hallName: hallName ?? this.hallName,
      eventDate: eventDate ?? this.eventDate,
      slot: slot ?? this.slot,
      eventType: eventType ?? this.eventType,
      guestCount: guestCount ?? this.guestCount,
      tableCount: tableCount ?? this.tableCount,
      serverCount: serverCount ?? this.serverCount,
      serverRate: serverRate ?? this.serverRate,
      isServerIncluded: isServerIncluded ?? this.isServerIncluded,
      isAc: isAc ?? this.isAc,
      acPrice: acPrice ?? this.acPrice,
      extraSound: extraSound ?? this.extraSound,
      soundPrice: soundPrice ?? this.soundPrice,
      extraGenerator: extraGenerator ?? this.extraGenerator,
      generatorPrice: generatorPrice ?? this.generatorPrice,
      extraDecoration: extraDecoration ?? this.extraDecoration,
      decorationPrice: decorationPrice ?? this.decorationPrice,
      decorationVendorId: decorationVendorId ?? this.decorationVendorId,
      decorationVendorName: decorationVendorName ?? this.decorationVendorName,
      soundVendorId: soundVendorId ?? this.soundVendorId,
      soundVendorName: soundVendorName ?? this.soundVendorName,
      generatorVendorId: generatorVendorId ?? this.generatorVendorId,
      generatorVendorName: generatorVendorName ?? this.generatorVendorName,
      basePrice: basePrice ?? this.basePrice,
      subTotal: subTotal ?? this.subTotal,
      assetAssignments: assetAssignments ?? this.assetAssignments,
    );
  }

  static String _parseDate(dynamic value) {
    if (value == null) return '';
    String dateStr = value.toString();
    if (dateStr.contains(' ')) {
      return dateStr.split(' ')[0];
    }
    if (dateStr.contains('T')) {
      return dateStr.split('T')[0];
    }
    return dateStr;
  }

  Map<String, dynamic> toJson() {
    return {
      'hall_id': hallId,
      'event_date': eventDate,
      'slot': slot,
      'event_type': eventType,
      'guest_count': guestCount,
      'table_count': tableCount,
      'server_count': serverCount,
      'server_rate': serverRate,
      'is_server_included': isServerIncluded,
      'is_ac': isAc,
      'ac_price': acPrice,
      'extra_sound': extraSound,
      'sound_price': soundPrice,
      'sound_vendor_id': soundVendorId,
      'extra_generator': extraGenerator,
      'generator_price': generatorPrice,
      'generator_vendor_id': generatorVendorId,
      'extra_decoration': extraDecoration,
      'decoration_price': decorationPrice,
      'decoration_vendor_id': decorationVendorId,
      'base_price': basePrice,
      'sub_total': subTotal,
    };
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
