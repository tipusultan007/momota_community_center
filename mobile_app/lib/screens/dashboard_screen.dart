import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';
import '../theme/app_theme.dart';
import '../models/booking.dart';
import 'bookings/booking_details_screen.dart';
import 'bookings/booking_form_screen.dart';
import 'bookings/all_bookings_screen.dart';
import 'calendar/calendar_screen.dart';
import 'accounting/transaction_form_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final currencyFormat = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().fetchDashboard();
    });
  }

  String _getBengaliToday() {
    final now = DateTime.now();
    const bnMonths = [
      'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
      'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'
    ];
    return '${now.day} ${bnMonths[now.month - 1]} ${now.year}';
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final dashboard = Provider.of<DashboardProvider>(context);

    final stats = Map<String, dynamic>.from(dashboard.data?['stats'] ?? {});
    final bookings = (dashboard.data?['recent_bookings'] as List?) ?? [];

    final num totalIncome = num.tryParse(stats['total_income']?.toString() ?? '0') ?? 0;
    final num totalExpense = num.tryParse(stats['total_expense']?.toString() ?? '0') ?? 0;
    final num netBalance = totalIncome - totalExpense;
    final int totalBookings = int.tryParse(stats['total_bookings']?.toString() ?? '0') ?? 0;
    final int totalStaff = int.tryParse(stats['total_staff']?.toString() ?? '0') ?? 0;

    final userName = auth.user?['name'] ?? 'ম্যানেজার';

    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      body: SafeArea(
        child: (dashboard.isLoading && dashboard.data == null)
            ? const Center(child: CircularProgressIndicator())
            : RefreshIndicator(
                onRefresh: () => dashboard.fetchDashboard(isPullToRefresh: true),
                child: SingleChildScrollView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: 10),

                      // 1. Executive Top Header (Compact)
                      _buildExecutiveHeader(userName),

                      const SizedBox(height: 12),

                      // 2. Financial Overview Hero Card (Compact & High Impact)
                      _buildFinancialHeroCard(netBalance, totalIncome, totalExpense),

                      const SizedBox(height: 12),

                      // 3. Compact Operational Metrics (2x2 Grid)
                      _buildCompactMetrics(totalBookings, totalStaff, totalIncome, totalExpense),

                      const SizedBox(height: 14),

                      // 4. Quick Actions Row
                      _buildQuickActions(),

                      const SizedBox(height: 16),

                      // 5. Upcoming Bookings Header & List
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Container(
                                width: 4,
                                height: 16,
                                decoration: BoxDecoration(
                                  color: AppTheme.primaryEmerald,
                                  borderRadius: BorderRadius.circular(2),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Text(
                                'আসন্ন বুকিং ও ইভেন্টসমূহ',
                                style: GoogleFonts.manrope(
                                  fontSize: 15,
                                  fontWeight: FontWeight.w800,
                                  color: AppTheme.textNavy,
                                ),
                              ),
                            ],
                          ),
                          TextButton(
                            style: TextButton.styleFrom(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              minimumSize: Size.zero,
                              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                            ),
                            onPressed: () => Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const AllBookingsScreen()),
                            ),
                            child: Row(
                              children: [
                                Text(
                                  'সকল বুকিং',
                                  style: GoogleFonts.manrope(
                                    color: AppTheme.primaryEmerald,
                                    fontWeight: FontWeight.w700,
                                    fontSize: 12,
                                  ),
                                ),
                                const SizedBox(width: 2),
                                const Icon(Icons.chevron_right_rounded, size: 16, color: AppTheme.primaryEmerald),
                              ],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),

                      // Bookings list or empty state
                      bookings.isEmpty
                          ? _buildEmptyState()
                          : ListView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: bookings.length > 4 ? 4 : bookings.length,
                              itemBuilder: (context, index) {
                                return _buildCompactBookingCard(bookings[index]);
                              },
                            ),

                      const SizedBox(height: 30),
                    ],
                  ),
                ),
              ),
      ),
    );
  }

  // 1. Executive Top Header
  Widget _buildExecutiveHeader(String userName) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withAlpha(20)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [AppTheme.primaryEmerald, AppTheme.primaryTealDark],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Center(
              child: Text(
                userName.isNotEmpty ? userName[0].toUpperCase() : 'M',
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Row(
                  children: [
                    const Text(
                      'শুভ দিন, ',
                      style: TextStyle(fontSize: 11, color: Colors.grey, fontWeight: FontWeight.w500),
                    ),
                    Text(
                      userName,
                      style: GoogleFonts.manrope(
                        fontSize: 13,
                        fontWeight: FontWeight.w800,
                        color: AppTheme.textNavy,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 1),
                const Text(
                  'মমতা কমিউনিটি সেন্টার',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.primaryEmerald,
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              color: AppTheme.primaryEmerald.withAlpha(15),
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: AppTheme.primaryEmerald.withAlpha(30)),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.calendar_today_rounded, size: 12, color: AppTheme.primaryEmerald),
                const SizedBox(width: 5),
                Text(
                  _getBengaliToday(),
                  style: const TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.primaryEmerald,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // 2. Financial Overview Hero Card
  Widget _buildFinancialHeroCard(num netBalance, num totalIncome, num totalExpense) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppTheme.primaryTealDark, AppTheme.primaryTeal],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primaryTeal.withAlpha(50),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(5),
                    decoration: BoxDecoration(
                      color: Colors.white.withAlpha(35),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.account_balance_wallet_rounded, color: Colors.white, size: 14),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'নিট ক্যাশ ব্যালেন্স',
                    style: GoogleFonts.manrope(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: Colors.white.withAlpha(220),
                    ),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: Colors.white.withAlpha(30),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  netBalance >= 0 ? 'পজিটিভ ক্যাশফ্লো' : 'নেগেটিভ',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 9.5,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            '৳ ${currencyFormat.format(netBalance)}',
            style: GoogleFonts.manrope(
              fontSize: 26,
              fontWeight: FontWeight.w900,
              color: Colors.white,
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.black.withAlpha(35),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                Row(
                  children: [
                    const Icon(Icons.arrow_downward_rounded, color: Color(0xFF69F0AE), size: 16),
                    const SizedBox(width: 6),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('মোট আয়', style: TextStyle(color: Colors.white.withAlpha(180), fontSize: 9.5)),
                        Text(
                          '৳ ${currencyFormat.format(totalIncome)}',
                          style: GoogleFonts.manrope(
                            color: Colors.white,
                            fontSize: 13,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                Container(width: 1, height: 26, color: Colors.white24),
                Row(
                  children: [
                    const Icon(Icons.arrow_upward_rounded, color: Color(0xFFFF8A80), size: 16),
                    const SizedBox(width: 6),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('মোট ব্যয়', style: TextStyle(color: Colors.white.withAlpha(180), fontSize: 9.5)),
                        Text(
                          '৳ ${currencyFormat.format(totalExpense)}',
                          style: GoogleFonts.manrope(
                            color: Colors.white,
                            fontSize: 13,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // 3. Compact Operational Metrics (2x2 Grid)
  Widget _buildCompactMetrics(int totalBookings, int totalStaff, num totalIncome, num totalExpense) {
    return Row(
      children: [
        Expanded(
          child: _buildMetricCard(
            title: 'সক্রিয় বুকিং',
            value: '$totalBookings টি',
            icon: Icons.calendar_month_rounded,
            accentColor: const Color(0xFF1E88E5),
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const AllBookingsScreen()),
            ),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildMetricCard(
            title: 'কর্মরত স্টাফ',
            value: '$totalStaff জন',
            icon: Icons.groups_rounded,
            accentColor: const Color(0xFFE65100),
            onTap: () {},
          ),
        ),
      ],
    );
  }

  Widget _buildMetricCard({
    required String title,
    required String value,
    required IconData icon,
    required Color accentColor,
    VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 11),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.grey.withAlpha(20)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(5),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(9),
              decoration: BoxDecoration(
                color: accentColor.withAlpha(22),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: accentColor, size: 20),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    value,
                    style: GoogleFonts.manrope(
                      fontSize: 16,
                      fontWeight: FontWeight.w900,
                      color: AppTheme.textNavy,
                      letterSpacing: -0.3,
                    ),
                  ),
                  Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontSize: 10.5,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey.shade600,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // 4. Quick Actions Row
  Widget _buildQuickActions() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withAlpha(20)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(5),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildActionButton(
            label: 'নতুন বুকিং',
            icon: Icons.add_circle_outline_rounded,
            color: AppTheme.primaryEmerald,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const BookingFormScreen()),
            ),
          ),
          _buildActionButton(
            label: 'আয় যুক্ত',
            icon: Icons.trending_up_rounded,
            color: const Color(0xFF2E7D32),
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const TransactionFormScreen(initialType: 'income')),
            ),
          ),
          _buildActionButton(
            label: 'ব্যয় যুক্ত',
            icon: Icons.trending_down_rounded,
            color: const Color(0xFFC62828),
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const TransactionFormScreen(initialType: 'expense')),
            ),
          ),
          _buildActionButton(
            label: 'ক্যালেন্ডার',
            icon: Icons.event_note_rounded,
            color: const Color(0xFF0277BD),
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const CalendarScreen()),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButton({
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(9),
              decoration: BoxDecoration(
                color: color.withAlpha(20),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w700,
                color: Colors.grey.shade800,
              ),
            ),
          ],
        ),
      ),
    );
  }

  // 5. Compact Recent Booking Card
  Widget _buildCompactBookingCard(dynamic data) {
    final booking = Booking.fromJson(Map<String, dynamic>.from(data));
    final hasItems = booking.items.isNotEmpty;
    final firstItem = hasItems ? booking.items[0] : null;

    final eventType = (firstItem != null && firstItem.eventType.isNotEmpty)
        ? firstItem.eventType
        : (data['hall']?['name'] ?? 'ইভেন্ট');

    final slot = firstItem?.slot ?? '';
    final dateStr = firstItem?.eventDate ?? '';

    String day = '';
    String month = '';
    if (dateStr.isNotEmpty) {
      try {
        final parsed = DateTime.parse(dateStr);
        day = parsed.day.toString();
        const mNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        month = mNames[parsed.month - 1];
      } catch (_) {}
    }

    final isPaid = booking.dueAmount <= 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withAlpha(20)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(4),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: InkWell(
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => BookingDetailsScreen(booking: booking)),
        ),
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          child: Row(
            children: [
              // Date Box
              Container(
                width: 44,
                padding: const EdgeInsets.symmetric(vertical: 6),
                decoration: BoxDecoration(
                  color: AppTheme.primaryEmerald.withAlpha(15),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.primaryEmerald.withAlpha(30)),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      day.isNotEmpty ? day : '#${booking.id}',
                      style: GoogleFonts.manrope(
                        fontSize: 14,
                        fontWeight: FontWeight.w900,
                        color: AppTheme.primaryEmerald,
                      ),
                    ),
                    if (month.isNotEmpty)
                      Text(
                        month.toUpperCase(),
                        style: const TextStyle(
                          fontSize: 9,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.primaryEmerald,
                        ),
                      ),
                  ],
                ),
              ),
              const SizedBox(width: 10),

              // Booking Details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Row(
                      children: [
                        Flexible(
                          child: Text(
                            booking.customerName,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: GoogleFonts.manrope(
                              fontWeight: FontWeight.w800,
                              fontSize: 13.5,
                              color: AppTheme.textNavy,
                            ),
                          ),
                        ),
                        if (slot.isNotEmpty) ...[
                          const SizedBox(width: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                            decoration: BoxDecoration(
                              color: Colors.grey.withAlpha(30),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              slot,
                              style: TextStyle(fontSize: 8.5, fontWeight: FontWeight.w600, color: Colors.grey.shade700),
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 2),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                          decoration: BoxDecoration(
                            color: AppTheme.primaryEmerald.withAlpha(15),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            eventType,
                            style: const TextStyle(
                              fontSize: 9.5,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.primaryEmerald,
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          booking.customerPhone,
                          style: TextStyle(fontSize: 10.5, color: Colors.grey.shade600),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // Financial & Status
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '৳ ${currencyFormat.format(booking.totalAmount)}',
                    style: GoogleFonts.manrope(
                      fontWeight: FontWeight.w900,
                      color: AppTheme.textNavy,
                      fontSize: 13,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: isPaid ? Colors.green.withAlpha(20) : Colors.orange.withAlpha(20),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      isPaid ? 'পরিশোধিত' : 'বকেয়া: ৳${currencyFormat.format(booking.dueAmount)}',
                      style: TextStyle(
                        color: isPaid ? const Color(0xFF2E7D32) : const Color(0xFFE65100),
                        fontSize: 8.5,
                        fontWeight: FontWeight.bold,
                      ),
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

  Widget _buildEmptyState() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 24),
      alignment: Alignment.center,
      child: Column(
        children: [
          Icon(Icons.event_busy_rounded, size: 36, color: Colors.grey.shade400),
          const SizedBox(height: 6),
          Text(
            'কোনো আসন্ন ইভেন্ট পাওয়া যায়নি।',
            style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
          ),
        ],
      ),
    );
  }
}
