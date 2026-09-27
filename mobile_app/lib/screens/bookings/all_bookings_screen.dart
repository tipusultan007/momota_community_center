import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/booking_provider.dart';
import '../../models/booking.dart';
import '../../theme/app_theme.dart';
import 'booking_details_screen.dart';
import 'booking_form_screen.dart';
import 'components/payment_dialog.dart';

class AllBookingsScreen extends StatefulWidget {
  const AllBookingsScreen({super.key});

  @override
  State<AllBookingsScreen> createState() => _AllBookingsScreenState();
}

class _AllBookingsScreenState extends State<AllBookingsScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _selectedStatus = 'all';
  String _paymentFilter = 'all'; // all, due, paid
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    Future.microtask(() => context.read<BookingProvider>().fetchBookings());
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  List<Booking> _filterBookings(List<Booking> allBookings) {
    return allBookings.where((b) {
      // 1. Status Filter
      if (_selectedStatus != 'all' && b.status.toLowerCase() != _selectedStatus.toLowerCase()) {
        return false;
      }

      // 2. Payment Filter
      if (_paymentFilter == 'due' && b.dueAmount <= 0) {
        return false;
      } else if (_paymentFilter == 'paid' && b.dueAmount > 0) {
        return false;
      }

      // 3. Search Query
      if (_searchQuery.isNotEmpty) {
        final q = _searchQuery.toLowerCase();
        final nameMatch = b.customerName.toLowerCase().contains(q);
        final phoneMatch = b.customerPhone.toLowerCase().contains(q);
        final idMatch = b.id.toString().contains(q);
        final notesMatch = (b.notes ?? '').toLowerCase().contains(q);
        final itemMatch = b.items.any((i) =>
            i.eventDate.contains(q) ||
            i.eventType.toLowerCase().contains(q) ||
            i.slot.toLowerCase().contains(q));

        if (!nameMatch && !phoneMatch && !idMatch && !notesMatch && !itemMatch) {
          return false;
        }
      }

      return true;
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        title: Text(
          'সকল বুকিং তালিকা',
          style: GoogleFonts.manrope(
            fontWeight: FontWeight.w900,
            fontSize: 18,
            color: AppTheme.textNavy,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: AppTheme.primaryTeal),
            tooltip: 'রিফ্রেশ',
            onPressed: () => context.read<BookingProvider>().fetchBookings(),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const BookingFormScreen()),
        ),
        backgroundColor: AppTheme.primaryTeal,
        icon: const Icon(Icons.add_rounded, color: AppTheme.accentGold),
        label: Text(
          'নতুন বুকিং',
          style: GoogleFonts.manrope(
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
      ),
      body: Consumer<BookingProvider>(
        builder: (context, provider, _) {
          final allBookings = provider.bookings;
          final filtered = _filterBookings(allBookings);

          final totalBookings = allBookings.length;
          final totalDue = allBookings.fold(0.0, (sum, b) => sum + (b.dueAmount > 0 ? b.dueAmount : 0));

          return RefreshIndicator(
            onRefresh: provider.fetchBookings,
            color: AppTheme.primaryTeal,
            child: CustomScrollView(
              physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
              slivers: [
                // Top KPI Summary Header
                SliverToBoxAdapter(
                  child: Container(
                    margin: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [AppTheme.primaryTeal, AppTheme.primaryTealDark],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: AppTheme.primaryTeal.withAlpha(50),
                          blurRadius: 15,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _buildKpiColumn('মোট বুকিং', '$totalBookings টি', Icons.event_available_outlined, AppTheme.accentGold),
                        Container(width: 1, height: 40, color: Colors.white24),
                        _buildKpiColumn('খুঁজে পাওয়া বুকিং', '${filtered.length} টি', Icons.filter_alt_outlined, Colors.white),
                        Container(width: 1, height: 40, color: Colors.white24),
                        _buildKpiColumn('মোট বকেয়া', '৳${NumberFormat('#,##0').format(totalDue)}', Icons.account_balance_wallet_outlined, const Color(0xFFFFB4A2)),
                      ],
                    ),
                  ),
                ),

                // Search Bar
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    child: TextField(
                      controller: _searchController,
                      onChanged: (val) => setState(() => _searchQuery = val.trim()),
                      decoration: InputDecoration(
                        hintText: 'গ্রাহকের নাম, মোবাইল বা বুকিং নম্বর দিয়ে খুঁজুন...',
                        hintStyle: TextStyle(color: Colors.grey.shade500, fontSize: 13),
                        prefixIcon: const Icon(Icons.search_rounded, color: AppTheme.primaryTeal),
                        suffixIcon: _searchQuery.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear_rounded, size: 20),
                                onPressed: () {
                                  _searchController.clear();
                                  setState(() => _searchQuery = '');
                                },
                              )
                            : null,
                        fillColor: Colors.white,
                        filled: true,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: BorderSide(color: Colors.grey.withAlpha(30)),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: BorderSide(color: Colors.grey.withAlpha(30)),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: const BorderSide(color: AppTheme.primaryTeal, width: 1.5),
                        ),
                      ),
                    ),
                  ),
                ),

                // Status Filter Chips
                SliverToBoxAdapter(
                  child: SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                    child: Row(
                      children: [
                        _statusChip('all', 'সব বুকিং', Icons.all_inclusive_rounded),
                        const SizedBox(width: 8),
                        _statusChip('confirmed', 'নিশ্চিত (Confirmed)', Icons.check_circle_outline_rounded),
                        const SizedBox(width: 8),
                        _statusChip('pending', 'পেন্ডিং (Pending)', Icons.hourglass_top_rounded),
                        const SizedBox(width: 8),
                        _statusChip('completed', 'সম্পন্ন (Completed)', Icons.task_alt_rounded),
                        const SizedBox(width: 8),
                        _statusChip('cancelled', 'বাতিল (Cancelled)', Icons.cancel_outlined),
                      ],
                    ),
                  ),
                ),

                // Payment Status Chips
                SliverToBoxAdapter(
                  child: SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    padding: const EdgeInsets.fromLTRB(16, 4, 16, 12),
                    child: Row(
                      children: [
                        _paymentChip('all', 'সকল পেমেন্ট'),
                        const SizedBox(width: 8),
                        _paymentChip('due', '⚠️ বকেয়া রয়েছে'),
                        const SizedBox(width: 8),
                        _paymentChip('paid', '✓ সম্পূর্ণ পরিশোধিত'),
                      ],
                    ),
                  ),
                ),

                // Bookings List or Loading or Empty
                if (provider.isLoading && allBookings.isEmpty)
                  const SliverFillRemaining(
                    child: Center(
                      child: CircularProgressIndicator(color: AppTheme.primaryTeal),
                    ),
                  )
                else if (filtered.isEmpty)
                  SliverFillRemaining(
                    child: Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.event_busy_rounded, size: 64, color: Colors.grey.shade400),
                          const SizedBox(height: 16),
                          Text(
                            'কোনো বুকিং পাওয়া যায়নি',
                            style: GoogleFonts.manrope(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.textNavy,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            _searchQuery.isNotEmpty || _selectedStatus != 'all' || _paymentFilter != 'all'
                                ? 'ফিল্টার পরিবর্তন করে পুনরায় চেষ্টা করুন।'
                                : 'নতুন বুকিং যোগ করতে নিচের বাটনে চাপ দিন।',
                            style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
                          ),
                        ],
                      ),
                    ),
                  )
                else
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(16, 0, 16, 80),
                    sliver: SliverList(
                      delegate: SliverChildBuilderDelegate(
                        (context, index) {
                          final booking = filtered[index];
                          return _buildBookingCard(booking);
                        },
                        childCount: filtered.length,
                      ),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildKpiColumn(String label, String value, IconData icon, Color color) {
    return Column(
      children: [
        Icon(icon, color: color, size: 20),
        const SizedBox(height: 6),
        Text(
          value,
          style: GoogleFonts.manrope(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15),
        ),
        Text(
          label,
          style: const TextStyle(color: Colors.white70, fontSize: 10, fontWeight: FontWeight.w600),
        ),
      ],
    );
  }

  Widget _statusChip(String status, String label, IconData icon) {
    final isSelected = _selectedStatus == status;
    return ChoiceChip(
      showCheckmark: false,
      selected: isSelected,
      label: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: isSelected ? Colors.white : AppTheme.primaryTeal),
          const SizedBox(width: 6),
          Text(label),
        ],
      ),
      labelStyle: TextStyle(
        color: isSelected ? Colors.white : AppTheme.textNavy,
        fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
        fontSize: 12,
      ),
      selectedColor: AppTheme.primaryTeal,
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(
          color: isSelected ? AppTheme.primaryTeal : Colors.grey.withAlpha(40),
        ),
      ),
      onSelected: (val) {
        if (val) setState(() => _selectedStatus = status);
      },
    );
  }

  Widget _paymentChip(String filter, String label) {
    final isSelected = _paymentFilter == filter;
    return ChoiceChip(
      showCheckmark: false,
      selected: isSelected,
      label: Text(label),
      labelStyle: TextStyle(
        color: isSelected ? AppTheme.primaryTealDark : Colors.grey.shade700,
        fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
        fontSize: 11,
      ),
      selectedColor: AppTheme.accentGold.withAlpha(40),
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(10),
        side: BorderSide(
          color: isSelected ? AppTheme.accentGold : Colors.grey.withAlpha(30),
          width: isSelected ? 1.4 : 1.0,
        ),
      ),
      onSelected: (val) {
        if (val) setState(() => _paymentFilter = filter);
      },
    );
  }

  Widget _buildBookingCard(Booking booking) {
    final hasDue = booking.dueAmount > 0;
    final item = booking.items.isNotEmpty ? booking.items.first : null;
    final eventDateStr = item?.eventDate ?? '';
    final slotStr = item?.slot ?? 'day';
    final isDay = slotStr == 'day' || slotStr == 'morning';

    DateTime? parsedDate;
    try {
      if (eventDateStr.isNotEmpty) parsedDate = DateTime.parse(eventDateStr);
    } catch (_) {}

    final dateFormatted = parsedDate != null
        ? DateFormat('dd MMM, yyyy').format(parsedDate)
        : (eventDateStr.isNotEmpty ? eventDateStr : 'তারিখ নেই');

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: hasDue ? const Color(0xFFFFD8D0) : AppTheme.primaryTeal.withAlpha(25),
          width: 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(4),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
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
              // Header: ID + Status + Quick Call
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryTeal.withAlpha(15),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: AppTheme.primaryTeal.withAlpha(30)),
                        ),
                        child: Text(
                          '#${booking.id}',
                          style: const TextStyle(
                            color: AppTheme.primaryTeal,
                            fontWeight: FontWeight.w900,
                            fontSize: 12,
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      _buildStatusPill(booking.status),
                    ],
                  ),
                  if (booking.customerPhone.isNotEmpty && booking.customerPhone != 'N/A')
                    InkWell(
                      onTap: () async {
                        final uri = Uri.parse('tel:${booking.customerPhone}');
                        if (await canLaunchUrl(uri)) {
                          await launchUrl(uri);
                        }
                      },
                      borderRadius: BorderRadius.circular(8),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppTheme.accentGold.withAlpha(20),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: AppTheme.accentGold.withAlpha(50)),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.phone_in_talk_rounded, size: 14, color: AppTheme.accentGoldDark),
                            const SizedBox(width: 4),
                            Text(
                              booking.customerPhone,
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.accentGoldDark,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                ],
              ),

              const SizedBox(height: 12),

              // Customer Name & Event Type
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CircleAvatar(
                    radius: 20,
                    backgroundColor: AppTheme.primaryTeal,
                    child: Text(
                      booking.customerName.isNotEmpty ? booking.customerName.substring(0, 1).toUpperCase() : 'C',
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          booking.customerName,
                          style: GoogleFonts.manrope(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: AppTheme.textNavy,
                          ),
                        ),
                        if (item?.eventType != null)
                          Text(
                            'ইভেন্ট: ${item!.eventType}',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey.shade600,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                      ],
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 14),

              // Event Schedule details pill
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                decoration: BoxDecoration(
                  color: AppTheme.backgroundSlate,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.withAlpha(20)),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.calendar_today_rounded, size: 15, color: AppTheme.primaryTeal),
                        const SizedBox(width: 6),
                        Text(
                          dateFormatted,
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                            color: AppTheme.textNavy,
                          ),
                        ),
                      ],
                    ),
                    Row(
                      children: [
                        Icon(
                          isDay ? Icons.wb_sunny_rounded : Icons.nightlight_round,
                          size: 15,
                          color: isDay ? AppTheme.accentGoldDark : Colors.indigo,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          isDay ? 'দিন (Day)' : 'রাত (Night)',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
                            color: isDay ? AppTheme.accentGoldDark : Colors.indigo,
                          ),
                        ),
                      ],
                    ),
                    if (item != null && item.guestCount > 0)
                      Row(
                        children: [
                          const Icon(Icons.people_alt_outlined, size: 14, color: Colors.grey),
                          const SizedBox(width: 4),
                          Text(
                            '${item.guestCount} জন',
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.grey),
                          ),
                        ],
                      ),
                  ],
                ),
              ),

              const SizedBox(height: 14),

              // Financial strip: Total, Paid, Due
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _priceColumn('মোট চুক্তি', '৳${NumberFormat('#,##0').format(booking.totalAmount)}', Colors.grey.shade800),
                  _priceColumn('পরিশোধিত', '৳${NumberFormat('#,##0').format(booking.paidAmount)}', AppTheme.primaryTeal),
                  _priceColumn(
                    'বকেয়া',
                    hasDue ? '৳${NumberFormat('#,##0').format(booking.dueAmount)}' : 'পরিশোধিত',
                    hasDue ? Colors.redAccent : AppTheme.primaryTeal,
                    isDue: hasDue,
                  ),
                ],
              ),

              const Divider(height: 24),

              // Bottom Actions
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  if (hasDue)
                    TextButton.icon(
                      onPressed: () {
                        showDialog(
                          context: context,
                          builder: (_) => PaymentDialog(
                            bookingId: booking.id,
                            dueAmount: booking.dueAmount,
                          ),
                        );
                      },
                      icon: const Icon(Icons.payment_rounded, size: 16, color: AppTheme.accentGoldDark),
                      label: const Text(
                        'পেমেন্ট জমা',
                        style: TextStyle(color: AppTheme.accentGoldDark, fontWeight: FontWeight.bold, fontSize: 13),
                      ),
                    ),
                  const SizedBox(width: 8),
                  ElevatedButton.icon(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => BookingDetailsScreen(booking: booking),
                        ),
                      );
                    },
                    icon: const Icon(Icons.arrow_forward_rounded, size: 16, color: Colors.white),
                    label: const Text('বিস্তারিত', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primaryTeal,
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 0),
                      minimumSize: const Size(0, 36),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatusPill(String status) {
    Color bg;
    Color fg;
    String label;

    switch (status.toLowerCase()) {
      case 'confirmed':
        bg = AppTheme.primaryTeal.withAlpha(20);
        fg = AppTheme.primaryTeal;
        label = 'নিশ্চিত';
        break;
      case 'cancelled':
        bg = Colors.red.withAlpha(20);
        fg = Colors.red;
        label = 'বাতিল';
        break;
      case 'completed':
        bg = Colors.blue.withAlpha(20);
        fg = Colors.blue;
        label = 'সম্পন্ন';
        break;
      default:
        bg = AppTheme.accentGold.withAlpha(25);
        fg = AppTheme.accentGoldDark;
        label = 'পেন্ডিং';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: fg.withAlpha(50)),
      ),
      child: Text(
        label,
        style: TextStyle(color: fg, fontSize: 10, fontWeight: FontWeight.bold),
      ),
    );
  }

  Widget _priceColumn(String label, String value, Color color, {bool isDue = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: TextStyle(color: Colors.grey.shade500, fontSize: 10, fontWeight: FontWeight.w600)),
        const SizedBox(height: 2),
        Text(
          value,
          style: GoogleFonts.manrope(
            color: color,
            fontWeight: FontWeight.w900,
            fontSize: isDue ? 14 : 13,
          ),
        ),
      ],
    );
  }
}
