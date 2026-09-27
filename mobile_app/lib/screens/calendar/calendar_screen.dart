import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:table_calendar/table_calendar.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../providers/booking_provider.dart';
import '../../models/booking.dart';
import '../../models/booking_item.dart';
import '../../theme/app_theme.dart';
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

  @override
  void initState() {
    super.initState();
    _selectedDay = DateTime(_focusedDay.year, _focusedDay.month, _focusedDay.day);
    Future.microtask(() {
      if (mounted) {
        context.read<BookingProvider>().fetchBookings();
      }
    });
  }

  List<Map<String, dynamic>> _getEventsForDay(DateTime day, Map<DateTime, List<Map<String, dynamic>>> eventsMap) {
    final normalizedDate = DateTime(day.year, day.month, day.day);
    return eventsMap[normalizedDate] ?? [];
  }

  void _onPreviousMonth() {
    setState(() {
      _focusedDay = DateTime(_focusedDay.year, _focusedDay.month - 1, 1);
    });
  }

  void _onNextMonth() {
    setState(() {
      _focusedDay = DateTime(_focusedDay.year, _focusedDay.month + 1, 1);
    });
  }

  void _onToday() {
    setState(() {
      final now = DateTime.now();
      _focusedDay = now;
      _selectedDay = DateTime(now.year, now.month, now.day);
    });
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
            onRefresh: provider.fetchBookings,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Month Navigation & Summary Header
                  _buildMonthNavigatorBar(monthlyCount),

                  // Calendar Card with Booking Badges
                  _buildCalendarCard(eventsMap),

                  // Slot Color Legend
                  _buildLegendBar(),

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
                    engMonth,
                    style: TextStyle(fontSize: 11, color: Colors.grey.shade600, fontWeight: FontWeight.w600),
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
          },
          onPageChanged: (focusedDay) {
            setState(() {
              _focusedDay = focusedDay;
            });
          },
          headerVisible: false, // We use our custom month navigator
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

              return _buildDayCell(
                day: day,
                isSelected: false,
                isToday: isSameDay(day, DateTime.now()),
                events: events,
                isBooked: isBooked,
              );
            },
            // Selected Day Cell
            selectedBuilder: (context, day, focusedDay) {
              final events = _getEventsForDay(day, eventsMap);
              return _buildDayCell(
                day: day,
                isSelected: true,
                isToday: isSameDay(day, DateTime.now()),
                events: events,
                isBooked: events.isNotEmpty,
              );
            },
            // Today Cell
            todayBuilder: (context, day, focusedDay) {
              final events = _getEventsForDay(day, eventsMap);
              final isSelected = isSameDay(_selectedDay, day);
              return _buildDayCell(
                day: day,
                isSelected: isSelected,
                isToday: true,
                events: events,
                isBooked: events.isNotEmpty,
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
  }) {
    bool hasDaySlot = false;
    bool hasNightSlot = false;

    for (var event in events) {
      final BookingItem item = event['item'];
      final s = (item.slot == 'night' || item.slot == 'evening') ? 'night' : 'day';
      if (s == 'night') hasNightSlot = true;
      if (s == 'day') hasDaySlot = true;
    }

    Color cellBgColor = Colors.transparent;
    Color textColor = AppTheme.textNavy;
    Border? border;

    if (isSelected) {
      cellBgColor = AppTheme.primaryEmerald;
      textColor = Colors.white;
    } else if (isToday) {
      cellBgColor = AppTheme.primaryEmerald.withAlpha(20);
      textColor = AppTheme.primaryEmerald;
      border = Border.all(color: AppTheme.primaryEmerald, width: 1.5);
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
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            '${day.day}',
            style: TextStyle(
              fontSize: 14,
              fontWeight: (isSelected || isToday || isBooked) ? FontWeight.bold : FontWeight.w500,
              color: textColor,
            ),
          ),
          const SizedBox(height: 2),
          // Booking indicator symbol
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
                      color: isSelected ? Colors.amberAccent : const Color(0xFFF59E0B), // Sun / Day (Amber)
                    ),
                  ),
                if (hasNightSlot)
                  Container(
                    width: 6,
                    height: 6,
                    margin: const EdgeInsets.symmetric(horizontal: 1),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: isSelected ? Colors.lightBlueAccent : const Color(0xFF7C3AED), // Moon / Night (Purple)
                    ),
                  ),
              ],
            )
          else
            const SizedBox(height: 6),
        ],
      ),
    );
  }

  Widget _buildLegendBar() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white.withAlpha(180),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildLegendItem(const Color(0xFFF59E0B), 'দিন স্লট (Day)'),
          _buildLegendItem(const Color(0xFF7C3AED), 'রাত স্লট (Night)'),
          _buildLegendItem(AppTheme.primaryEmerald, 'নির্বাচিত দিন'),
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
        const SizedBox(width: 6),
        Text(
          label,
          style: TextStyle(fontSize: 10, color: Colors.grey.shade700, fontWeight: FontWeight.bold),
        ),
      ],
    );
  }

  Widget _buildSelectedDateSection(List<Map<String, dynamic>> selectedEvents) {
    final formattedDate = _formatBanglaDate(_selectedDay);

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
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
