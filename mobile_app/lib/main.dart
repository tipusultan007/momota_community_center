import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'providers/auth_provider.dart';
import 'providers/booking_provider.dart';
import 'providers/customer_provider.dart';
import 'providers/accounting_provider.dart';
import 'providers/staff_provider.dart';
import 'providers/hall_provider.dart';
import 'providers/dashboard_provider.dart';
import 'providers/user_provider.dart';
import 'providers/report_provider.dart';
import 'theme/app_theme.dart';
import 'screens/dashboard_screen.dart';
import 'screens/main_navigation_screen.dart';
import 'screens/login_screen.dart';
import 'screens/splash_screen.dart';
import 'providers/vendor_provider.dart';
import 'providers/asset_provider.dart';

import 'api/api_service.dart';
import 'api/database_service.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize Database
  await DatabaseService.instance.database;

  // Listen for connectivity changes to trigger sync
  Connectivity().onConnectivityChanged.listen((List<ConnectivityResult> results) {
    final online = results.any((result) => result != ConnectivityResult.none);
    ApiService.instance.isOnline.value = online;
    if (online) {
      ApiService.instance.processSyncQueue();
    }
  });

  // Check initial connectivity at app launch
  Connectivity().checkConnectivity().then((List<ConnectivityResult> results) {
    final online = results.any((result) => result != ConnectivityResult.none);
    ApiService.instance.isOnline.value = online;
    if (online) {
      ApiService.instance.processSyncQueue();
    }
  });

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()..checkAuth()),
        ChangeNotifierProvider(create: (_) => HallProvider()),
        ChangeNotifierProvider(create: (_) => BookingProvider()),
        ChangeNotifierProvider(create: (_) => CustomerProvider()),
        ChangeNotifierProvider(create: (_) => AccountingProvider()),
        ChangeNotifierProvider(create: (_) => StaffProvider()),
        ChangeNotifierProvider(create: (_) => DashboardProvider()),
        ChangeNotifierProvider(create: (_) => UserProvider()),
        ChangeNotifierProvider(create: (_) => ReportProvider()),
        ChangeNotifierProvider(create: (_) => VendorProvider()),
        ChangeNotifierProvider(create: (_) => AssetProvider()),
      ],
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Momota Community Center',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const SplashScreen(),
    );
  }
}
