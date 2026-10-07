class BanglaDateResult {
  final String day;
  final int dayNum;
  final String month;
  final int monthIndex;
  final String year;
  final int yearNum;
  final String full;

  BanglaDateResult({
    required this.day,
    required this.dayNum,
    required this.month,
    required this.monthIndex,
    required this.year,
    required this.yearNum,
    required this.full,
  });
}

class DualBanglaDate {
  final BanglaDateResult academy;
  final BanglaDateResult panjika;
  final BanglaDateResult bisuddhaSiddhanta;

  DualBanglaDate({
    required this.academy,
    required this.panjika,
    BanglaDateResult? bisuddhaSiddhanta,
  }) : bisuddhaSiddhanta = bisuddhaSiddhanta ?? panjika;
}

class BanglaDateHelper {
  static const List<String> bnMonths = [
    'বৈশাখ', 'জ্যৈষ্ঠ', 'আষাঢ়', 'শ্রাবণ', 'ভাদ্র', 'আশ্বিন',
    'কার্তিক', 'অগ্রহায়ণ', 'পৌষ', 'মাঘ', 'ফাল্গুন', 'চৈত্র'
  ];

  static const List<String> bnNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

  static String toBnNum(dynamic number) {
    final str = number.toString();
    var result = '';
    for (int i = 0; i < str.length; i++) {
      final code = str.codeUnitAt(i);
      if (code >= 48 && code <= 57) {
        result += bnNumbers[code - 48];
      } else {
        result += str[i];
      }
    }
    return result;
  }

  static bool isLeapYear(int year) {
    return (year % 4 == 0 && year % 100 != 0) || (year % 400 == 0);
  }

  /// Official Bangla Academy (Bangladesh Government Revised) date.
  static BanglaDateResult getAcademyDate(DateTime date) {
    final year = date.year;
    final isLeap = isLeapYear(year);

    DateTime baStart = DateTime(year, 4, 14);
    final target = DateTime(date.year, date.month, date.day);

    int bnYear;
    if (target.isBefore(baStart)) {
      bnYear = year - 594;
      baStart = DateTime(year - 1, 4, 14);
    } else {
      bnYear = year - 593;
    }

    int diffDays = target.difference(baStart).inDays;
    final monthLengths = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, isLeap ? 30 : 29, 30];

    int bnMonth = 0;
    while (diffDays >= monthLengths[bnMonth] && bnMonth < 11) {
      diffDays -= monthLengths[bnMonth];
      bnMonth++;
    }

    final bnDay = diffDays + 1;

    return BanglaDateResult(
      day: toBnNum(bnDay),
      dayNum: bnDay,
      month: bnMonths[bnMonth],
      monthIndex: bnMonth,
      year: toBnNum(bnYear),
      yearNum: bnYear,
      full: '${toBnNum(bnDay)} ${bnMonths[bnMonth]} ${toBnNum(bnYear)}',
    );
  }

  /// Traditional Hindu Panjika (Surya Siddhanta / Sankranti ingress based) date.
  static BanglaDateResult getPanjikaDate(DateTime date) {
    final year = date.year;
    final target = DateTime(date.year, date.month, date.day);

    // Solar ingress dates according to Bisuddha Siddhanta (Prokerala / Drik)
    final panjikaIngress = [
      {'m': 1, 'd': 16, 'bm': 9},  // Jan 16 -> Magh (ends Feb 13)
      {'m': 2, 'd': 14, 'bm': 10}, // Feb 14 -> Falgun (ends Mar 14)
      {'m': 3, 'd': 15, 'bm': 11}, // Mar 15 -> Chaitra (ends Apr 14)
      {'m': 4, 'd': 15, 'bm': 0},  // Apr 15 -> Boishakh (ends May 15)
      {'m': 5, 'd': 16, 'bm': 1},  // May 16 -> Jyeshtha (ends Jun 15)
      {'m': 6, 'd': 16, 'bm': 2},  // Jun 16 -> Ashadha (ends Jul 17)
      {'m': 7, 'd': 18, 'bm': 3},  // Jul 18 -> Srabon (ends Aug 18)
      {'m': 8, 'd': 19, 'bm': 4},  // Aug 19 -> Bhadra (ends Sep 18)
      {'m': 9, 'd': 19, 'bm': 5},  // Sep 19 -> Ashwin (ends Oct 18, so Oct 7 is 19 Ashwin)
      {'m': 10, 'd': 19, 'bm': 6}, // Oct 19 -> Kartik (ends Nov 17, so Oct 24 is 6 Kartik)
      {'m': 11, 'd': 18, 'bm': 7}, // Nov 18 -> Agrahayan (ends Dec 16)
      {'m': 12, 'd': 17, 'bm': 8}, // Dec 17 -> Poush (ends Jan 15)
    ];

    int? pjMonth;
    DateTime? pjStart;
    int pjYear = year - 593;

    for (final ing in panjikaIngress) {
      final ingDate = DateTime(year, ing['m']!, ing['d']!);
      if (!target.isBefore(ingDate)) {
        pjMonth = ing['bm'];
        pjStart = ingDate;
      }
    }

    if (pjStart == null) {
      // Before Jan 15: Poush of previous Gregorian year
      pjMonth = 8;
      pjStart = DateTime(year - 1, 12, 17);
      pjYear = year - 594;
    } else if (pjMonth! >= 9) {
      // Magh, Falgun, Chaitra
      pjYear = year - 594;
    }

    final diffDays = target.difference(pjStart).inDays;
    final pjDay = diffDays + 1;

    return BanglaDateResult(
      day: toBnNum(pjDay),
      dayNum: pjDay,
      month: bnMonths[pjMonth],
      monthIndex: pjMonth,
      year: toBnNum(pjYear),
      yearNum: pjYear,
      full: '${toBnNum(pjDay)} ${bnMonths[pjMonth]} ${toBnNum(pjYear)}',
    );
  }

  /// Traditional Bisuddha Siddhanta Panjika (Prokerala / Drik) date.
  static BanglaDateResult getBisuddhaSiddhantaDate(DateTime date) {
    return getPanjikaDate(date);
  }

  /// Returns both Academy and Bisuddha Siddhanta Panjika dates
  static DualBanglaDate getBothDates(DateTime date) {
    final pj = getBisuddhaSiddhantaDate(date);
    return DualBanglaDate(
      academy: getAcademyDate(date),
      panjika: pj,
      bisuddhaSiddhanta: pj,
    );
  }
}
