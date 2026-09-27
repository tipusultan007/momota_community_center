import 'package:flutter/material.dart';
import '../api/api_service.dart';

class DashboardProvider with ChangeNotifier {
  Map<String, dynamic>? _data;
  bool _isLoading = false;

  Map<String, dynamic>? get data => _data;
  bool get isLoading => _isLoading;

  Future<void> fetchDashboard({bool isPullToRefresh = false}) async {
    // 1. Instant Cache check: load from cache in 0ms without blocking UI
    if (_data == null) {
      final cached = await ApiService.instance.getLocalCache('/dashboard');
      if (cached != null && cached['data'] != null) {
        _data = Map<String, dynamic>.from(cached['data']);
        _isLoading = false;
        notifyListeners();
      } else if (!isPullToRefresh) {
        _isLoading = true;
        notifyListeners();
      }
    }

    // 2. Fresh network sync
    try {
      final response = await ApiService.instance.get('/dashboard');
      if (response.statusCode == 200 && response.data?['data'] != null) {
        _data = Map<String, dynamic>.from(response.data['data']);
      }
    } catch (e) {
      debugPrint('Error fetching dashboard: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
