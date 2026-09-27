import 'package:flutter/material.dart';
import '../api/api_service.dart';
import '../models/customer.dart';

class CustomerProvider with ChangeNotifier {
  List<Customer> _customers = [];
  bool _isLoading = false;

  List<Customer> get customers => _customers;
  bool get isLoading => _isLoading;

  Future<void> fetchCustomers({bool isPullToRefresh = false}) async {
    // 1. Instant Cache check: load from cache in 0ms without blocking UI
    if (_customers.isEmpty) {
      final cached = await ApiService.instance.getLocalCache('/customers');
      if (cached != null && cached['data'] is List) {
        final List list = cached['data'];
        _customers = list.map((item) => Customer.fromJson(Map<String, dynamic>.from(item))).toList();
        _isLoading = false;
        notifyListeners();
      } else if (!isPullToRefresh) {
        _isLoading = true;
        notifyListeners();
      }
    }

    // 2. Fresh network sync
    try {
      final response = await ApiService.instance.get('/customers');
      if (response.statusCode == 200 && response.data?['data'] is List) {
        final List data = response.data['data'];
        _customers = data.map((item) => Customer.fromJson(Map<String, dynamic>.from(item))).toList();
      }
    } catch (e) {
      debugPrint('Error fetching customers: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void addOptimisticCustomer(String name, String phone) {
    if (name.trim().isEmpty || phone.trim().isEmpty) return;
    final exists = _customers.any((c) => c.phone == phone);
    if (!exists) {
      _customers.insert(0, Customer(
        id: -DateTime.now().millisecondsSinceEpoch,
        name: name,
        email: '',
        phone: phone,
      ));
      notifyListeners();
    }
  }
}
