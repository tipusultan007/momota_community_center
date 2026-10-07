import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:table_calendar/table_calendar.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../providers/booking_provider.dart';
import '../../models/booking.dart';
import '../../models/booking_item.dart';
import '../../models/wedding_date.dart';
import '../../api/api_service.dart';
import '../../theme/app_theme.dart';
import '../../utils/bangla_date_helper.dart';
import '../bookings/booking_details_screen.dart';
import '../bookings/booking_form_screen.dart';

class CalendarScreen extends StatefulWidget {
  const CalendarScreen({super.key});

  @override
  State<CalendarScreen> createState() => _CalendarScreenState();
}

class _CalendarScreenState extends State<CalendarScreen> {
  DateTime _focusedDay = DateTime.now();
  DateTime _selectedDay = DateTime.now();
  final CalendarFormat _calendarFormat = CalendarFormat.month;

  List<WeddingDate> _weddingDates = [];
  Map<DateTime, WeddingDate> _weddingDatesMap = {};
  bool _isLoadingWeddingDates = false;

  @override
  void initState() {
    super.initState();
    _selectedDay = DateTime(_focusedDay.year, _focusedDay.month, _focusedDay.day);
    Future.microtask(() {
      if (mounted) {
        context.read<BookingProvider>().fetchBookings();
        _fetchWeddingDates();
      }
    });
  }

