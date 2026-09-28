import 'package:flutter/material.dart';
import '../api/api_service.dart';

class VendorProvider with ChangeNotifier {
  
  List<dynamic> _vendors = [];
  Map<String, dynamic>? _vendorDetails;
  bool _isLoading = false;
  String? _error;

  List<dynamic> get vendors => _vendors;
  Map<String, dynamic>? get vendorDetails => _vendorDetails;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchVendors({bool isPullToRefresh = false}) async {
    // 1. Instant Cache check: load from cache in 0ms without blocking UI
    if (_vendors.isEmpty) {
      final cached = await ApiService.instance.getLocalCache('vendors');
      if (cached != null && cached['data']?['data'] is List) {
        _vendors = cached['data']['data'];
        _isLoading = false;
        notifyListeners();
      } else if (!isPullToRefresh) {
        _isLoading = true;
        _error = null;
        notifyListeners();
      }
    }

    try {
      final response = await ApiService.instance.get('vendors');
      if (response.statusCode == 200 && response.data?['data']?['data'] is List) {
        _vendors = response.data['data']['data']; // Laravel pagination structure
        _error = null;
      } else if (_vendors.isEmpty) {
        _error = 'ভেন্ডর তালিকা লোড করতে ব্যর্থ হয়েছে।';
      }
    } catch (e) {
      if (_vendors.isEmpty) {
        _error = e.toString();
      }
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchVendorDetails(int id) async {
    _isLoading = true;
    _error = null;
    _vendorDetails = null;
    notifyListeners();

    try {
      final response = await ApiService.instance.get('vendors/$id');
      if (response.statusCode == 200) {
        _vendorDetails = response.data['data'];
      } else {
        _error = 'ভেন্ডর তথ্য লোড করতে ব্যর্থ হয়েছে।';
      }
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createVendor(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await ApiService.instance.post('vendors', data: data);
      _isLoading = false;
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final optimisticVendor = {
            'id': -DateTime.now().millisecondsSinceEpoch,
            'name': data['name'] ?? '',
            'type': data['type'] ?? '',
            'contact_person': data['contact_person'] ?? '',
            'phone': data['phone'] ?? '',
            'email': data['email'] ?? '',
            'commission_rate': data['commission_rate'] ?? 0,
            'balance': 0,
          };
          _vendors.insert(0, optimisticVendor);
        } else {
          await fetchVendors();
        }
        notifyListeners();
        return true;
      }
      notifyListeners();
      return false;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateVendor(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await ApiService.instance.put('vendors/$id', data: data);
      _isLoading = false;
      if (response.statusCode == 200 || response.statusCode == 202) {
        final index = _vendors.indexWhere((v) => v['id'] == id);
        if (index != -1) {
          _vendors[index] = {
            ..._vendors[index],
            ...data,
          };
        }
        notifyListeners();
        return true;
      }
      notifyListeners();
      return false;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteVendor(int id) async {
    try {
      final response = await ApiService.instance.delete('vendors/$id');
      if (response.statusCode == 200 || response.statusCode == 202) {
        _vendors.removeWhere((v) => v['id'] == id);
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<bool> collectCommission(int commissionId) async {
    try {
      final response = await ApiService.instance.post('commissions/$commissionId/collect', data: {});
      return response.statusCode == 200 || response.statusCode == 201 || response.statusCode == 202;
    } catch (e) {
      return false;
    }
  }

  Future<bool> payVendor(int commissionId) async {
    try {
      final response = await ApiService.instance.post('commissions/$commissionId/pay-vendor', data: {});
      return response.statusCode == 200 || response.statusCode == 201 || response.statusCode == 202;
    } catch (e) {
      return false;
    }
  }
}
