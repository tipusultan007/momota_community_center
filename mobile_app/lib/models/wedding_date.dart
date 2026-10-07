import '../utils/bangla_date_helper.dart';

class WeddingDateAvailability {
  final String status;
  final String label;
  final String badgeClass;
  final bool dayAvailable;
  final bool nightAvailable;
  final int itemsCount;

  WeddingDateAvailability({
    required this.status,
    required this.label,
    required this.badgeClass,
    required this.dayAvailable,
    required this.nightAvailable,
    required this.itemsCount,
  });

  factory WeddingDateAvailability.fromJson(Map<String, dynamic>? json) {
    if (json == null) {
      return WeddingDateAvailability(
        status: 'available',
        label: 'সম্পূর্ণ খালি',
        badgeClass: 'bg-success',
        dayAvailable: true,
        nightAvailable: true,
        itemsCount: 0,
      );
    }
    return WeddingDateAvailability(
      status: json['status']?.toString() ?? 'available',
      label: json['label']?.toString() ?? 'সম্পূর্ণ খালি',
      badgeClass: json['badge_class']?.toString() ?? 'bg-success',
      dayAvailable: json['day_available'] == true || json['day_available'] == 1,
      nightAvailable: json['night_available'] == true || json['night_available'] == 1,
      itemsCount: json['items_count'] is int ? json['items_count'] : int.tryParse(json['items_count']?.toString() ?? '0') ?? 0,
    );
  }
}

class WeddingDate {
  final int id;
  final String date;
  final DateTime dateTime;
  final String day;
  final String monthName;
  final String dayName;
  final String formattedDate;
  final String title;
  final String? bengaliDate;
  final String? bengaliDateAcademy;
  final String? bengaliDatePanjika;
  final String? tithi;
  final String? notes;
  final WeddingDateAvailability availability;

  WeddingDate({
    required this.id,
    required this.date,
    required this.dateTime,
    required this.day,
    required this.monthName,
    required this.dayName,
    required this.formattedDate,
    required this.title,
    this.bengaliDate,
    this.bengaliDateAcademy,
    this.bengaliDatePanjika,
    this.tithi,
    this.notes,
    required this.availability,
  });

  factory WeddingDate.fromJson(Map<String, dynamic> json) {
    final rawDate = json['date']?.toString() ?? '';
    DateTime parsedDateTime;
    try {
      parsedDateTime = DateTime.parse(rawDate);
    } catch (_) {
      parsedDateTime = DateTime.now();
    }

    final normalizedDate = DateTime(parsedDateTime.year, parsedDateTime.month, parsedDateTime.day);
    final dualDates = BanglaDateHelper.getBothDates(normalizedDate);

    final rawAcademy = json['bengali_date_academy']?.toString();
    final rawPanjika = json['bengali_date_panjika']?.toString();
    final rawBengaliDate = json['bengali_date']?.toString();

    return WeddingDate(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      date: rawDate,
      dateTime: normalizedDate,
      day: json['day']?.toString() ?? parsedDateTime.day.toString(),
      monthName: json['month_name']?.toString() ?? '',
      dayName: json['day_name']?.toString() ?? '',
      formattedDate: json['formatted_date']?.toString() ?? rawDate,
      title: json['title']?.toString() ?? 'শুভ বিবাহ লগ্ন',
      bengaliDate: rawBengaliDate ?? dualDates.panjika.full,
      bengaliDateAcademy: rawAcademy ?? dualDates.academy.full,
      bengaliDatePanjika: rawPanjika ?? rawBengaliDate ?? dualDates.panjika.full,
      tithi: json['tithi']?.toString(),
      notes: json['notes']?.toString(),
      availability: WeddingDateAvailability.fromJson(
        json['availability'] as Map<String, dynamic>?,
      ),
    );
  }
}
