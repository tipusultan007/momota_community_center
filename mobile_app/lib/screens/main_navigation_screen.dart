import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dashboard_screen.dart';
import 'bookings/booking_list_screen.dart';
import 'bookings/all_bookings_screen.dart';
import 'calendar/calendar_screen.dart';
import 'accounting/accounting_ledger_screen.dart';
import 'customers/customer_list_screen.dart';
import 'staff/staff_list_screen.dart';
import '../providers/hall_provider.dart';
import '../providers/booking_provider.dart';
import '../providers/accounting_provider.dart';
import '../providers/customer_provider.dart';
import '../providers/dashboard_provider.dart';
import '../providers/staff_provider.dart';
import 'settings/settings_screen.dart';
import '../providers/auth_provider.dart';
import 'staff/salary_list_screen.dart';
import 'users/user_list_screen.dart';
import 'reports/report_selection_screen.dart';
import 'accounting/category_list_screen.dart';
import 'settings/business_settings_screen.dart';
import 'settings/change_password_screen.dart';
import 'vendors/vendor_list_screen.dart';
import 'staff/staff_form_screen.dart';
import 'assets/asset_list_screen.dart';
import '../theme/app_theme.dart';
import 'package:google_fonts/google_fonts.dart';
import '../api/api_service.dart';

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> with WidgetsBindingObserver {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const DashboardScreen(),
    const BookingListScreen(),
    const AccountingLedgerScreen(),
    const CustomerListScreen(),
    const StaffListScreen(),
  ];

  String _getPageTitle() {
    switch (_selectedIndex) {
      case 0:
        return 'ড্যাশবোর্ড';
      case 1:
        return 'বুকিং ব্যবস্থাপনা';
      case 2:
        return 'হিসাব ও লেনদেন';
      case 3:
        return 'গ্রাহক তালিকা (CRM)';
      case 4:
        return 'স্টাফ ম্যানেজমেন্ট';
      default:
        return 'মমতা কমিউনিটি সেন্টার';
    }
  }

  List<Widget> _buildAppBarActions() {
    return [
      if (_selectedIndex == 4)
        IconButton(
          tooltip: 'নতুন স্টাফ যোগ করুন',
          icon: const Icon(Icons.person_add_alt_1_rounded, color: AppTheme.primaryEmerald),
          onPressed: () => Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const StaffFormScreen()),
          ),
        ),
      if (_selectedIndex == 2)
        IconButton(
          tooltip: 'ক্যাটেগরি তালিকা',
          icon: const Icon(Icons.category_outlined, color: AppTheme.textNavy),
          onPressed: () => Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const CategoryListScreen()),
          ),
        ),
      IconButton(
        tooltip: 'রিফ্রেশ করুন',
        icon: const Icon(Icons.refresh_rounded, color: AppTheme.textNavy),
        onPressed: _refreshAllData,
      ),
      const SizedBox(width: 4),
    ];
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    ApiService.instance.onSyncComplete = () {
      if (mounted) {
        _refreshAllData();
      }
    };
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _refreshAllData();
      final hallProvider = context.read<HallProvider>();
      hallProvider.addListener(_onHallChanged);
      hallProvider.fetchHalls();
    });
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed && mounted) {
      _refreshAllData();
    }
  }

  void _onHallChanged() {
    if (mounted) {
      final hallProvider = context.read<HallProvider>();
      if (!hallProvider.isLoading) {
        _refreshAllData();
      }
    }
  }

  void _refreshAllData() {
    context.read<DashboardProvider>().fetchDashboard();
    context.read<BookingProvider>().fetchBookings();
    context.read<AccountingProvider>().fetchTransactions();
    context.read<CustomerProvider>().fetchCustomers();
    context.read<StaffProvider>().fetchStaff();
  }

  Widget _buildOfflineStatusBar() {
    return AnimatedBuilder(
      animation: Listenable.merge([
        ApiService.instance.isOnline,
        ApiService.instance.isSyncing,
        ApiService.instance.pendingSyncCount,
      ]),
      builder: (context, _) {
        final isOnline = ApiService.instance.isOnline.value;
        final isSyncing = ApiService.instance.isSyncing.value;
        final pendingCount = ApiService.instance.pendingSyncCount.value;

        if (isOnline && pendingCount == 0 && !isSyncing) {
          return const SizedBox.shrink();
        }

        if (isSyncing) {
          return Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 16),
            color: Colors.blue.shade700,
            child: const Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                SizedBox(
                  width: 14,
                  height: 14,
                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                ),
                SizedBox(width: 8),
                Text(
                  'অনলাইনে ডাটা সিঙ্ক হচ্ছে...',
                  style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          );
        }

        return Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 16),
          color: Colors.amber.shade800,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.wifi_off, color: Colors.white, size: 14),
              const SizedBox(width: 8),
              Text(
                pendingCount > 0
                    ? 'অফলাইন মোড • $pendingCount টি পরিবর্তন সিঙ্ক হবে'
                    : 'অফলাইন মোড সক্রিয় • সংরক্ষিত তথ্য প্রদর্শিত হচ্ছে',
                style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      drawer: _buildSidebar(context, user),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        leading: Builder(
          builder: (context) => IconButton(
            icon: const Icon(Icons.menu_rounded, color: AppTheme.textNavy),
            onPressed: () => Scaffold.of(context).openDrawer(),
          ),
        ),
        title: Text(
          _getPageTitle(),
          style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 18, color: AppTheme.textNavy),
        ),
        centerTitle: false,
        actions: _buildAppBarActions(),
      ),
      body: Column(
        children: [
          _buildOfflineStatusBar(),
          Expanded(
            child: IndexedStack(
              index: _selectedIndex,
              children: _screens,
            ),
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.fromLTRB(14, 0, 14, 12),
        decoration: const BoxDecoration(
          color: Colors.transparent,
        ),
        child: SafeArea(
          top: false,
          child: Container(
            height: 66,
            padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(22),
              border: Border.all(color: Colors.black.withAlpha(12), width: 1),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withAlpha(18),
                  blurRadius: 20,
                  offset: const Offset(0, 6),
                  spreadRadius: 1,
                ),
                BoxShadow(
                  color: AppTheme.primaryTeal.withAlpha(20),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildNavItem(0, Icons.dashboard_outlined, Icons.dashboard_rounded, 'ড্যাশবোর্ড'),
                _buildNavItem(1, Icons.calendar_month_outlined, Icons.calendar_month_rounded, 'বুকিং'),
                _buildNavItem(2, Icons.account_balance_wallet_outlined, Icons.account_balance_wallet_rounded, 'লেজার'),
                _buildNavItem(3, Icons.people_outline_rounded, Icons.people_rounded, 'সিআরএম'),
                _buildNavItem(4, Icons.badge_outlined, Icons.badge_rounded, 'স্টাফ'),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, IconData activeIcon, String label) {
    final isSelected = _selectedIndex == index;
    return Expanded(
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () {
            if (_selectedIndex != index) {
              setState(() => _selectedIndex = index);
            }
            if (index == 0) {
              context.read<DashboardProvider>().fetchDashboard();
            } else if (index == 1) {
              context.read<BookingProvider>().fetchBookings();
            } else if (index == 2) {
              context.read<AccountingProvider>().fetchTransactions();
              context.read<AccountingProvider>().fetchCategories();
            } else if (index == 3) {
              context.read<CustomerProvider>().fetchCustomers();
            } else if (index == 4) {
              context.read<StaffProvider>().fetchStaff();
            }
          },
          borderRadius: BorderRadius.circular(18),
          splashColor: AppTheme.primaryEmerald.withAlpha(25),
          highlightColor: Colors.transparent,
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 220),
            curve: Curves.easeInOut,
            padding: const EdgeInsets.symmetric(vertical: 4),
            decoration: BoxDecoration(
              color: isSelected ? AppTheme.primaryEmerald.withAlpha(20) : Colors.transparent,
              borderRadius: BorderRadius.circular(18),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                AnimatedScale(
                  scale: isSelected ? 1.08 : 1.0,
                  duration: const Duration(milliseconds: 200),
                  child: Icon(
                    isSelected ? activeIcon : icon,
                    color: isSelected ? AppTheme.primaryEmerald : Colors.grey.shade500,
                    size: 22,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: 9.5,
                    fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                    color: isSelected ? AppTheme.primaryEmerald : Colors.grey.shade500,
                    letterSpacing: -0.2,
                  ),
                ),
                const SizedBox(height: 2),
                AnimatedContainer(
                  duration: const Duration(milliseconds: 220),
                  height: 2.5,
                  width: isSelected ? 12 : 0,
                  decoration: BoxDecoration(
                    color: AppTheme.primaryEmerald,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSidebar(BuildContext context, Map<String, dynamic>? user) {
    return Drawer(
      backgroundColor: Colors.white,
      child: Column(
        children: [
          _buildDrawerHeader(user),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.symmetric(vertical: 8),
              children: [
                _drawerItem(Icons.dashboard_rounded, 'ড্যাশবোর্ড', 0),
                
                _buildDrawerSectionTitle('বুকিং ব্যবস্থাপনা'),
                _drawerItem(
                  Icons.receipt_long_rounded, 
                  'সকল বুকিং তালিকা', 
                  -1, 
                  screen: const AllBookingsScreen(),
                  isHighlighted: true,
                ),
                _drawerItem(Icons.calendar_view_day_rounded, 'দৈনিক বুকিং', 1),
                _drawerItem(Icons.calendar_month_rounded, 'মাসিক ক্যালেন্ডার', -1, screen: const CalendarScreen()),
                
                _buildDrawerSectionTitle('হিসাব ও লেনদেন'),
                _drawerExpansionTile(
                  Icons.account_balance_wallet_rounded, 
                  'হিসাব রক্ষণ',
                  [
                    _drawerSubItem('লেনদেন বিবরণী', 2),
                    _drawerSubItem('আয়/ব্যয় ক্যাটেগরি', -1, screen: const CategoryListScreen()),
                  ]
                ),

                _buildDrawerSectionTitle('ব্যবস্থাপনা'),
                _drawerItem(Icons.groups_rounded, 'সিআরএম (গ্রাহক)', 3),
                _drawerItem(Icons.badge_rounded, 'স্টাফ ম্যানেজমেন্ট', 4),
                _drawerItem(Icons.payments_rounded, 'বেতন (স্যালারি) ম্যানেজমেন্ট', -1, screen: const SalaryListScreen()),
                _drawerItem(Icons.handshake_rounded, 'ভেন্ডর ম্যানেজমেন্ট', -1, screen: const VendorListScreen()),
                _drawerItem(Icons.inventory_2_rounded, 'মালামাল (ইনভেন্টরি)', -1, screen: const AssetListScreen()),
                _drawerItem(Icons.manage_accounts_rounded, 'ব্যবহারকারী (Users)', -1, screen: const UserListScreen()),
                _drawerItem(Icons.analytics_rounded, 'রিপোর্ট সমূহ', -1, screen: const ReportSelectionScreen()),
                
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                  child: Divider(height: 1, thickness: 1, color: Color(0xFFEFEFEF)),
                ),
                
                _drawerExpansionTile(
                  Icons.settings_rounded, 
                  'সেটিংস',
                  [
                    _drawerSubItem('প্রোফাইল সেটিংস', -1, screen: const SettingsScreen()),
                    _drawerSubItem('ব্যবসা সেটিংস', -1, screen: const BusinessSettingsScreen()),
                    _drawerSubItem('পাসওয়ার্ড পরিবর্তন', -1, screen: const ChangePasswordScreen()),
                  ]
                ),

                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                  child: ListTile(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    tileColor: Colors.red.shade50.withAlpha(120),
                    leading: const Icon(Icons.logout_rounded, color: Colors.redAccent, size: 22),
                    title: const Text(
                      'লগআউট', 
                      style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold, fontSize: 13),
                    ),
                    onTap: () async {
                      final auth = context.read<AuthProvider>();
                      await auth.logout();
                    },
                  ),
                ),
              ],
            ),
          ),
          _buildDrawerFooter(),
        ],
      ),
    );
  }

  Widget _buildDrawerSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 14, 20, 4),
      child: Text(
        title.toUpperCase(),
        style: const TextStyle(
          color: Color(0xFF006D5B),
          fontSize: 10,
          fontWeight: FontWeight.w800,
          letterSpacing: 1.2,
        ),
      ),
    );
  }

  Widget _buildDrawerHeader(Map<String, dynamic>? user) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.only(top: 52, left: 20, bottom: 20, right: 20),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF003028), Color(0xFF004D40)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.only(bottomRight: Radius.circular(32)),
        boxShadow: [
          BoxShadow(
            color: Color(0x33003028),
            blurRadius: 14,
            offset: Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(3),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: const Color(0xFFF5A623), width: 2.2),
                  color: Colors.white,
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFFF5A623).withAlpha(80),
                      blurRadius: 10,
                      spreadRadius: 1,
                    ),
                  ],
                ),
                child: ClipOval(
                  child: Image.asset(
                    'assets/images/logo.png',
                    height: 44,
                    width: 44,
                    fit: BoxFit.contain,
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'মমতা কমিউনিটি সেন্টার',
                      style: GoogleFonts.hindSiliguri(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                        height: 1.1,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      'MOMOTA CONVENTION',
                      style: GoogleFonts.manrope(
                        color: const Color(0xFFF5A623),
                        fontWeight: FontWeight.w800,
                        fontSize: 9,
                        letterSpacing: 1.2,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white.withAlpha(22),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.white.withAlpha(30)),
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 15,
                  backgroundColor: const Color(0xFFF5A623),
                  child: Text(
                    (user?['name'] != null && user!['name'].toString().isNotEmpty)
                        ? user['name'].toString().substring(0, 1).toUpperCase()
                        : 'A',
                    style: const TextStyle(
                      color: Color(0xFF003028),
                      fontWeight: FontWeight.w900,
                      fontSize: 13,
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        user?['name'] ?? 'অ্যাডমিনিস্ট্রেটর',
                        style: GoogleFonts.manrope(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          fontSize: 13,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 2),
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1.5),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF5A623),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              user?['role']?.toString().toUpperCase() ?? 'ADMINISTRATOR',
                              style: const TextStyle(
                                color: Color(0xFF003028),
                                fontWeight: FontWeight.w900,
                                fontSize: 8.5,
                                letterSpacing: 0.6,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _drawerItem(
    IconData icon, 
    String label, 
    int index, 
    {Widget? screen, bool isHighlighted = false}
  ) {
    final isSelected = _selectedIndex == index && index != -1;
    
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
      child: Material(
        color: isSelected
            ? const Color(0xFF004D40).withAlpha(20)
            : (isHighlighted ? const Color(0xFFF5A623).withAlpha(20) : Colors.transparent),
        borderRadius: BorderRadius.circular(12),
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () {
            Navigator.pop(context); // Close drawer
            if (index != -1) {
              setState(() => _selectedIndex = index);
            } else if (screen != null) {
              Navigator.push(context, MaterialPageRoute(builder: (_) => screen));
            }
          },
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
            decoration: BoxDecoration(
              border: isSelected
                  ? const Border(left: BorderSide(color: Color(0xFFF5A623), width: 3.5))
                  : (isHighlighted ? Border(left: BorderSide(color: const Color(0xFF004D40), width: 3.5)) : null),
            ),
            child: Row(
              children: [
                Icon(
                  icon,
                  color: isSelected
                      ? const Color(0xFF004D40)
                      : (isHighlighted ? const Color(0xFF004D40) : const Color(0xFF5A7175)),
                  size: 20,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    label,
                    style: TextStyle(
                      color: isSelected
                          ? const Color(0xFF004D40)
                          : (isHighlighted ? const Color(0xFF004D40) : const Color(0xFF1E2D2F)),
                      fontWeight: (isSelected || isHighlighted) ? FontWeight.w800 : FontWeight.w600,
                      fontSize: 13.5,
                    ),
                  ),
                ),
                if (isHighlighted)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF5A623),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: const Text(
                      'NEW',
                      style: TextStyle(
                        color: Color(0xFF003028),
                        fontWeight: FontWeight.w900,
                        fontSize: 8,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _drawerExpansionTile(IconData icon, String label, List<Widget> children) {
    return Theme(
      data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
      child: ExpansionTile(
        tilePadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 0),
        leading: Icon(icon, color: const Color(0xFF5A7175), size: 20),
        title: Text(
          label,
          style: const TextStyle(
            color: Color(0xFF1E2D2F),
            fontWeight: FontWeight.w600,
            fontSize: 13.5,
          ),
        ),
        iconColor: const Color(0xFF004D40),
        collapsedIconColor: const Color(0xFF5A7175),
        children: children,
      ),
    );
  }

  Widget _drawerSubItem(String label, int index, {Widget? screen}) {
    return Padding(
      padding: const EdgeInsets.only(left: 20),
      child: _drawerItem(Icons.arrow_right_rounded, label, index, screen: screen),
    );
  }

  Widget _buildDrawerFooter() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAF9),
        border: Border(top: BorderSide(color: Colors.grey.shade200)),
      ),
      child: Row(
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: const BoxDecoration(
              color: Color(0xFFF5A623),
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 8),
          const Expanded(
            child: Text(
              'মমতা কমিউনিটি সেন্টার • v1.0.0',
              style: TextStyle(
                color: Color(0xFF5A7175),
                fontSize: 11,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
