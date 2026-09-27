import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import '../api/api_service.dart';

class UserProvider with ChangeNotifier {
  List<dynamic> _users = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<dynamic> get users => _users;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchUsers({bool isPullToRefresh = false}) async {
    if (!isPullToRefresh && _users.isEmpty) {
      final cached = await ApiService.instance.getLocalCache('/users');
      if (cached != null && cached['data'] != null) {
        _users = cached['data'];
        notifyListeners();
      }
    }

    if (_users.isEmpty) {
      _isLoading = true;
      notifyListeners();
    }

    try {
      final response = await ApiService.instance.get('/users');
      if (response.statusCode == 200 && response.data != null) {
        _users = response.data['data'] ?? [];
        _errorMessage = null;
      }
    } catch (e) {
      debugPrint('Error fetching users: $e');
      _errorMessage = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>> createUser(Map<String, dynamic> data) async {
    _errorMessage = null;
    try {
      final response = await ApiService.instance.post('/users', data: data);
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          _users.insert(0, {
            'id': -DateTime.now().millisecondsSinceEpoch,
            'name': data['name'] ?? '',
            'email': data['email'] ?? '',
            'role': data['role'] ?? 'Staff',
            'phone': data['phone'] ?? '',
            'roles': [{'name': data['role'] ?? 'Staff'}],
          });
          notifyListeners();
        } else {
          await fetchUsers(isPullToRefresh: true);
        }
        return {
          'success': true,
          'message': response.data?['message'] ?? 'ব্যবহারকারী সফলভাবে তৈরি করা হয়েছে।'
        };
      }
      return {
        'success': false,
        'message': response.data?['message'] ?? 'তৈরি করা সম্ভব হয়নি।'
      };
    } catch (e) {
      debugPrint('Error creating user: $e');
      String msg = 'ব্যবহারকারী তৈরিতে ব্যর্থ হয়েছে।';
      if (e is DioException) {
        final resData = e.response?.data;
        if (resData is Map) {
          msg = resData['message']?.toString() ?? resData['error']?.toString() ?? msg;
        } else if (e.message != null) {
          msg = e.message!;
        }
      }
      _errorMessage = msg;
      return {'success': false, 'message': msg};
    }
  }

  Future<Map<String, dynamic>> updateUser(int id, Map<String, dynamic> data) async {
    _errorMessage = null;
    try {
      final response = await ApiService.instance.put('/users/$id', data: data);
      if (response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final index = _users.indexWhere((u) => u['id'] == id);
          if (index != -1) {
            _users[index] = {
              ..._users[index],
              ...data,
              'roles': [{'name': data['role'] ?? 'Staff'}],
            };
            notifyListeners();
          }
        } else {
          await fetchUsers(isPullToRefresh: true);
        }
        return {
          'success': true,
          'message': response.data?['message'] ?? 'ব্যবহারকারী সফলভাবে আপডেট করা হয়েছে।'
        };
      }
      return {
        'success': false,
        'message': response.data?['message'] ?? 'আপডেট করা সম্ভব হয়নি।'
      };
    } catch (e) {
      debugPrint('Error updating user: $e');
      String msg = 'আপডেট করতে ব্যর্থ হয়েছে।';
      if (e is DioException) {
        final resData = e.response?.data;
        if (resData is Map) {
          msg = resData['message']?.toString() ?? resData['error']?.toString() ?? msg;
        } else if (e.message != null) {
          msg = e.message!;
        }
      }
      _errorMessage = msg;
      return {'success': false, 'message': msg};
    }
  }

  Future<Map<String, dynamic>> deleteUser(int id) async {
    _errorMessage = null;
    try {
      final response = await ApiService.instance.delete('/users/$id');
      if (response.statusCode == 200 || response.statusCode == 202) {
        _users.removeWhere((u) => u['id'] == id);
        notifyListeners();
        return {
          'success': true,
          'message': response.data?['message'] ?? 'ব্যবহারকারী মুছে ফেলা হয়েছে।'
        };
      }
      return {
        'success': false,
        'message': response.data?['message'] ?? 'মুছে ফেলা সম্ভব হয়নি।'
      };
    } catch (e) {
      debugPrint('Error deleting user: $e');
      String msg = 'মুছে ফেলতে ব্যর্থ হয়েছে।';
      if (e is DioException) {
        final resData = e.response?.data;
        if (resData is Map) {
          msg = resData['message']?.toString() ?? msg;
        }
      }
      return {'success': false, 'message': msg};
    }
  }
}
