import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../api/api_service.dart';
import '../api/database_service.dart';
import '../utils/constants.dart';

class AuthProvider with ChangeNotifier {
  final _storage = const FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );
  bool _isAuthenticated = false;
  Map<String, dynamic>? _user;
  Map<String, dynamic>? _subscription;
  bool _isLoading = false;

  bool get isAuthenticated => _isAuthenticated;
  Map<String, dynamic>? get user => _user;
  Map<String, dynamic>? get subscription => _subscription;
  bool get isLoading => _isLoading;

  bool get isSubscriptionActive {
    if (_subscription == null) return true; // Default true offline if unverified
    final currentSub = _subscription!['current_subscription'];
    if (currentSub == null) return true;
    return currentSub['is_active'] == true;
  }

  Future<void> checkAuth() async {
    final token = await _storage.read(key: AppConstants.tokenKey);
    final cachedUserJson = await _storage.read(key: 'cached_user_profile');
    final cachedSubJson = await _storage.read(key: 'cached_subscription');

    if (token != null && token.isNotEmpty) {
      _isAuthenticated = true;

      if (cachedUserJson != null) {
        try {
          _user = jsonDecode(cachedUserJson);
        } catch (_) {}
      }
      if (cachedSubJson != null) {
        try {
          _subscription = jsonDecode(cachedSubJson);
        } catch (_) {}
      }
      notifyListeners();

      try {
        final response = await ApiService.instance.get('user');
        if (response.statusCode == 200) {
          _user = response.data['data'];
          _isAuthenticated = true;
          await _storage.write(key: 'cached_user_profile', value: jsonEncode(_user));
          await fetchSubscription();
        } else if (response.statusCode == 401) {
          await logout();
        }
      } catch (e) {
        debugPrint('checkAuth offline/network error: $e');
        _isAuthenticated = true;
      }
    } else {
      _isAuthenticated = false;
    }
    notifyListeners();
  }

  Future<void> fetchSubscription() async {
    try {
      final response = await ApiService.instance.get('/subscription');
      if (response.statusCode == 200) {
        _subscription = response.data['data'];
        await _storage.write(key: 'cached_subscription', value: jsonEncode(_subscription));
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error fetching subscription: $e');
    }
  }

  Future<bool> login(String loginInput, String password) async {
    _isLoading = true;
    notifyListeners();

    try {
      final isEmail = loginInput.contains('@');
      final response = await ApiService.instance.post('/login', data: {
        'login': loginInput,
        if (isEmail) 'email': loginInput else 'phone': loginInput,
        'password': password,
        'device_name': 'Mobile App',
      });

      if (response.statusCode == 200) {
        final token = response.data['token'];
        _user = response.data['user'];
        await _storage.write(key: AppConstants.tokenKey, value: token);
        await _storage.write(key: 'cached_user_profile', value: jsonEncode(_user));
        _isAuthenticated = true;
        _isLoading = false;
        await fetchSubscription();
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Login Error: $e');
      _isLoading = false;
      notifyListeners();
      return false;
    }
    
    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<bool> register(String name, String businessName, String email, String password, String passwordConfirmation, {String? phone}) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await ApiService.instance.post('/register', data: {
        'name': name,
        'business_name': businessName,
        'email': email,
        if (phone != null && phone.isNotEmpty) 'phone': phone,
        'password': password,
        'password_confirmation': passwordConfirmation,
      });

      if (response.statusCode == 201) {
        final token = response.data['token'];
        _user = response.data['user'];
        await _storage.write(key: AppConstants.tokenKey, value: token);
        await _storage.write(key: 'cached_user_profile', value: jsonEncode(_user));
        _isAuthenticated = true;
        _isLoading = false;
        await fetchSubscription();
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Registration Error: $e');
      _isLoading = false;
      notifyListeners();
      return false;
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<bool> checkoutSubscription(Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.post('/subscription/checkout', data: data);
      if (response.statusCode == 201 || response.statusCode == 202) {
        return true;
      }
    } catch (e) {
      debugPrint('Checkout Error: $e');
    }
    return false;
  }

  Future<List> fetchSubscriptionHistory() async {
    try {
      final response = await ApiService.instance.get('/subscription/history');
      if (response.statusCode == 200) {
        return response.data['data']['data'];
      }
    } catch (e) {
      debugPrint('Error fetching history: $e');
    }
    return [];
  }

  Future<void> logout() async {
    try {
      await ApiService.instance.get('/logout');
    } catch (e) {
      // Ignore logout errors
    }
    await _storage.delete(key: AppConstants.tokenKey);
    await _storage.delete(key: 'cached_user_profile');
    await _storage.delete(key: 'cached_subscription');
    await DatabaseService.instance.clearDatabase();
    _isAuthenticated = false;
    _user = null;
    _subscription = null;
    notifyListeners();
  }

  Future<bool> updateProfile(String name, String email) async {
    try {
      final response = await ApiService.instance.post('/user/profile', data: {
        'name': name,
        'email': email,
      });
      if (response.statusCode == 200) {
        _user = response.data['data'];
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Error updating profile: $e');
    }
    return false;
  }

  Future<bool> changePassword(String currentPassword, String password, String passwordConfirmation) async {
    try {
      final response = await ApiService.instance.post('/user/password', data: {
        'current_password': currentPassword,
        'password': password,
        'password_confirmation': passwordConfirmation,
      });
      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error changing password: $e');
    }
    return false;
  }

  Future<bool> updateTenantSettings(Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.post('/tenant/settings', data: data);
      if (response.statusCode == 200) {
        // Refresh profile to get updated tenant data
        await checkAuth();
        return true;
      }
    } catch (e) {
      debugPrint('Error updating tenant settings: $e');
    }
    return false;
  }
}
