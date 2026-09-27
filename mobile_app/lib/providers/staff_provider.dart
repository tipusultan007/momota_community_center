import 'package:flutter/material.dart';
import '../api/api_service.dart';
import '../models/staff.dart';
import '../models/salary.dart';

class StaffProvider with ChangeNotifier {
  List<Staff> _staffList = [];
  List<Salary> _salaryList = [];
  bool _isLoading = false;

  List<Staff> get staffList => _staffList;
  List<Salary> get salaryList => _salaryList;
  bool get isLoading => _isLoading;

  Future<void> fetchStaff({bool isPullToRefresh = false}) async {
    // 1. Instant Cache check: load from cache in 0ms without blocking UI
    if (_staffList.isEmpty) {
      final cached = await ApiService.instance.getLocalCache('/staff');
      if (cached != null && cached['data'] is List) {
        final List list = cached['data'];
        _staffList = list.map((item) => Staff.fromJson(Map<String, dynamic>.from(item))).toList();
        _isLoading = false;
        notifyListeners();
      } else if (!isPullToRefresh) {
        _isLoading = true;
        notifyListeners();
      }
    }

    // 2. Fresh network sync
    try {
      final response = await ApiService.instance.get('/staff');
      if (response.statusCode == 200 && response.data?['data'] is List) {
        final List data = response.data['data'];
        _staffList = data.map((item) => Staff.fromJson(Map<String, dynamic>.from(item))).toList();
      }
    } catch (e) {
      debugPrint('Error fetching staff: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchAllSalaries() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await ApiService.instance.get('/salaries');
      if (response.statusCode == 200) {
        final List data = response.data['data']['data'];
        _salaryList = data.map((item) => Salary.fromJson(Map<String, dynamic>.from(item))).toList();
      }
    } catch (e) {
      debugPrint('Error fetching all salaries: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<Staff?> getStaffDetails(int id) async {
    try {
      final response = await ApiService.instance.get('/staff/$id');
      if (response.statusCode == 200) {
        return Staff.fromJson(Map<String, dynamic>.from(response.data['data']));
      }
    } catch (e) {
      debugPrint('Error fetching staff details: $e');
    }
    return null;
  }

  Future<bool> createStaff(Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.post('/staff', data: data);
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final newStaff = Staff(
            id: -DateTime.now().millisecondsSinceEpoch,
            name: data['name'] ?? '',
            phone: data['phone'] ?? '',
            designation: data['designation'] ?? data['role'] ?? 'Staff',
            salaryAmount: (data['salary_amount'] as num?)?.toDouble() ?? 0.0,
            isActive: true,
            status: 'active',
          );
          _staffList.insert(0, newStaff);
          notifyListeners();
        } else {
          await fetchStaff();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error creating staff: $e');
    }
    return false;
  }

  Future<bool> updateStaff(int id, Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.patch('/staff/$id', data: data);
      if (response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final index = _staffList.indexWhere((s) => s.id == id);
          if (index != -1) {
            final old = _staffList[index];
            _staffList[index] = Staff(
              id: old.id,
              name: data['name'] ?? old.name,
              phone: data['phone'] ?? old.phone,
              designation: data['designation'] ?? data['role'] ?? old.designation,
              salaryAmount: (data['salary_amount'] as num?)?.toDouble() ?? old.salaryAmount,
              isActive: old.isActive,
              status: old.status,
            );
            notifyListeners();
          }
        } else {
          await fetchStaff();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error updating staff: $e');
    }
    return false;
  }

  Future<bool> deleteStaff(int id) async {
    try {
      final response = await ApiService.instance.delete('/staff/$id');
      if (response.statusCode == 200 || response.statusCode == 202) {
        _staffList.removeWhere((s) => s.id == id);
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Error deleting staff: $e');
    }
    return false;
  }

  Future<bool> updateStatus(int id, String status) async {
    try {
      final response = await ApiService.instance.patch('/staff/$id/status', data: {
        'status': status,
      });
      if (response.statusCode == 200 || response.statusCode == 202) {
        final index = _staffList.indexWhere((s) => s.id == id);
        if (index != -1) {
          final old = _staffList[index];
          _staffList[index] = Staff(
            id: old.id,
            name: old.name,
            phone: old.phone,
            designation: old.designation,
            salaryAmount: old.salaryAmount,
            isActive: status == 'active',
            status: status,
          );
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error updating staff status: $e');
    }
    return false;
  }

  Future<bool> paySalary(int staffId, Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.post('/staff/$staffId/pay-salary', data: data);
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        await fetchStaff();
        return true;
      }
    } catch (e) {
      debugPrint('Error paying salary: $e');
    }
    return false;
  }
}
