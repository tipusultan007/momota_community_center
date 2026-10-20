import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:momota_community_center/providers/auth_provider.dart';
import 'package:momota_community_center/providers/dashboard_provider.dart';
import 'package:momota_community_center/screens/dashboard_screen.dart';

class _FakeDashboardProvider extends ChangeNotifier
    implements DashboardProvider {
  _FakeDashboardProvider(this._data);

  final Map<String, dynamic>? _data;

  @override
  Map<String, dynamic>? get data => _data;

  @override
  bool get isLoading => false;

  @override
  Future<void> fetchDashboard({bool isPullToRefresh = false}) async {}
}

void main() {
  GoogleFonts.config.allowRuntimeFetching = false;

  testWidgets('dashboard shows KPIs and upcoming events without overflow', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(390, 844);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.resetPhysicalSize);
    addTearDown(tester.view.resetDevicePixelRatio);

    final eventDate = DateTime.now().add(const Duration(days: 3));
    final dateStr = eventDate.toIso8601String().split('T').first;

    final dashboardData = <String, dynamic>{
      'stats': <String, dynamic>{
        'total_income': 120000,
        'total_expense': 45000,
        'total_bookings': 8,
        'total_staff': 6,
        'active_staff': 5,
        'total_customers': 24,
        'today_events': 1,
        'upcoming_events': 4,
        'total_due': 85000,
        'month_booking_value': 400000,
        'collection_rate': 62,
      },
      'upcoming_bookings': [
        {
          'id': 7,
          'customer_id': 3,
          'customer': {'name': 'রহিম আহমেদ', 'phone': '01700000000'},
          'total_amount': 60000,
          'advance_amount': 10000,
          'status': 'confirmed',
          'items': [
            {
              'id': 9,
              'event_date': dateStr,
              'slot': 'day',
              'event_type': 'বিয়ে',
              'guest_count': 200,
              'table_count': 20,
              'server_count': 10,
              'server_rate': 500,
              'is_server_included': true,
              'base_price': 50000,
              'sub_total': 60000,
            },
          ],
          'incomes': [],
        },
      ],
    };

    await tester.pumpWidget(
      MultiProvider(
        providers: [
          ChangeNotifierProvider<AuthProvider>(create: (_) => AuthProvider()),
          ChangeNotifierProvider<DashboardProvider>(
            create: (_) => _FakeDashboardProvider(dashboardData),
          ),
        ],
        child: const MaterialApp(home: DashboardScreen()),
      ),
    );

    await tester.pump();
    await tester.pump(const Duration(milliseconds: 100));

    expect(tester.takeException(), isNull);
    expect(find.text('পরিচালন সূচক'), findsOneWidget);
    expect(find.text('আজকের ইভেন্ট'), findsOneWidget);
    expect(find.text('বকেয়া আদায়'), findsOneWidget);
    expect(find.text('মোট গ্রাহক'), findsOneWidget);
    expect(find.text('আসন্ন বুকিং ও ইভেন্টসমূহ'), findsOneWidget);
    expect(find.text('রহিম আহমেদ'), findsOneWidget);
  });
}
