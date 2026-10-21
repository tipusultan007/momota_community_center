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
import 'loans/loan_list_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final currencyFormat = NumberFormat('#,##0');

  static const List<String> _bnMonths = [
    'জানুয়ারি',
    'ফেব্রুয়ারি',
    'মার্চ',
    'এপ্রিল',
    'মে',
    'জুন',
    'জুলাই',
    'আগস্ট',
    'সেপ্টেম্বর',
    'অক্টোবর',
    'নভেম্বর',
    'ডিসেম্বর',
  ];

  static const List<String> _bnMonthsShort = [
    'জানু',
    'ফেব',
    'মার্চ',
    'এপ্রি',
    'মে',
    'জুন',
    'জুল',
    'আগ',
    'সেপ্ট',
    'অক্টো',
    'নভে',
    'ডিসে',
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().fetchDashboard();
    });
  }

  num _toNum(dynamic value) {
    if (value == null) return 0;
    if (value is num) return value;
    return num.tryParse(value.toString()) ?? 0;
  }

  int _toInt(dynamic value) => _toNum(value).toInt();

  String _compactNumber(num value) {
    final absolute = value.abs();
    if (absolute >= 10000000) {
      return '${_trimDecimal(value / 10000000)} কোটি';
    }
    if (absolute >= 100000) {
      return '${_trimDecimal(value / 100000)} লাখ';
    }
    if (absolute >= 1000) {
      return '${_trimDecimal(value / 1000)} হাজার';
    }
    return currencyFormat.format(value);
  }

  String _trimDecimal(num value) {
    final text = value.toStringAsFixed(value.abs() >= 100 ? 0 : 1);
    return text.endsWith('.0') ? text.substring(0, text.length - 2) : text;
  }

  String _getBengaliToday() {
    final now = DateTime.now();
    return '${now.day} ${_bnMonths[now.month - 1]} ${now.year}';
  }

  DateTime get _todayDate {
    final now = DateTime.now();
    return DateTime(now.year, now.month, now.day);
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final dashboard = Provider.of<DashboardProvider>(context);

    final stats = Map<String, dynamic>.from(dashboard.data?['stats'] ?? {});
    final upcomingData =
        dashboard.data?['upcoming_bookings'] ??
        dashboard.data?['recent_bookings'];
    final bookings = (upcomingData as List?) ?? const <dynamic>[];

    final num totalIncome = _toNum(stats['total_income']);
    final num totalExpense = _toNum(stats['total_expense']);
    final num netBalance = totalIncome - totalExpense;
    final int totalBookings = _toInt(stats['total_bookings']);
    final int todayEvents = _toInt(stats['today_events']);
    final int upcomingEvents = _toInt(stats['upcoming_events']);
    final num totalDue = _toNum(stats['total_due']);
    final num totalLoanDue = _toNum(stats['total_loan_due']);
    final num loanTotalRepaid = _toNum(stats['loan_total_repaid']);
    final int unsettledLoansCount = _toInt(stats['unsettled_loans_count']);
    final int collectionRate = _toInt(
      stats['collection_rate'],
    ).clamp(0, 100).toInt();

    final userName = auth.user?['name']?.toString() ?? 'ম্যানেজার';

    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      body: SafeArea(
        child: (dashboard.isLoading && dashboard.data == null)
            ? const Center(child: CircularProgressIndicator())
            : RefreshIndicator(
                color: AppTheme.primaryEmerald,
                backgroundColor: Colors.white,
                onRefresh: () =>
                    dashboard.fetchDashboard(isPullToRefresh: true),
                child: SingleChildScrollView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(16, 12, 16, 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildHeader(userName),
                      const SizedBox(height: 14),
                      _buildFinancialHeroCard(
                        netBalance,
                        totalIncome,
                        totalExpense,
                        collectionRate,
                      ),
                      const SizedBox(height: 18),
                      _buildSectionHeader(
                        'পরিচালন সূচক',
                        trailing: 'এই মাসের হিসাব',
                      ),
                      const SizedBox(height: 10),
                      _buildKpiGrid(
                        todayEvents: todayEvents,
                        upcomingEvents: upcomingEvents,
                        totalBookings: totalBookings,
                        totalDue: totalDue,
                        totalLoanDue: totalLoanDue,
                        loanTotalRepaid: loanTotalRepaid,
                        unsettledLoansCount: unsettledLoansCount,
                      ),
                      const SizedBox(height: 18),
                      _buildSectionHeader('দ্রুত কাজ'),
                      const SizedBox(height: 10),
                      _buildQuickActions(),
                      const SizedBox(height: 18),
                      _buildUpcomingHeader(upcomingEvents),
                      const SizedBox(height: 10),
                      bookings.isEmpty
                          ? _buildEmptyState()
                          : ListView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: bookings.length > 4
                                  ? 4
                                  : bookings.length,
                              itemBuilder: (context, index) {
                                return _buildUpcomingBookingCard(
                                  bookings[index],
                                );
                              },
                            ),
                      const SizedBox(height: 10),
                    ],
                  ),
                ),
              ),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────────
  // Header
  // ─────────────────────────────────────────────────────────────────────────

  Widget _buildHeader(String userName) {
    final trimmedName = userName.trim();
    final initial = trimmedName.isNotEmpty ? trimmedName[0].toUpperCase() : 'M';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.withAlpha(20)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [AppTheme.primaryTealLight, AppTheme.primaryTealDark],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(15),
              boxShadow: [
                BoxShadow(
                  color: AppTheme.primaryTeal.withAlpha(35),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Center(
              child: Text(
                initial,
                style: GoogleFonts.manrope(
                  color: Colors.white,
                  fontWeight: FontWeight.w900,
                  fontSize: 19,
                ),
              ),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text(
                      'শুভ দিন, ',
                      style: TextStyle(
                        fontSize: 11,
                        color: Colors.grey.shade600,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    Flexible(
                      child: Text(
                        trimmedName.isEmpty ? 'ম্যানেজার' : trimmedName,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.manrope(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.textNavy,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 3),
                Row(
                  children: [
                    const Icon(
                      Icons.location_on_rounded,
                      size: 12,
                      color: AppTheme.primaryEmerald,
                    ),
                    const SizedBox(width: 3),
                    Flexible(
                      child: Text(
                        'মমতা কমিউনিটি সেন্টার',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 10.5,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.primaryEmerald,
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 7),
            decoration: BoxDecoration(
              color: AppTheme.primaryEmerald.withAlpha(12),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppTheme.primaryEmerald.withAlpha(25)),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(
                  Icons.calendar_today_rounded,
                  size: 13,
                  color: AppTheme.primaryEmerald,
                ),
                const SizedBox(height: 3),
                Text(
                  _getBengaliToday(),
                  style: const TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.w800,
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

  // ─────────────────────────────────────────────────────────────────────────
  // Financial hero
  // ─────────────────────────────────────────────────────────────────────────

  Widget _buildFinancialHeroCard(
    num netBalance,
    num totalIncome,
    num totalExpense,
    int collectionRate,
  ) {
    final isPositive = netBalance >= 0;

    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primaryTeal.withAlpha(45),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(22),
        child: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [AppTheme.primaryTealDark, AppTheme.primaryTealLight],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: Stack(
            clipBehavior: Clip.hardEdge,
            children: [
              Positioned(
                right: -35,
                top: -40,
                child: Container(
                  width: 150,
                  height: 150,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white.withAlpha(9),
                  ),
                ),
              ),
              Positioned(
                left: -45,
                bottom: -70,
                child: Container(
                  width: 170,
                  height: 170,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppTheme.accentGold.withAlpha(12),
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(18),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(6),
                          decoration: BoxDecoration(
                            color: Colors.white.withAlpha(28),
                            borderRadius: BorderRadius.circular(9),
                          ),
                          child: const Icon(
                            Icons.account_balance_wallet_rounded,
                            color: Colors.white,
                            size: 15,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'এই মাসের নিট ব্যালেন্স',
                            style: GoogleFonts.manrope(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: Colors.white.withAlpha(220),
                            ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color:
                                (isPositive
                                        ? const Color(0xFF69F0AE)
                                        : const Color(0xFFFF8A80))
                                    .withAlpha(30),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: Colors.white.withAlpha(25),
                            ),
                          ),
                          child: Text(
                            isPositive ? 'উদ্বৃত্ত' : 'ঘাটতি',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 9,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Text(
                      '৳ ${currencyFormat.format(netBalance)}',
                      style: GoogleFonts.manrope(
                        fontSize: 29,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                        letterSpacing: -0.8,
                      ),
                    ),
                    const SizedBox(height: 14),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 10,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.black.withAlpha(28),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: Colors.white.withAlpha(15)),
                      ),
                      child: Row(
                        children: [
                          Expanded(
                            child: _buildHeroMoney(
                              'এই মাসের আয়',
                              totalIncome,
                              Icons.arrow_downward_rounded,
                              const Color(0xFF69F0AE),
                            ),
                          ),
                          Container(
                            width: 1,
                            height: 32,
                            color: Colors.white.withAlpha(30),
                          ),
                          Expanded(
                            child: _buildHeroMoney(
                              'এই মাসের ব্যয়',
                              totalExpense,
                              Icons.arrow_upward_rounded,
                              const Color(0xFFFF8A80),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 13),
                    Row(
                      children: [
                        Text(
                          'বুকিং আদায়ের হার',
                          style: TextStyle(
                            fontSize: 9.5,
                            color: Colors.white.withAlpha(190),
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const Spacer(),
                        Text(
                          '$collectionRate%',
                          style: GoogleFonts.manrope(
                            fontSize: 11,
                            fontWeight: FontWeight.w900,
                            color: AppTheme.accentGold,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(4),
                      child: LinearProgressIndicator(
                        value: collectionRate / 100,
                        minHeight: 6,
                        backgroundColor: Colors.white.withAlpha(25),
                        valueColor: const AlwaysStoppedAnimation<Color>(
                          AppTheme.accentGold,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeroMoney(String label, num amount, IconData icon, Color color) {
    return Row(
      children: [
        Icon(icon, color: color, size: 16),
        const SizedBox(width: 6),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  color: Colors.white.withAlpha(175),
                  fontSize: 9,
                ),
              ),
              const SizedBox(height: 1),
              Text(
                '৳ ${currencyFormat.format(amount)}',
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.manrope(
                  color: Colors.white,
                  fontSize: 13,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // ─────────────────────────────────────────────────────────────────────────
  // KPI grid
  // ─────────────────────────────────────────────────────────────────────────

  Widget _buildSectionHeader(String title, {String? trailing}) {
    return Row(
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
          title,
          style: GoogleFonts.manrope(
            fontSize: 15,
            fontWeight: FontWeight.w800,
            color: AppTheme.textNavy,
          ),
        ),
        const Spacer(),
        if (trailing != null)
          Text(
            trailing,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w600,
              color: Colors.grey.shade600,
            ),
          ),
      ],
    );
  }

  Widget _buildKpiGrid({
    required int todayEvents,
    required int upcomingEvents,
    required int totalBookings,
    required num totalDue,
    required num totalLoanDue,
    required num loanTotalRepaid,
    required int unsettledLoansCount,
  }) {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _buildKpiCard(
                title: 'আজকের ইভেন্ট',
                value: '$todayEvents টি',
                subtitle: 'আজকের তারিখ',
                icon: Icons.today_rounded,
                accentColor: const Color(0xFF0277BD),
                onTap: () => _openCalendar(),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _buildKpiCard(
                title: 'আসন্ন ইভেন্ট',
                value: '$upcomingEvents টি',
                subtitle: 'আজসহ সামনের সব',
                icon: Icons.event_available_rounded,
                accentColor: const Color(0xFF00897B),
                onTap: () => _openCalendar(),
              ),
            ),
          ],
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: _buildKpiCard(
                title: 'এই মাসের বুকিং',
                value: '$totalBookings টি',
                subtitle: 'চলতি মাসে তৈরি',
                icon: Icons.calendar_month_rounded,
                accentColor: const Color(0xFF5E35B1),
                onTap: () => _openAllBookings(),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _buildKpiCard(
                title: 'বকেয়া আদায়',
                value: '৳ ${_compactNumber(totalDue)}',
                subtitle: 'গ্রাহকের কাছে বাকি',
                icon: Icons.receipt_long_rounded,
                accentColor: const Color(0xFFD84315),
                onTap: () => _openAllBookings(),
              ),
            ),
          ],
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: _buildKpiCard(
                title: 'হাওলাত / ঋণ বাকি',
                value: '৳ ${_compactNumber(totalLoanDue)}',
                subtitle: unsettledLoansCount > 0
                    ? '$unsettledLoansCount টি ঋণ অপরিশোধিত'
                    : 'সকল ঋণ পরিশোধিত',
                icon: Icons.account_balance_wallet_rounded,
                accentColor: const Color(0xFFC62828),
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const LoanListScreen()),
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _buildKpiCard(
                title: 'পরিশোধিত ঋণ',
                value: '৳ ${_compactNumber(loanTotalRepaid)}',
                subtitle: 'ধার পরিশোধ হিসাব',
                icon: Icons.assignment_turned_in_rounded,
                accentColor: const Color(0xFF2E7D32),
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const LoanListScreen()),
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildKpiCard({
    required String title,
    required String value,
    required String subtitle,
    required IconData icon,
    required Color accentColor,
    required VoidCallback onTap,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.grey.withAlpha(18)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(4),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(18),
          child: Padding(
            padding: const EdgeInsets.all(13),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        color: accentColor.withAlpha(18),
                        borderRadius: BorderRadius.circular(11),
                      ),
                      child: Icon(icon, color: accentColor, size: 19),
                    ),
                    const Spacer(),
                    Icon(
                      Icons.arrow_outward_rounded,
                      size: 13,
                      color: Colors.grey.shade400,
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Text(
                  value,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.manrope(
                    fontSize: 19,
                    fontWeight: FontWeight.w900,
                    color: AppTheme.textNavy,
                    letterSpacing: -0.4,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  title,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 10.5,
                    fontWeight: FontWeight.w700,
                    color: Colors.black54,
                  ),
                ),
                const SizedBox(height: 1),
                Text(
                  subtitle,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.w500,
                    color: Colors.grey.shade500,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────────
  // Quick actions
  // ─────────────────────────────────────────────────────────────────────────

  Widget _buildQuickActions() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.grey.withAlpha(18)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(4),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: _buildActionButton(
              label: 'নতুন বুকিং',
              icon: Icons.add_circle_outline_rounded,
              color: AppTheme.primaryEmerald,
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const BookingFormScreen()),
              ),
            ),
          ),
          Expanded(
            child: _buildActionButton(
              label: 'আয় যুক্ত',
              icon: Icons.trending_up_rounded,
              color: const Color(0xFF2E7D32),
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) =>
                      const TransactionFormScreen(initialType: 'income'),
                ),
              ),
            ),
          ),
          Expanded(
            child: _buildActionButton(
              label: 'ব্যয় যুক্ত',
              icon: Icons.trending_down_rounded,
              color: const Color(0xFFC62828),
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) =>
                      const TransactionFormScreen(initialType: 'expense'),
                ),
              ),
            ),
          ),
          Expanded(
            child: _buildActionButton(
              label: 'হাওলাত/ঋণ',
              icon: Icons.handshake_outlined,
              color: const Color(0xFFE65100),
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const LoanListScreen()),
              ),
            ),
          ),
          Expanded(
            child: _buildActionButton(
              label: 'ক্যালেন্ডার',
              icon: Icons.event_note_rounded,
              color: const Color(0xFF0277BD),
              onTap: _openCalendar,
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
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 2, vertical: 4),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: color.withAlpha(18),
                  borderRadius: BorderRadius.circular(13),
                ),
                child: Icon(icon, color: color, size: 21),
              ),
              const SizedBox(height: 5),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  fontSize: 9.5,
                  fontWeight: FontWeight.w700,
                  color: Colors.grey.shade800,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────────
  // Upcoming events
  // ─────────────────────────────────────────────────────────────────────────

  Widget _buildUpcomingHeader(int upcomingEvents) {
    return Row(
      children: [
        Container(
          width: 4,
          height: 18,
          decoration: BoxDecoration(
            color: AppTheme.primaryEmerald,
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Row(
            children: [
              Flexible(
                child: Text(
                  'আসন্ন বুকিং ও ইভেন্টসমূহ',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.manrope(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                    color: AppTheme.textNavy,
                  ),
                ),
              ),
              if (upcomingEvents > 0) ...[
                const SizedBox(width: 6),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 6,
                    vertical: 2,
                  ),
                  decoration: BoxDecoration(
                    color: AppTheme.accentGoldLight,
                    borderRadius: BorderRadius.circular(7),
                  ),
                  child: Text(
                    '$upcomingEvents টি',
                    style: const TextStyle(
                      fontSize: 8.5,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.accentGoldDark,
                    ),
                  ),
                ),
              ],
            ],
          ),
        ),
        TextButton(
          style: TextButton.styleFrom(
            foregroundColor: AppTheme.primaryEmerald,
            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
            minimumSize: Size.zero,
            tapTargetSize: MaterialTapTargetSize.shrinkWrap,
          ),
          onPressed: _openAllBookings,
          child: Row(
            children: [
              Text(
                'সব দেখুন',
                style: GoogleFonts.manrope(
                  color: AppTheme.primaryEmerald,
                  fontWeight: FontWeight.w700,
                  fontSize: 11,
                ),
              ),
              const SizedBox(width: 1),
              const Icon(Icons.chevron_right_rounded, size: 16),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildUpcomingBookingCard(dynamic data) {
    final booking = Booking.fromJson(Map<String, dynamic>.from(data));
    final today = _todayDate;

    var displayItem = booking.items.isNotEmpty ? booking.items.first : null;
    for (final item in booking.items) {
      final parsed = DateTime.tryParse(item.eventDate);
      if (parsed == null) continue;
      final itemDate = DateTime(parsed.year, parsed.month, parsed.day);
      if (!itemDate.isBefore(today)) {
        displayItem = item;
        break;
      }
    }

    final eventType = (displayItem != null && displayItem.eventType.isNotEmpty)
        ? displayItem.eventType
        : (data['event_type']?.toString() ?? 'ইভেন্ট');
    final slot = displayItem?.slot ?? '';
    final slotLabel = slot.isEmpty
        ? ''
        : (slot == 'night' || slot == 'evening' ? 'রাত' : 'দিন');
    final dateStr = displayItem?.eventDate ?? '';

    String day = '';
    String month = '';
    String countdown = '';
    final eventDate = DateTime.tryParse(dateStr);
    if (eventDate != null) {
      day = eventDate.day.toString();
      month = _bnMonthsShort[eventDate.month - 1];
      final itemDate = DateTime(eventDate.year, eventDate.month, eventDate.day);
      final days = itemDate.difference(today).inDays;
      if (days == 0) {
        countdown = 'আজ';
      } else if (days == 1) {
        countdown = 'আগামীকাল';
      } else if (days > 1) {
        countdown = '$days দিন বাকি';
      }
    }

    final isPaid = booking.dueAmount <= 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 9),
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
          MaterialPageRoute(
            builder: (_) => BookingDetailsScreen(booking: booking),
          ),
        ),
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 11),
          child: Row(
            children: [
              Container(
                width: 46,
                padding: const EdgeInsets.symmetric(vertical: 7),
                decoration: BoxDecoration(
                  color: AppTheme.primaryEmerald.withAlpha(15),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: AppTheme.primaryEmerald.withAlpha(30),
                  ),
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
                        month,
                        style: const TextStyle(
                          fontSize: 8.5,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.primaryEmerald,
                        ),
                      ),
                  ],
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
                        Flexible(
                          child: Text(
                            booking.customerName,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: GoogleFonts.manrope(
                              fontWeight: FontWeight.w800,
                              fontSize: 13,
                              color: AppTheme.textNavy,
                            ),
                          ),
                        ),
                        if (countdown.isNotEmpty) ...[
                          const SizedBox(width: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 5,
                              vertical: 1.5,
                            ),
                            decoration: BoxDecoration(
                              color: AppTheme.accentGoldLight,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              countdown,
                              style: const TextStyle(
                                fontSize: 8,
                                fontWeight: FontWeight.w800,
                                color: AppTheme.accentGoldDark,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 3),
                    Row(
                      children: [
                        Flexible(
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 6,
                              vertical: 1,
                            ),
                            decoration: BoxDecoration(
                              color: AppTheme.primaryEmerald.withAlpha(15),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              eventType,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 9,
                                fontWeight: FontWeight.bold,
                                color: AppTheme.primaryEmerald,
                              ),
                            ),
                          ),
                        ),
                        if (slotLabel.isNotEmpty) ...[
                          const SizedBox(width: 6),
                          Text(
                            slotLabel,
                            style: TextStyle(
                              fontSize: 9,
                              fontWeight: FontWeight.w600,
                              color: Colors.grey.shade600,
                            ),
                          ),
                        ],
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            booking.customerPhone,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              fontSize: 9.5,
                              color: Colors.grey.shade500,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '৳ ${currencyFormat.format(booking.totalAmount)}',
                    style: GoogleFonts.manrope(
                      fontWeight: FontWeight.w900,
                      color: AppTheme.textNavy,
                      fontSize: 12.5,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 6,
                      vertical: 2,
                    ),
                    decoration: BoxDecoration(
                      color: isPaid
                          ? Colors.green.withAlpha(20)
                          : Colors.orange.withAlpha(20),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      isPaid
                          ? 'পরিশোধিত'
                          : 'বাকি ৳ ${_compactNumber(booking.dueAmount)}',
                      style: TextStyle(
                        color: isPaid
                            ? const Color(0xFF2E7D32)
                            : const Color(0xFFE65100),
                        fontSize: 8,
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
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.grey.withAlpha(18)),
      ),
      child: Column(
        children: [
          Container(
            width: 52,
            height: 52,
            decoration: BoxDecoration(
              color: AppTheme.primaryEmerald.withAlpha(10),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.event_available_rounded,
              size: 26,
              color: AppTheme.primaryEmerald,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            'কোনো আসন্ন ইভেন্ট পাওয়া যায়নি।',
            style: GoogleFonts.manrope(
              fontSize: 12.5,
              fontWeight: FontWeight.w700,
              color: AppTheme.textNavy,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'নতুন বুকিং যোগ করলে এখানে দেখা যাবে।',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 10.5, color: Colors.grey.shade600),
          ),
          const SizedBox(height: 10),
          TextButton.icon(
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const BookingFormScreen()),
            ),
            icon: const Icon(Icons.add_rounded, size: 16),
            label: const Text(
              'নতুন বুকিং',
              style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700),
            ),
            style: TextButton.styleFrom(
              foregroundColor: AppTheme.primaryEmerald,
              minimumSize: Size.zero,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
            ),
          ),
        ],
      ),
    );
  }

  void _openAllBookings() {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const AllBookingsScreen()),
    );
  }

  void _openCalendar() {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const CalendarScreen()),
    );
  }
}
