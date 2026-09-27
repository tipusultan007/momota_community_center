import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../providers/booking_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/booking_item.dart';
import '../../models/booking.dart';
import 'booking_details_screen.dart';
import 'booking_form_screen.dart';
import 'all_bookings_screen.dart';
import '../calendar/calendar_screen.dart';

class BookingListScreen extends StatefulWidget {
  const BookingListScreen({super.key});

  @override
  State<BookingListScreen> createState() => _BookingListScreenState();
}

class _BookingListScreenState extends State<BookingListScreen> {
  DateTime _selectedDate = DateTime.now();
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      context.read<BookingProvider>().fetchBookings();
      _scrollToSelectedDate(animate: false);
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollToSelectedDate({bool animate = true}) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!_scrollController.hasClients) return;
      final dayIndex = _selectedDate.day - 1;
      final screenWidth = MediaQuery.of(context).size.width;
      final targetOffset = (dayIndex * 60.0) - (screenWidth / 2) + 30.0;
      final clampedOffset = targetOffset.clamp(0.0, _scrollController.position.maxScrollExtent);
      if (animate) {
        _scrollController.animateTo(
          clampedOffset,
          duration: const Duration(milliseconds: 350),
          curve: Curves.easeOutCubic,
        );
      } else {
        _scrollController.jumpTo(clampedOffset);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(),
            _buildHorizontalDatePicker(),
            Expanded(
              child: Consumer<BookingProvider>(
                builder: (context, provider, _) {
                  if (provider.isLoading && provider.bookings.isEmpty) {
                    return const Center(child: CircularProgressIndicator());
                  }

                  // Group all items by date and then filter by selectedDate
                  final dailyEvents = provider.eventsByDate[DateTime(
                    _selectedDate.year,
                    _selectedDate.month,
                    _selectedDate.day,
                  )] ?? [];

                  return RefreshIndicator(
                    onRefresh: () => provider.fetchBookings(isPullToRefresh: true),
                    child: dailyEvents.isEmpty
                        ? ListView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            children: [
                              SizedBox(height: MediaQuery.of(context).size.height * 0.15),
                              _buildEmptyState(),
                            ],
                          )
                        : ListView.builder(
                            physics: const AlwaysScrollableScrollPhysics(),
                            padding: const EdgeInsets.all(16),
                            itemCount: dailyEvents.length,
                            itemBuilder: (context, index) {
                              final event = dailyEvents[index];
                              return _buildMajesticCard(event);
                            },
                          ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _changeMonth(int delta) {
    setState(() {
      final now = DateTime.now();
      final newMonth = _selectedDate.month + delta;
      final newYear = _selectedDate.year + (newMonth < 1 ? -1 : (newMonth > 12 ? 1 : 0));
      final normalizedMonth = ((newMonth - 1) % 12) + 1;
      final maxDays = DateTime(newYear, normalizedMonth + 1, 0).day;
      
      int newDay = 1;
      if (newYear == now.year && normalizedMonth == now.month) {
        newDay = now.day;
      } else if (_selectedDate.day <= maxDays) {
        newDay = _selectedDate.day;
      }
      _selectedDate = DateTime(newYear, normalizedMonth, newDay);
    });
    _scrollToSelectedDate();
  }

  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 6, 20, 10),
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            OutlinedButton.icon(
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const AllBookingsScreen()),
              ),
              icon: const Icon(Icons.receipt_long_rounded, size: 16, color: Color(0xFFF5A623)),
              label: const Text('সকল বুকিং', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF004D40))),
              style: OutlinedButton.styleFrom(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
                side: const BorderSide(color: Color(0xFFF5A623), width: 1.5),
                backgroundColor: const Color(0xFFFFF7EB),
                minimumSize: const Size(0, 38),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            ),
            const SizedBox(width: 8),
            OutlinedButton.icon(
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const CalendarScreen()),
              ),
              icon: const Icon(Icons.calendar_month_rounded, size: 16, color: AppTheme.primaryEmerald),
              label: const Text('মাসিক ভিউ', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.primaryEmerald)),
              style: OutlinedButton.styleFrom(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
                side: const BorderSide(color: AppTheme.primaryEmerald, width: 1.2),
                minimumSize: const Size(0, 38),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            ),
            const SizedBox(width: 8),
            ElevatedButton.icon(
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => BookingFormScreen(
                    initialDate: _selectedDate.toIso8601String().split('T')[0],
                  ),
                ),
              ),
              icon: const Icon(Icons.add, size: 16, color: Colors.white),
              label: const Text('নতুন বুকিং', style: TextStyle(fontSize: 12, color: Colors.white, fontWeight: FontWeight.bold)),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 0),
                backgroundColor: AppTheme.textNavy,
                minimumSize: const Size(0, 38),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHorizontalDatePicker() {
    final monthName = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ][_selectedDate.month - 1];

    final daysInMonth = DateTime(_selectedDate.year, _selectedDate.month + 1, 0).day;

    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              GestureDetector(
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const CalendarScreen()),
                ),
                child: Row(
                  children: [
                    Text(
                      '${monthName.toUpperCase()} ${_selectedDate.year}',
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800, letterSpacing: 1.0, color: AppTheme.textNavy),
                    ),
                    const SizedBox(width: 4),
                    const Icon(Icons.arrow_drop_down, color: AppTheme.textNavy, size: 18),
                  ],
                ),
              ),
              Row(
                children: [
                  IconButton(
                    onPressed: () => _changeMonth(-1),
                    icon: const Icon(Icons.chevron_left_rounded, size: 24, color: AppTheme.textNavy),
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
                    tooltip: 'পূর্ববর্তী মাস',
                    style: IconButton.styleFrom(
                      backgroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                        side: BorderSide(color: Colors.grey.withAlpha(40)),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton(
                    onPressed: () => _changeMonth(1),
                    icon: const Icon(Icons.chevron_right_rounded, size: 24, color: AppTheme.textNavy),
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
                    tooltip: 'পরবর্তী মাস',
                    style: IconButton.styleFrom(
                      backgroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                        side: BorderSide(color: Colors.grey.withAlpha(40)),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
        Consumer<BookingProvider>(
          builder: (context, provider, _) {
            return Container(
              height: 96,
              margin: const EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: Colors.grey.withAlpha(20)),
                boxShadow: [
                  BoxShadow(color: Colors.black.withAlpha(4), blurRadius: 10, offset: const Offset(0, 3)),
                ],
              ),
              child: ListView.builder(
                controller: _scrollController,
                scrollDirection: Axis.horizontal,
                physics: const BouncingScrollPhysics(),
                itemCount: daysInMonth,
                itemBuilder: (context, index) {
                  final date = DateTime(_selectedDate.year, _selectedDate.month, index + 1);
                  final isSelected = isSameDay(_selectedDate, date);
                  final isToday = isSameDay(DateTime.now(), date);
                  final dayName = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][date.weekday - 1];

                  final normalizedDate = DateTime(date.year, date.month, date.day);
                  final hasBookings = (provider.eventsByDate[normalizedDate] ?? []).isNotEmpty;

                  return GestureDetector(
                    onTap: () {
                      setState(() => _selectedDate = date);
                      _scrollToSelectedDate();
                    },
                    child: Container(
                      width: 52,
                      margin: const EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                      decoration: BoxDecoration(
                        color: isSelected
                            ? AppTheme.primaryEmerald
                            : (isToday ? AppTheme.primaryEmerald.withAlpha(20) : Colors.transparent),
                        borderRadius: BorderRadius.circular(20),
                        border: isSelected
                            ? null
                            : (isToday ? Border.all(color: AppTheme.primaryEmerald, width: 1.2) : null),
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            dayName.toUpperCase(),
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
                              color: isSelected
                                  ? Colors.white
                                  : (isToday ? AppTheme.primaryEmerald : Colors.grey.shade600),
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            '${date.day}',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: isSelected
                                  ? Colors.white
                                  : (isToday ? AppTheme.primaryEmerald : AppTheme.textNavy),
                            ),
                          ),
                          const SizedBox(height: 4),
                          if (hasBookings)
                            Container(
                              width: 6,
                              height: 6,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                color: isSelected ? Colors.amberAccent : AppTheme.primaryEmerald,
                              ),
                            )
                          else
                            const SizedBox(height: 6),
                        ],
                      ),
                    ),
                  );
                },
              ),
            );
          },
        ),
      ],
    );
  }

  Widget _buildMajesticCard(Map<String, dynamic> event) {
    final BookingItem item = event['item'];
    final Booking booking = event['booking'];

    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => BookingDetailsScreen(booking: booking))),
      child: Card(
        margin: const EdgeInsets.only(bottom: 16),
        elevation: 0,
        color: _getCardBgColor(booking.status),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(24),
          side: BorderSide(color: _getStatusColor(booking.status).withAlpha(30)),
        ),
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(color: _getSlotColor(item.slot).withAlpha(15), borderRadius: BorderRadius.circular(10)),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.access_time, size: 10, color: _getSlotColor(item.slot)),
                        const SizedBox(width: 4),
                        Text(_translateSlot(item.slot), style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1.2, color: _getSlotColor(item.slot))),
                      ],
                    ),
                  ),
                  _buildStatusBadge(booking.status),
                ],
              ),
              const SizedBox(height: 16),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(booking.customerName, style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 18, color: AppTheme.textNavy)),
                        const SizedBox(height: 2),
                        Row(
                          children: [
                            const Icon(Icons.phone_outlined, size: 12, color: Colors.grey),
                            const SizedBox(width: 4),
                            Text(booking.customerPhone, style: const TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.w600)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(color: AppTheme.backgroundSlate, borderRadius: BorderRadius.circular(12)),
                    child: Column(
                      children: [
                        Text(
                          DateFormat('dd').format(DateTime.parse(item.eventDate)),
                          style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 18, color: AppTheme.primaryEmerald),
                        ),
                        Text(
                          DateFormat('MMM yyyy').format(DateTime.parse(item.eventDate)),
                          style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [
                  _infoChip(Icons.tag_rounded, 'বুকিং #${booking.id}'),
                  _infoChip(
                    _getEventIcon(item.eventType),
                    item.eventType.isNotEmpty ? item.eventType : 'অনুষ্ঠান',
                    color: AppTheme.primaryEmerald,
                  ),
                  if (booking.dueAmount > 0)
                    _infoChip(
                      Icons.pending_actions_rounded,
                      'বকেয়া: ৳${NumberFormat('#,##0').format(booking.dueAmount)}',
                      color: Colors.redAccent,
                    )
                  else
                    _infoChip(
                      Icons.check_circle_outline_rounded,
                      'পরিশোধিত',
                      color: Colors.green,
                    ),
                ],
              ),
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 16),
                child: Divider(height: 1),
              ),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _statsBadge(Icons.people_outline, '${booking.totalGuests}', 'মেহমান'),
                  _statsBadge(Icons.table_bar_outlined, '${booking.totalTables}', 'টেবিল'),
                  _statsBadge(Icons.person_pin_outlined, '${booking.totalServers}', 'সার্ভার'),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  IconData _getEventIcon(String eventType) {
    final lower = eventType.toLowerCase();
    if (lower.contains('corporate') || lower.contains('অফিস') || lower.contains('মিটিং') || lower.contains('office') || lower.contains('meeting') || lower.contains('কনফারেন্স') || lower.contains('সেমিনার') || lower.contains('এজিএম') || lower.contains('প্রেস')) {
      return Icons.business_center_rounded;
    } else if (lower.contains('wedding') || lower.contains('বিয়ে') || lower.contains('বিবাহ') || lower.contains('reception') || lower.contains('আকদ') || lower.contains('এনগেজমেন্ট') || lower.contains('বৌভাত') || lower.contains('ওয়ালিমা') || lower.contains('বিবাহবার্ষিকী')) {
      return Icons.favorite_rounded;
    } else if (lower.contains('birthday') || lower.contains('জন্মদিন') || lower.contains('আকিকা')) {
      return Icons.cake_rounded;
    } else if (lower.contains('হলুদ') || lower.contains('মেহেদি') || lower.contains('holud') || lower.contains('mehendi')) {
      return Icons.celebration_rounded;
    } else if (lower.contains('ইফতার') || lower.contains('দোয়া') || lower.contains('মিলাদ')) {
      return Icons.nights_stay_rounded;
    } else if (lower.contains('সাংস্কৃতিক') || lower.contains('কনসার্ট')) {
      return Icons.music_note_rounded;
    } else if (lower.contains('সমাবর্তন') || lower.contains('র্যাগ')) {
      return Icons.school_rounded;
    } else if (lower.contains('মেলা') || lower.contains('প্রদর্শনী')) {
      return Icons.storefront_rounded;
    } else if (lower.contains('পুনর্মিলনী') || lower.contains('রিইউনিয়ন')) {
      return Icons.groups_rounded;
    }
    return Icons.celebration_rounded;
  }

  Widget _statsBadge(IconData icon, String value, String label) {
    return Column(
      children: [
        Row(
          children: [
            Icon(icon, size: 16, color: AppTheme.textNavy),
            const SizedBox(width: 6),
            Text(value, style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 15, color: AppTheme.textNavy)),
          ],
        ),
        Text(label, style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.w500)),
      ],
    );
  }

  Widget _infoChip(IconData icon, String label, {Color? color}) {
    final chipColor = color ?? Colors.grey.shade700;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: (color != null) ? color.withAlpha(20) : AppTheme.backgroundSlate,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 13, color: chipColor),
          const SizedBox(width: 6),
          Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: chipColor)),
        ],
      ),
    );
  }


  Widget _buildStatusBadge(String status) {
    Color color;
    String label;

    switch (status.toLowerCase()) {
      case 'confirmed':
        color = AppTheme.primaryEmerald;
        label = 'নিশ্চিত';
        break;
      case 'cancelled':
        color = Colors.red;
        label = 'বাতিল';
        break;
      case 'completed':
        color = Colors.blue;
        label = 'সম্পন্ন';
        break;
      default:
        color = Colors.orange;
        label = 'পেন্ডিং';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withAlpha(20),
        borderRadius: BorderRadius.circular(30),
      ),
      child: Text(label, style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold)),
    );
  }

  Color _getSlotColor(String slot) {
    if (slot == 'day' || slot == 'morning') return AppTheme.primaryEmerald;
    if (slot == 'night' || slot == 'evening') return Colors.orange;
    return Colors.blue;
  }


  String _translateSlot(String slot) {
    if (slot == 'day' || slot == 'morning') return 'দিন (Day)';
    if (slot == 'night' || slot == 'evening') return 'রাত (Night)';
    return 'সারাদিন';
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed': return AppTheme.primaryEmerald;
      case 'cancelled': return Colors.red;
      case 'completed': return Colors.blue;
      default: return Colors.orange;
    }
  }

  Color _getCardBgColor(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed': return AppTheme.primaryEmerald.withAlpha(10);
      case 'cancelled': return Colors.red.withAlpha(10);
      case 'completed': return Colors.blue.withAlpha(10);
      default: return Colors.white;
    }
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.event_available, size: 64, color: Colors.grey.shade300),
          const SizedBox(height: 16),
          const Text('এই দিনে কোনো ইভেন্ট নেই।', style: TextStyle(color: Colors.grey)),
        ],
      ),
    );
  }

  bool isSameDay(DateTime? a, DateTime? b) {
    if (a == null || b == null) return false;
    return a.year == b.year && a.month == b.month && a.day == b.day;
  }
}