  Future<void> _fetchWeddingDates() async {
    setState(() => _isLoadingWeddingDates = true);
    try {
      final firstDay = DateTime(_focusedDay.year, _focusedDay.month, 1);
      final lastDay = DateTime(_focusedDay.year, _focusedDay.month + 1, 0);
      final startStr = DateFormat('yyyy-MM-dd').format(firstDay);
      final endStr = DateFormat('yyyy-MM-dd').format(lastDay);

      final response = await ApiService.instance.get(
        '/wedding-dates',
        queryParameters: {'start': startStr, 'end': endStr},
      );

      if (response.statusCode == 200 && response.data != null) {
        final List list = response.data is List ? response.data : (response.data['data'] ?? []);
        final items = list.map((e) => WeddingDate.fromJson(Map<String, dynamic>.from(e))).toList();
        final Map<DateTime, WeddingDate> map = {};
        for (var wd in items) {
          final norm = DateTime(wd.dateTime.year, wd.dateTime.month, wd.dateTime.day);
          map[norm] = wd;
        }
        if (mounted) {
          setState(() {
            _weddingDates = items;
            _weddingDatesMap = map;
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching wedding dates: $e');
    } finally {
      if (mounted) {
        setState(() => _isLoadingWeddingDates = false);
      }
    }
  }

  List<Map<String, dynamic>> _getEventsForDay(DateTime day, Map<DateTime, List<Map<String, dynamic>>> eventsMap) {
    final normalizedDate = DateTime(day.year, day.month, day.day);
    return eventsMap[normalizedDate] ?? [];
  }

  void _onPreviousMonth() {
    setState(() {
      _focusedDay = DateTime(_focusedDay.year, _focusedDay.month - 1, 1);
    });
    _fetchWeddingDates();
  }

  void _onNextMonth() {
    setState(() {
      _focusedDay = DateTime(_focusedDay.year, _focusedDay.month + 1, 1);
    });
    _fetchWeddingDates();
  }

  void _onToday() {
    setState(() {
      final now = DateTime.now();
      _focusedDay = now;
      _selectedDay = DateTime(now.year, now.month, now.day);
    });
    _fetchWeddingDates();
  }

  String _getBanglaMonthName(int month) {
    const bnMonths = [
      'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
      'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'
    ];
    return bnMonths[month - 1];
  }

  String _formatBanglaDate(DateTime date) {
    const bnDays = ['সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার', 'রবিবার'];
    final dayOfWeek = bnDays[date.weekday - 1];
    final monthName = _getBanglaMonthName(date.month);
    return '${date.day} $monthName, ${date.year} ($dayOfWeek)';
  }

  int _countMonthlyBookings(Map<DateTime, List<Map<String, dynamic>>> eventsMap) {
    int count = 0;
    eventsMap.forEach((date, events) {
      if (date.year == _focusedDay.year && date.month == _focusedDay.month) {
        count += events.length;
      }
    });
    return count;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(
          'বুকিং ক্যালেন্ডার',
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 20),
        ),
        elevation: 0,
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textNavy,
        actions: [
          TextButton.icon(
            onPressed: _onToday,
            icon: const Icon(Icons.today, size: 18, color: AppTheme.primaryEmerald),
            label: const Text(
              'আজ',
              style: TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryEmerald),
            ),
          ),
          IconButton(
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => BookingFormScreen(
                  initialDate: _selectedDay.toIso8601String().split('T')[0],
                ),
              ),
            ),
            icon: const Icon(Icons.add_circle, color: AppTheme.primaryEmerald, size: 28),
            tooltip: 'নতুন বুকিং',
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: Consumer<BookingProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading && provider.bookings.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          final eventsMap = provider.eventsByDate;
          final selectedEvents = _getEventsForDay(_selectedDay, eventsMap);
          final monthlyCount = _countMonthlyBookings(eventsMap);

          return RefreshIndicator(
            onRefresh: () async {
              await Future.wait([
                provider.fetchBookings(),
                _fetchWeddingDates(),
              ]);
            },
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Month Navigation & Summary Header
                  _buildMonthNavigatorBar(monthlyCount),

                  // Calendar Card with Booking Badges & Auspicious Highlighting
                  _buildCalendarCard(eventsMap),

                  // Slot & Wedding Date Legend
                  _buildLegendBar(),

                  // Shuvo Bibaho Auspicious Wedding Dates Module for Active Month
                  _buildWeddingDatesSection(),

                  // Selected Date Bookings Section
                  _buildSelectedDateSection(selectedEvents),

                  const SizedBox(height: 40),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildMonthNavigatorBar(int monthlyCount) {
    final engMonth = DateFormat('MMMM yyyy').format(_focusedDay);
    final bnMonth = '${_getBanglaMonthName(_focusedDay.month)} ${_focusedDay.year}';
    final dualDates = BanglaDateHelper.getBothDates(_focusedDay);
    final String bnSub = dualDates.academy.month == dualDates.panjika.month
        ? '${dualDates.academy.month} ${dualDates.academy.year}'
        : '${dualDates.academy.month} (বিশুদ্ধ সিদ্ধান্ত: ${dualDates.panjika.month})';

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 6),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: Colors.black.withAlpha(6), blurRadius: 10, offset: const Offset(0, 3)),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          // Previous Month Arrow Button
          IconButton(
            onPressed: _onPreviousMonth,
            icon: const Icon(Icons.chevron_left_rounded, size: 28, color: AppTheme.textNavy),
            tooltip: 'পূর্ববর্তী মাস',
            style: IconButton.styleFrom(
              backgroundColor: AppTheme.backgroundSlate,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
          ),
          // Month & Year Title
          Column(
            children: [
              Text(
                bnMonth,
                style: GoogleFonts.manrope(
                  fontSize: 18,
                  fontWeight: FontWeight.w900,
                  color: AppTheme.textNavy,
                ),
              ),
              const SizedBox(height: 2),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '$engMonth • $bnSub',
                    style: TextStyle(fontSize: 10.5, color: Colors.grey.shade600, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: AppTheme.primaryEmerald.withAlpha(20),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      '$monthlyCount টি বুকিং',
                      style: const TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.primaryEmerald,
                      ),
                    ),
                  ),
                  if (_weddingDates.isNotEmpty) ...[
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFEF3C7),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFF59E0B), width: 0.8),
                      ),
                      child: Text(
                        '💍 ${_weddingDates.length} লগ্ন',
                        style: const TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF92400E),
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ],
          ),
          // Next Month Arrow Button
          IconButton(
            onPressed: _onNextMonth,
            icon: const Icon(Icons.chevron_right_rounded, size: 28, color: AppTheme.textNavy),
            tooltip: 'পরবর্তী মাস',
            style: IconButton.styleFrom(
              backgroundColor: AppTheme.backgroundSlate,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCalendarCard(Map<DateTime, List<Map<String, dynamic>>> eventsMap) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(24),
        side: BorderSide(color: Colors.grey.withAlpha(25)),
      ),
      color: Colors.white,
      child: Padding(
        padding: const EdgeInsets.fromLTRB(8, 0, 8, 12),
        child: TableCalendar(
          firstDay: DateTime(2022, 1, 1),
          lastDay: DateTime(2035, 12, 31),
          focusedDay: _focusedDay,
          calendarFormat: _calendarFormat,
          startingDayOfWeek: StartingDayOfWeek.saturday,
          selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
          onDaySelected: (selectedDay, focusedDay) {
            setState(() {
              _selectedDay = selectedDay;
              _focusedDay = focusedDay;
            });
            if (focusedDay.month != _focusedDay.month || focusedDay.year != _focusedDay.year) {
              _fetchWeddingDates();
            }
          },
          onPageChanged: (focusedDay) {
            setState(() {
              _focusedDay = focusedDay;
            });
            _fetchWeddingDates();
          },
          headerVisible: false, // We use our custom month navigator
          rowHeight: 56.0,
          daysOfWeekStyle: const DaysOfWeekStyle(
            weekdayStyle: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: AppTheme.textNavy),
            weekendStyle: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.redAccent),
          ),
          calendarStyle: const CalendarStyle(
            outsideDaysVisible: false,
          ),
          calendarBuilders: CalendarBuilders(
            // Default Day Cell
            defaultBuilder: (context, day, focusedDay) {
              final events = _getEventsForDay(day, eventsMap);
              final isBooked = events.isNotEmpty;
              final weddingDate = _weddingDatesMap[DateTime(day.year, day.month, day.day)];

              return _buildDayCell(
                day: day,
                isSelected: false,
                isToday: isSameDay(day, DateTime.now()),
                events: events,
                isBooked: isBooked,
                weddingDate: weddingDate,
              );
            },
            // Selected Day Cell
            selectedBuilder: (context, day, focusedDay) {
              final events = _getEventsForDay(day, eventsMap);
              final weddingDate = _weddingDatesMap[DateTime(day.year, day.month, day.day)];

              return _buildDayCell(
                day: day,
                isSelected: true,
                isToday: isSameDay(day, DateTime.now()),
                events: events,
                isBooked: events.isNotEmpty,
                weddingDate: weddingDate,
              );
            },
            // Today Cell
            todayBuilder: (context, day, focusedDay) {
              final events = _getEventsForDay(day, eventsMap);
              final isSelected = isSameDay(_selectedDay, day);
              final weddingDate = _weddingDatesMap[DateTime(day.year, day.month, day.day)];

              return _buildDayCell(
                day: day,
                isSelected: isSelected,
                isToday: true,
                events: events,
                isBooked: events.isNotEmpty,
                weddingDate: weddingDate,
              );
            },
          ),
        ),
      ),
    );
  }

  Widget _buildDayCell({
    required DateTime day,
    required bool isSelected,
    required bool isToday,
    required List<Map<String, dynamic>> events,
    required bool isBooked,
    WeddingDate? weddingDate,
  }) {
    bool hasDaySlot = false;
    bool hasNightSlot = false;

    for (var event in events) {
      final BookingItem item = event['item'];
      final s = (item.slot == 'night' || item.slot == 'evening') ? 'night' : 'day';
      if (s == 'night') hasNightSlot = true;
      if (s == 'day') hasDaySlot = true;
    }

    final bool isWeddingDate = weddingDate != null;

    Color cellBgColor = Colors.transparent;
    Color textColor = AppTheme.textNavy;
    Border? border;

    if (isSelected) {
      cellBgColor = AppTheme.primaryEmerald;
      textColor = Colors.white;
      if (isWeddingDate) {
        border = Border.all(color: const Color(0xFFF59E0B), width: 2.2);
      }
    } else if (isToday) {
      if (isWeddingDate) {
        cellBgColor = const Color(0xFFFEF3C7);
        textColor = const Color(0xFFB45309);
        border = Border.all(color: const Color(0xFFD97706), width: 2);
      } else {
        cellBgColor = AppTheme.primaryEmerald.withAlpha(20);
        textColor = AppTheme.primaryEmerald;
        border = Border.all(color: AppTheme.primaryEmerald, width: 1.5);
      }
    } else if (isWeddingDate) {
      // Golden highlighting for Shuvo Bibaho auspicious date
      cellBgColor = const Color(0xFFFFFBEB); // Warm festive amber tint
      textColor = const Color(0xFF92400E);
      border = Border.all(color: const Color(0xFFF59E0B), width: 1.5);
    } else if (isBooked) {
      cellBgColor = const Color(0xFFE8F5E9); // Soft pastel green
      border = Border.all(color: AppTheme.primaryEmerald.withAlpha(80), width: 1);
    }

    return Container(
      margin: const EdgeInsets.all(3),
      decoration: BoxDecoration(
        color: cellBgColor,
        borderRadius: BorderRadius.circular(14),
        border: border,
        boxShadow: isWeddingDate && !isSelected
            ? [
                BoxShadow(
                  color: const Color(0xFFF59E0B).withAlpha(35),
                  blurRadius: 4,
                  offset: const Offset(0, 1),
                ),
              ]
            : null,
      ),
      child: Stack(
        children: [
          // Auspicious ring icon in top right corner
          if (isWeddingDate)
            Positioned(
              top: 2,
              right: 3,
              child: Text(
                '💍',
                style: TextStyle(
                  fontSize: 8,
                  shadows: isSelected
                      ? [const Shadow(color: Colors.black45, blurRadius: 2)]
                      : null,
                ),
              ),
            ),
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  '${day.day}',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: (isSelected || isToday || isBooked || isWeddingDate)
                        ? FontWeight.bold
                        : FontWeight.w500,
                    color: textColor,
                  ),
                ),
                Builder(
                  builder: (context) {
                    final dualBn = BanglaDateHelper.getBothDates(day);
                    return Text(
                      'বা:${dualBn.academy.day} বি:${dualBn.panjika.day}',
                      style: TextStyle(
                        fontSize: 7.5,
                        fontWeight: (isWeddingDate || isSelected) ? FontWeight.bold : FontWeight.w500,
                        color: isSelected
                            ? Colors.white.withAlpha(220)
                            : (isWeddingDate ? const Color(0xFFB45309) : Colors.grey.shade600),
                      ),
                    );
                  },
                ),
                const SizedBox(height: 1),
                // Booking indicator dots
                if (isBooked)
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      if (hasDaySlot)
                        Container(
                          width: 6,
                          height: 6,
                          margin: const EdgeInsets.symmetric(horizontal: 1),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: isSelected
                                ? Colors.amberAccent
                                : const Color(0xFFF59E0B), // Sun / Day (Amber)
                          ),
                        ),
                      if (hasNightSlot)
                        Container(
                          width: 6,
                          height: 6,
                          margin: const EdgeInsets.symmetric(horizontal: 1),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: isSelected
                                ? Colors.lightBlueAccent
                                : const Color(0xFF7C3AED), // Moon / Night (Purple)
                          ),
                        ),
                    ],
                  )
                else if (isWeddingDate)
                  // For unbooked auspicious wedding dates, show green availability indicator dot
                  Container(
                    width: 5,
                    height: 5,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: isSelected
                          ? Colors.white70
                          : (weddingDate.availability.status == 'available'
                              ? const Color(0xFF10B981)
                              : const Color(0xFFF59E0B)),
                    ),
                  )
                else
                  const SizedBox(height: 6),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegendBar() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.withAlpha(20)),
      ),
      child: Wrap(
        alignment: WrapAlignment.spaceAround,
        spacing: 10,
        runSpacing: 6,
        children: [
          _buildLegendItem(const Color(0xFFF59E0B), 'দিন স্লট (Day)'),
          _buildLegendItem(const Color(0xFF7C3AED), 'রাত স্লট (Night)'),
          _buildLegendWeddingItem('💍 শুভ বিবাহ লগ্ন'),
          _buildLegendItem(AppTheme.primaryEmerald, 'নির্বাচিত দিন'),
          _buildLegendDualBadgeItem(),
        ],
      ),
    );
  }

  Widget _buildLegendItem(Color color, String label) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 8,
          height: 8,
          decoration: BoxDecoration(shape: BoxShape.circle, color: color),
        ),
        const SizedBox(width: 5),
        Text(
          label,
          style: TextStyle(fontSize: 10, color: Colors.grey.shade700, fontWeight: FontWeight.bold),
        ),
      ],
    );
  }

  Widget _buildLegendWeddingItem(String label) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 3, vertical: 1),
          decoration: BoxDecoration(
            color: const Color(0xFFFEF3C7),
            borderRadius: BorderRadius.circular(4),
            border: Border.all(color: const Color(0xFFF59E0B), width: 1),
          ),
          child: const Text('💍', style: TextStyle(fontSize: 8)),
        ),
        const SizedBox(width: 5),
        Text(
          label,
          style: const TextStyle(fontSize: 10, color: Color(0xFF92400E), fontWeight: FontWeight.bold),
        ),
      ],
    );
  }

  Widget _buildLegendDualBadgeItem() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
      decoration: BoxDecoration(
        color: Colors.grey.shade100,
        borderRadius: BorderRadius.circular(5),
        border: Border.all(color: Colors.grey.shade300, width: 0.8),
      ),
      child: const Text(
        'বা: একাডেমি  |  বি: বিশুদ্ধ সিদ্ধান্ত',
        style: TextStyle(fontSize: 9.5, color: AppTheme.textNavy, fontWeight: FontWeight.bold),
      ),
    );
  }

  Widget _buildWeddingDatesSection() {
    final bnMonth = _getBanglaMonthName(_focusedDay.month);

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 10, 16, 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFFDE68A), width: 1.2),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFFF59E0B).withAlpha(15),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section Header
          Padding(
            padding: const EdgeInsets.fromLTRB(14, 12, 14, 10),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(7),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFEF3C7),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Text('💍', style: TextStyle(fontSize: 18)),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'শুভ বিবাহের লগ্ন (হিন্দু বিবাহ)',
                        style: GoogleFonts.manrope(
                          fontWeight: FontWeight.w800,
                          fontSize: 14.5,
                          color: const Color(0xFF92400E),
                        ),
                      ),
                      Text(
                        '$bnMonth ${_focusedDay.year} • ${_weddingDates.length}টি লগ্ন নির্ধারিত',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Colors.amber.shade800,
                        ),
                      ),
                    ],
                  ),
                ),
                ElevatedButton.icon(
                  onPressed: _showAddWeddingDateBottomSheet,
                  icon: const Icon(Icons.add, size: 14, color: Colors.white),
                  label: const Text(
                    'লগ্ন যোগ',
                    style: TextStyle(fontSize: 11, color: Colors.white, fontWeight: FontWeight.bold),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFD97706),
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 0),
                    minimumSize: const Size(0, 34),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: Color(0xFFFEF3C7)),

          // Body List / Loading / Empty
          if (_isLoadingWeddingDates && _weddingDates.isEmpty)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 20),
              child: Center(
                child: SizedBox(
                  width: 24,
                  height: 24,
                  child: CircularProgressIndicator(color: Color(0xFFD97706), strokeWidth: 2.5),
                ),
              ),
            )
          else if (_weddingDates.isEmpty)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              child: Row(
                children: [
                  Icon(Icons.info_outline, size: 20, color: Colors.amber.shade700),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      'এই মাসে কোনো শুভ বিবাহের লগ্ন নির্ধারিত নেই। ম্যানুয়ালি লগ্ন যোগ করতে "+ লগ্ন যোগ" বাটনে চাপুন।',
                      style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                    ),
                  ),
                ],
              ),
            )
          else
            ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
              itemCount: _weddingDates.length,
              separatorBuilder: (ctx, i) => const SizedBox(height: 8),
              itemBuilder: (context, index) {
                final wd = _weddingDates[index];
                return _buildWeddingDateCard(wd);
              },
            ),
        ],
      ),
    );
  }

  Widget _buildWeddingDateCard(WeddingDate wd) {
    Color badgeBg;
    Color badgeText;
    if (wd.availability.status == 'available') {
      badgeBg = const Color(0xFFD1FAE5);
      badgeText = const Color(0xFF047857);
    } else if (wd.availability.status == 'partial') {
      badgeBg = const Color(0xFFFEF3C7);
      badgeText = const Color(0xFFB45309);
    } else {
      badgeBg = const Color(0xFFFEE2E2);
      badgeText = const Color(0xFFB91C1C);
    }

    final isSelected = isSameDay(_selectedDay, wd.dateTime);

    return Container(
      decoration: BoxDecoration(
        color: isSelected ? const Color(0xFFFFFBEB) : Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isSelected ? const Color(0xFFD97706) : Colors.grey.withAlpha(35),
          width: isSelected ? 1.5 : 1,
        ),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: () {
            setState(() {
              _selectedDay = wd.dateTime;
            });
          },
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                // Day box
                Container(
                  width: 44,
                  padding: const EdgeInsets.symmetric(vertical: 6),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFEF3C7),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFF59E0B), width: 1),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        wd.day,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFF92400E),
                          height: 1.1,
                        ),
                      ),
                      Text(
                        wd.dayName.length > 3 ? wd.dayName.substring(0, 3) : wd.dayName,
                        style: const TextStyle(
                          fontSize: 9,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFFB45309),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),

                // Details
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Flexible(
                            child: Text(
                              wd.title,
                              style: GoogleFonts.manrope(
                                fontWeight: FontWeight.w800,
                                fontSize: 13,
                                color: AppTheme.textNavy,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          const SizedBox(width: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(
                              color: badgeBg,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              wd.availability.label,
                              style: TextStyle(
                                fontSize: 9.5,
                                fontWeight: FontWeight.bold,
                                color: badgeText,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 3),
                      Wrap(
                        spacing: 6,
                        runSpacing: 2,
                        crossAxisAlignment: WrapCrossAlignment.center,
                        children: [
                          if (wd.bengaliDatePanjika != null && wd.bengaliDatePanjika!.isNotEmpty)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                              decoration: BoxDecoration(
                                color: const Color(0xFFFEF3C7),
                                borderRadius: BorderRadius.circular(4),
                                border: Border.all(color: const Color(0xFFF59E0B), width: 0.7),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Text('বি ', style: TextStyle(fontSize: 8.5, fontWeight: FontWeight.bold, color: Color(0xFF92400E))),
                                  Text(
                                    wd.bengaliDatePanjika!,
                                    style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.bold, color: Color(0xFF92400E)),
                                  ),
                                ],
                              ),
                            ),
                          if (wd.bengaliDateAcademy != null && wd.bengaliDateAcademy!.isNotEmpty)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                              decoration: BoxDecoration(
                                color: Colors.grey.shade100,
                                borderRadius: BorderRadius.circular(4),
                                border: Border.all(color: Colors.grey.shade300, width: 0.7),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Text('বা ', style: TextStyle(fontSize: 8.5, fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
                                  Text(
                                    wd.bengaliDateAcademy!,
                                    style: TextStyle(fontSize: 10.5, color: Colors.grey.shade800),
                                  ),
                                ],
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 2),
                      if (wd.tithi != null && wd.tithi!.isNotEmpty)
                        Text(
                          'লগ্ন: ${wd.tithi!}',
                          style: TextStyle(fontSize: 10.5, color: Colors.grey.shade600),
                        ),
                    ],
                  ),
                ),

                // Action buttons
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (wd.availability.status != 'booked')
                      IconButton(
                        onPressed: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => BookingFormScreen(
                              initialDate: wd.date,
                            ),
                          ),
                        ),
                        icon: const Icon(Icons.bookmark_add_outlined, size: 20, color: AppTheme.primaryEmerald),
                        tooltip: 'বুকিং করুন',
                        style: IconButton.styleFrom(
                          backgroundColor: AppTheme.primaryEmerald.withAlpha(20),
                          padding: const EdgeInsets.all(6),
                          minimumSize: const Size(32, 32),
                        ),
                      ),
                    IconButton(
                      onPressed: () => _confirmDeleteWeddingDate(wd),
                      icon: const Icon(Icons.delete_outline, size: 18, color: Colors.redAccent),
                      tooltip: 'মুছে ফেলুন',
                      style: IconButton.styleFrom(
                        padding: const EdgeInsets.all(6),
                        minimumSize: const Size(32, 32),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _showAddWeddingDateBottomSheet() {
    final messenger = ScaffoldMessenger.of(context);
    final navigator = Navigator.of(context);
    DateTime selectedDate = _selectedDay;
    final titleController = TextEditingController(text: 'শুভ বিবাহ লগ্ন');
    final bengaliDateController = TextEditingController();
    final tithiController = TextEditingController(text: 'রাত্রিলগ্ন');
    final notesController = TextEditingController();
    bool isSubmitting = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Container(
              padding: EdgeInsets.only(
                top: 20,
                left: 20,
                right: 20,
                bottom: MediaQuery.of(context).viewInsets.bottom + 20,
              ),
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: const Color(0xFFFEF3C7),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Text('💍', style: TextStyle(fontSize: 22)),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'নতুন শুভ বিবাহ লগ্ন যোগ করুন',
                                style: GoogleFonts.manrope(
                                  fontWeight: FontWeight.w800,
                                  fontSize: 17,
                                  color: AppTheme.textNavy,
                                ),
                              ),
                              Text(
                                'পঞ্জিকা অনুসারে বিবাহের তারিখ যুক্ত করুন',
                                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                              ),
                            ],
                          ),
                        ),
                        IconButton(
                          onPressed: () => Navigator.pop(context),
                          icon: const Icon(Icons.close),
                        ),
                      ],
                    ),
                    const Divider(height: 24),

                    // Date Picker
                    Text('তারিখ নির্বাচন করুন *', style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 13, color: AppTheme.textNavy)),
                    const SizedBox(height: 6),
                    InkWell(
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: selectedDate,
                          firstDate: DateTime(2022, 1, 1),
                          lastDate: DateTime(2035, 12, 31),
                        );
                        if (picked != null) {
                          setModalState(() {
                            selectedDate = picked;
                            final dual = BanglaDateHelper.getBothDates(picked);
                            bengaliDateController.text = dual.panjika.full;
                          });
                        }
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        decoration: BoxDecoration(
                          border: Border.all(color: Colors.grey.shade300),
                          borderRadius: BorderRadius.circular(12),
                          color: AppTheme.backgroundSlate,
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                const Icon(Icons.calendar_month, color: AppTheme.primaryEmerald, size: 20),
                                const SizedBox(width: 10),
                                Text(
                                  DateFormat('dd MMMM, yyyy (EEEE)').format(selectedDate),
                                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppTheme.textNavy),
                                ),
                              ],
                            ),
                            const Text('পরিবর্তন', style: TextStyle(color: AppTheme.primaryEmerald, fontWeight: FontWeight.bold, fontSize: 12)),
                          ],
                        ),
                      ),
                    ),
                    // Dual Bangla Date Preview Box
                    Builder(
                      builder: (context) {
                        final dualBn = BanglaDateHelper.getBothDates(selectedDate);
                        if (bengaliDateController.text.isEmpty) {
                          bengaliDateController.text = dualBn.panjika.full;
                        }
                        return Container(
                          margin: const EdgeInsets.only(top: 8),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          decoration: BoxDecoration(
                            color: const Color(0xFFFFFBEB),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFFFDE68A)),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const Text('বি বিশুদ্ধ সিদ্ধান্ত (প্রোকেরালা):', style: TextStyle(fontSize: 10, color: Color(0xFF92400E), fontWeight: FontWeight.bold)),
                                    const SizedBox(height: 2),
                                    Text(dualBn.panjika.full, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFFB45309))),
                                  ],
                                ),
                              ),
                              Container(height: 26, width: 1, color: const Color(0xFFFDE68A)),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text('বা বাংলা একাডেমি (সরকারি):', style: TextStyle(fontSize: 10, color: Colors.grey.shade700, fontWeight: FontWeight.bold)),
                                    const SizedBox(height: 2),
                                    Text(dualBn.academy.full, style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade800)),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 14),

                    // Title
                    Text('শিরোনাম', style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 13, color: AppTheme.textNavy)),
                    const SizedBox(height: 6),
                    TextField(
                      controller: titleController,
                      decoration: InputDecoration(
                        hintText: 'যেমন: শুভ বিবাহ লগ্ন',
                        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Bengali Date
                    Text('বাংলা সন ও তারিখ (ঐচ্ছিক)', style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 13, color: AppTheme.textNavy)),
                    const SizedBox(height: 6),
                    TextField(
                      controller: bengaliDateController,
                      decoration: InputDecoration(
                        hintText: 'যেমন: ২৮ আশ্বিন, ১৪৩৩ বা ৬ কার্তিক',
                        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                    // Quick choose chips
                    Builder(
                      builder: (context) {
                        final dualBn = BanglaDateHelper.getBothDates(selectedDate);
                        return Padding(
                          padding: const EdgeInsets.only(top: 6),
                          child: Wrap(
                            spacing: 8,
                            children: [
                              ActionChip(
                                label: Text('বিশুদ্ধ সিদ্ধান্ত: ${dualBn.panjika.full}', style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.bold, color: Color(0xFF92400E))),
                                backgroundColor: const Color(0xFFFEF3C7),
                                side: const BorderSide(color: Color(0xFFF59E0B), width: 0.8),
                                onPressed: () {
                                  setModalState(() {
                                    bengaliDateController.text = dualBn.panjika.full;
                                  });
                                },
                              ),
                              ActionChip(
                                label: Text('একাডেমি: ${dualBn.academy.full}', style: TextStyle(fontSize: 10.5, color: Colors.grey.shade800)),
                                backgroundColor: Colors.grey.shade100,
                                side: BorderSide(color: Colors.grey.shade300, width: 0.8),
                                onPressed: () {
                                  setModalState(() {
                                    bengaliDateController.text = dualBn.academy.full;
                                  });
                                },
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 14),

                    // Tithi / লগ্ন সময়
                    Text('তিথি ও লগ্ন সময় *', style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 13, color: AppTheme.textNavy)),
                    const SizedBox(height: 6),
                    TextField(
                      controller: tithiController,
                      decoration: InputDecoration(
                        hintText: 'যেমন: রাত্রিলগ্ন / দিবা ০১:৪৫ গতে',
                        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                    const SizedBox(height: 8),
                    // Quick tithi chips
                    Wrap(
                      spacing: 8,
                      children: ['রাত্রিলগ্ন', 'দিবালগ্ন', 'গোধূলিলগ্ন', 'দ্বাদশীর রাত্রি'].map((chip) {
                        return ActionChip(
                          label: Text(chip, style: const TextStyle(fontSize: 11)),
                          backgroundColor: const Color(0xFFFEF3C7),
                          side: BorderSide.none,
                          onPressed: () {
                            setModalState(() {
                              tithiController.text = chip;
                            });
                          },
                        );
                      }).toList(),
                    ),
                    const SizedBox(height: 14),

                    // Notes
                    Text('মন্তব্য / নোট (ঐচ্ছিক)', style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 13, color: AppTheme.textNavy)),
                    const SizedBox(height: 6),
                    TextField(
                      controller: notesController,
                      maxLines: 2,
                      decoration: InputDecoration(
                        hintText: 'পঞ্জিকার অতিরিক্ত বিবরণ...',
                        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Submit Button
                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: ElevatedButton.icon(
                        onPressed: isSubmitting
                            ? null
                            : () async {
                                setModalState(() => isSubmitting = true);
                                try {
                                  final dateStr = DateFormat('yyyy-MM-dd').format(selectedDate);
                                  await ApiService.instance.post(
                                    '/wedding-dates',
                                    data: {
                                      'date': dateStr,
                                      'title': titleController.text.trim().isEmpty ? 'শুভ বিবাহ লগ্ন' : titleController.text.trim(),
                                      'bengali_date': bengaliDateController.text.trim().isEmpty ? null : bengaliDateController.text.trim(),
                                      'tithi': tithiController.text.trim().isEmpty ? 'রাত্রিলগ্ন' : tithiController.text.trim(),
                                      'notes': notesController.text.trim().isEmpty ? null : notesController.text.trim(),
                                    },
                                  );

                                  if (mounted) {
                                    navigator.pop();
                                    messenger.showSnackBar(
                                      const SnackBar(
                                        content: Text('শুভ বিবাহের তারিখ সফলভাবে যুক্ত করা হয়েছে!'),
                                        backgroundColor: Color(0xFF059669),
                                      ),
                                    );
                                    _fetchWeddingDates();
                                  }
                                } catch (e) {
                                  if (mounted) {
                                    setModalState(() => isSubmitting = false);
                                    messenger.showSnackBar(
                                      SnackBar(
                                        content: Text('ত্রুটি ঘটেছে: $e'),
                                        backgroundColor: Colors.redAccent,
                                      ),
                                    );
                                  }
                                }
                              },
                        icon: isSubmitting
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                            : const Icon(Icons.check_circle, color: Colors.white),
                        label: Text(
                          isSubmitting ? 'সংরক্ষণ করা হচ্ছে...' : 'শুভ বিবাহের লগ্ন সংরক্ষণ করুন',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFD97706),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  void _confirmDeleteWeddingDate(WeddingDate wd) {
    final messenger = ScaffoldMessenger.of(context);
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: Row(
          children: const [
            Icon(Icons.warning_amber_rounded, color: Colors.redAccent),
            SizedBox(width: 8),
            Text('তারিখ মুছে ফেলুন', style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold)),
          ],
        ),
        content: Text(
          'আপনি কি নিশ্চিতভাবে এই শুভ বিবাহের লগ্নটি (${wd.formattedDate}) মুছে ফেলতে চান?',
          style: const TextStyle(fontSize: 13),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('বাতিল', style: TextStyle(color: Colors.grey)),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              try {
                await ApiService.instance.delete('/wedding-dates/${wd.id}');
                if (mounted) {
                  messenger.showSnackBar(
                    const SnackBar(
                      content: Text('শুভ বিবাহের লগ্নটি সফলভাবে মুছে ফেলা হয়েছে।'),
                      backgroundColor: Colors.redAccent,
                    ),
                  );
                  _fetchWeddingDates();
                }
              } catch (e) {
                if (mounted) {
                  messenger.showSnackBar(
                    SnackBar(
                      content: Text('মুছতে সমস্যা হয়েছে: $e'),
                      backgroundColor: Colors.redAccent,
                    ),
                  );
                }
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            child: const Text('মুছে ফেলুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildSelectedDateSection(List<Map<String, dynamic>> selectedEvents) {
    final formattedDate = _formatBanglaDate(_selectedDay);
    final WeddingDate? selectedWeddingDate =
        _weddingDatesMap[DateTime(_selectedDay.year, _selectedDay.month, _selectedDay.day)];

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // If this selected date is an auspicious Hindu wedding date, show banner!
          if (selectedWeddingDate != null)
            Container(
              margin: const EdgeInsets.only(bottom: 12),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                color: const Color(0xFFFEF3C7),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0xFFF59E0B), width: 1.2),
              ),
              child: Row(
                children: [
                  const Text('💍', style: TextStyle(fontSize: 22)),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'শুভ বিবাহ লগ্ন: ${selectedWeddingDate.title}',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF92400E)),
                        ),
                        Wrap(
                          spacing: 6,
                          crossAxisAlignment: WrapCrossAlignment.center,
                          children: [
                            if (selectedWeddingDate.bengaliDatePanjika != null)
                              Text(
                                'বি: ${selectedWeddingDate.bengaliDatePanjika}',
                                style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFFB45309)),
                              ),
                            if (selectedWeddingDate.bengaliDateAcademy != null)
                              Text(
                                'বা: ${selectedWeddingDate.bengaliDateAcademy}',
                                style: TextStyle(fontSize: 10.5, color: Colors.grey.shade800),
                              ),
                            if (selectedWeddingDate.tithi != null && selectedWeddingDate.tithi!.isNotEmpty)
                              Text(
                                '(${selectedWeddingDate.tithi})',
                                style: const TextStyle(fontSize: 10.5, color: Color(0xFF92400E)),
                              ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: selectedWeddingDate.availability.status == 'available'
                          ? const Color(0xFF059669)
                          : (selectedWeddingDate.availability.status == 'partial'
                              ? const Color(0xFFD97706)
                              : const Color(0xFFDC2626)),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      selectedWeddingDate.availability.label,
                      style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ),
            ),

          // Section Title
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.grey.withAlpha(20)),
            ),
            child: Row(
              children: [
                const Icon(Icons.event_note, color: AppTheme.primaryEmerald, size: 22),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        formattedDate,
                        style: GoogleFonts.manrope(
                          fontWeight: FontWeight.w800,
                          fontSize: 14,
                          color: AppTheme.textNavy,
                        ),
                      ),
                      Builder(builder: (context) {
                        final dual = BanglaDateHelper.getBothDates(_selectedDay);
                        return Padding(
                          padding: const EdgeInsets.symmetric(vertical: 2),
                          child: Text(
                            'বা: ${dual.academy.full}  |  বি: ${dual.panjika.full}',
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF92400E)),
                          ),
                        );
                      }),
                      Text(
                        selectedEvents.isEmpty
                            ? 'এই দিনে কোনো বুকিং নেই'
                            : '${selectedEvents.length} টি বুকিং নিবন্ধিত রয়েছে',
                        style: TextStyle(
                          fontSize: 11,
                          color: selectedEvents.isEmpty ? Colors.grey : AppTheme.primaryEmerald,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
                ElevatedButton.icon(
                  onPressed: () => Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => BookingFormScreen(
                        initialDate: _selectedDay.toIso8601String().split('T')[0],
                      ),
                    ),
                  ),
                  icon: const Icon(Icons.add, size: 14, color: Colors.white),
                  label: const Text('বুকিং দিন', style: TextStyle(fontSize: 11, color: Colors.white, fontWeight: FontWeight.bold)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.textNavy,
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
                    minimumSize: const Size(0, 36),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // Booking Cards or Empty State
          if (selectedEvents.isEmpty)
            _buildEmptyDateState()
          else
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: selectedEvents.length,
              itemBuilder: (context, index) {
                final event = selectedEvents[index];
                final BookingItem item = event['item'];
                final Booking booking = event['booking'];

                return _buildBookingDetailCard(item, booking);
              },
            ),
        ],
      ),
    );
  }

  Widget _buildEmptyDateState() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 36, horizontal: 20),
      margin: const EdgeInsets.only(top: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.withAlpha(20)),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.event_available_outlined, size: 54, color: Colors.grey.shade300),
          const SizedBox(height: 12),
          Text(
            'এই তারিখে কোনো হল বুকিং নেই',
            style: GoogleFonts.manrope(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: Colors.grey.shade600,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'হল খালি রয়েছে, নতুন বুকিং গ্রহণ করতে পারেন।',
            style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
          ),
          const SizedBox(height: 16),
          OutlinedButton.icon(
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => BookingFormScreen(
                  initialDate: _selectedDay.toIso8601String().split('T')[0],
                ),
              ),
            ),
            icon: const Icon(Icons.add, size: 16, color: AppTheme.primaryEmerald),
            label: const Text(
              'এই তারিখে বুকিং করুন',
              style: TextStyle(color: AppTheme.primaryEmerald, fontWeight: FontWeight.bold),
            ),
            style: OutlinedButton.styleFrom(
              side: const BorderSide(color: AppTheme.primaryEmerald),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBookingDetailCard(BookingItem item, Booking booking) {
    final isNight = (item.slot == 'night' || item.slot == 'evening');
    final slotLabel = isNight ? 'রাত (Night)' : 'দিন (Day)';
    final slotColor = isNight ? const Color(0xFF7C3AED) : const Color(0xFFF59E0B);
    final slotIcon = isNight ? Icons.nightlight_round : Icons.wb_sunny_rounded;

    final eventTitle = item.eventType.isNotEmpty ? item.eventType : 'ইভেন্ট / অনুষ্ঠান';
    IconData eventIcon = Icons.celebration_rounded;
    final lower = item.eventType.toLowerCase();
    if (lower.contains('corporate') || lower.contains('অফিস') || lower.contains('মিটিং') || lower.contains('office') || lower.contains('meeting') || lower.contains('কনফারেন্স') || lower.contains('সেমিনার') || lower.contains('এজিএম') || lower.contains('প্রেস')) {
      eventIcon = Icons.business_center_rounded;
    } else if (lower.contains('wedding') || lower.contains('বিয়ে') || lower.contains('বিবাহ') || lower.contains('reception') || lower.contains('আকদ') || lower.contains('এনগেজমেন্ট') || lower.contains('বৌভাত') || lower.contains('ওয়ালিমা') || lower.contains('বিবাহবার্ষিকী')) {
      eventIcon = Icons.favorite_rounded;
    } else if (lower.contains('birthday') || lower.contains('জন্মদিন') || lower.contains('আকিকা')) {
      eventIcon = Icons.cake_rounded;
    } else if (lower.contains('হলুদ') || lower.contains('মেহেদি') || lower.contains('holud') || lower.contains('mehendi')) {
      eventIcon = Icons.celebration_rounded;
    } else if (lower.contains('ইফতার') || lower.contains('দোয়া') || lower.contains('মিলাদ')) {
      eventIcon = Icons.nights_stay_rounded;
    } else if (lower.contains('সাংস্কৃতিক') || lower.contains('কনসার্ট')) {
      eventIcon = Icons.music_note_rounded;
    } else if (lower.contains('সমাবর্তন') || lower.contains('র্যাগ')) {
      eventIcon = Icons.school_rounded;
    } else if (lower.contains('মেলা') || lower.contains('প্রদর্শনী')) {
      eventIcon = Icons.storefront_rounded;
    } else if (lower.contains('পুনর্মিলনী') || lower.contains('রিইউনিয়ন')) {
      eventIcon = Icons.groups_rounded;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.withAlpha(25)),
        boxShadow: [
          BoxShadow(color: Colors.black.withAlpha(4), blurRadius: 10, offset: const Offset(0, 4)),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(20),
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => BookingDetailsScreen(booking: booking),
              ),
            );
          },
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Row: Slot Badge & Status
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: slotColor.withAlpha(25),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(slotIcon, size: 14, color: slotColor),
                          const SizedBox(width: 4),
                          Text(
                            slotLabel,
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: slotColor,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: _getStatusBgColor(booking.status),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        booking.status.toUpperCase(),
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: _getStatusColor(booking.status),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

                // Middle: Event Type & Customer Info
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: AppTheme.primaryEmerald.withAlpha(15),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: Icon(eventIcon, color: AppTheme.primaryEmerald, size: 24),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            eventTitle,
                            style: GoogleFonts.manrope(
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                              color: AppTheme.textNavy,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Row(
                            children: [
                              const Icon(Icons.person_outline, size: 14, color: Colors.grey),
                              const SizedBox(width: 4),
                              Text(
                                booking.customerName,
                                style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                              ),
                            ],
                          ),
                          if (booking.customerPhone.isNotEmpty) ...[
                            const SizedBox(height: 2),
                            Row(
                              children: [
                                const Icon(Icons.phone_outlined, size: 14, color: Colors.grey),
                                const SizedBox(width: 4),
                                Text(
                                  booking.customerPhone,
                                  style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                                ),
                              ],
                            ),
                          ],
                        ],
                      ),
                    ),
                    const Icon(Icons.chevron_right_rounded, color: Colors.grey, size: 24),
                  ],
                ),
                const Divider(height: 20),

                // Bottom Row: Amounts (Total & Paid)
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text.rich(
                      TextSpan(
                        children: [
                          const TextSpan(text: 'মোট: ', style: TextStyle(color: Colors.grey, fontSize: 12)),
                          TextSpan(
                            text: '৳ ${booking.totalAmount.toStringAsFixed(0)}',
                            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppTheme.textNavy),
                          ),
                        ],
                      ),
                    ),
                    Text.rich(
                      TextSpan(
                        children: [
                          const TextSpan(text: 'পরিশোধিত: ', style: TextStyle(color: Colors.grey, fontSize: 12)),
                          TextSpan(
                            text: '৳ ${booking.paidAmount.toStringAsFixed(0)}',
                            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppTheme.primaryEmerald),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed':
        return Colors.green.shade700;
      case 'cancelled':
        return Colors.red.shade700;
      case 'completed':
        return Colors.blue.shade700;
      default:
        return Colors.orange.shade700;
    }
  }

  Color _getStatusBgColor(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed':
        return Colors.green.withAlpha(25);
      case 'cancelled':
        return Colors.red.withAlpha(25);
      case 'completed':
        return Colors.blue.withAlpha(25);
      default:
        return Colors.orange.withAlpha(25);
    }
  }
}
