import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../api/api_service.dart';

class HallProvider with ChangeNotifier {
  final _storage = const FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );
  List<dynamic> _halls = [];
  int? _activeHallId;
  bool _isLoading = false;

  List<dynamic> get halls => _halls;
  int? get activeHallId => _activeHallId;
  bool get isLoading => _isLoading;

  Map<String, dynamic> get activeHall {
    if (_halls.isNotEmpty) {
      if (_activeHallId != null) {
        try {
          return _halls.firstWhere((h) => h['id'] == _activeHallId);
        } catch (_) {}
      }
      return _halls.first;
    }
    return {
      'id': 1,
      'name': 'মমতা কমিউনিটি সেন্টার',
      'price_per_slot': 30000,
      'default_server_rate': 500,
    };
  }

  Future<void> fetchHalls() async {
    _isLoading = true;
    notifyListeners();

    try {
      print('Fetching halls from api/halls...');
      final response = await ApiService.instance.get('halls');
      print('Halls response: ${response.statusCode}');
      if (response.statusCode == 200) {
        _halls = response.data['data'];
        print('Loaded ${_halls.length} halls');
        
        // Load persisted hall ID or default to first
        final storedId = await _storage.read(key: 'active_hall_id');
        if (storedId != null) {
          _activeHallId = int.tryParse(storedId);
        }
        
        if (_activeHallId == null && _halls.isNotEmpty) {
          _activeHallId = _halls.first['id'];
        }
        if (_activeHallId != null) {
          await _storage.write(key: 'active_hall_id', value: _activeHallId.toString());
        }
      }
    } catch (e) {
      print('Error fetching halls: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> switchHall(int id) async {
    _activeHallId = id;
    await _storage.write(key: 'active_hall_id', value: id.toString());
    notifyListeners();
  }
}
