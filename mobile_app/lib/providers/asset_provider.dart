import 'package:flutter/material.dart';
import '../models/asset.dart';
import '../api/api_service.dart';

class AssetProvider with ChangeNotifier {
  List<Asset> _assets = [];
  bool _isLoading = false;

  List<Asset> get assets => _assets;
  bool get isLoading => _isLoading;

  Future<void> fetchAssets() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await ApiService.instance.get('/assets');

      if (response.statusCode == 200) {
        final List data = response.data['data'];
        _assets = data.map((json) => Asset.fromJson(json)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching assets: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createAsset(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await ApiService.instance.post('/assets', data: data);
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final optimisticAsset = Asset(
            id: -DateTime.now().millisecondsSinceEpoch,
            name: data['name'] ?? '',
            totalStock: int.tryParse(data['total_stock']?.toString() ?? data['total_quantity']?.toString() ?? '1') ?? 1,
            availableStock: int.tryParse(data['available_stock']?.toString() ?? data['total_quantity']?.toString() ?? '1') ?? 1,
            description: data['description'] ?? data['notes'],
          );
          _assets.insert(0, optimisticAsset);
        } else {
          await fetchAssets();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error creating asset: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateAsset(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await ApiService.instance.patch('/assets/$id', data: data);
      if (response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final index = _assets.indexWhere((a) => a.id == id);
          if (index != -1) {
            final old = _assets[index];
            _assets[index] = Asset(
              id: old.id,
              name: data['name'] ?? old.name,
              totalStock: int.tryParse(data['total_stock']?.toString() ?? data['total_quantity']?.toString() ?? old.totalStock.toString()) ?? old.totalStock,
              availableStock: int.tryParse(data['available_stock']?.toString() ?? data['available_quantity']?.toString() ?? old.availableStock.toString()) ?? old.availableStock,
              description: data['description'] ?? data['notes'] ?? old.description,
            );
          }
        } else {
          await fetchAssets();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error updating asset: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteAsset(int id) async {
    _isLoading = true;
    notifyListeners();
    try {
      final response = await ApiService.instance.delete('/assets/$id');
      if (response.statusCode == 200 || response.statusCode == 202) {
        _assets.removeWhere((a) => a.id == id);
        return true;
      }
    } catch (e) {
      debugPrint('Error deleting asset: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }
}
