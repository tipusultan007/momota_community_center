import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../utils/constants.dart';
import 'database_service.dart';

class ApiService {
  static final ApiService instance = ApiService._internal();
  factory ApiService() => instance;

  late Dio _dio;
  final _storage = const FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );

  final ValueNotifier<int> pendingSyncCount = ValueNotifier<int>(0);
  final ValueNotifier<bool> isSyncing = ValueNotifier<bool>(false);
  final ValueNotifier<bool> isOnline = ValueNotifier<bool>(true);
  VoidCallback? onSyncComplete;

  String _normalizePath(String path) {
    if (path.startsWith('/')) {
      return path.substring(1);
    }
    return path;
  }

  ApiService._internal() {
    _dio = Dio(BaseOptions(
      baseUrl: AppConstants.baseUrl,
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 10),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: AppConstants.tokenKey);
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        
        final activeHallId = await _storage.read(key: 'active_hall_id');
        if (activeHallId != null && !options.headers.containsKey('X-Hall-Id')) {
          options.headers['X-Hall-Id'] = activeHallId;
        }
        
        return handler.next(options);
      },
      onError: (DioException e, handler) async {
        if (_isOfflineError(e)) {
          isOnline.value = false;
        }
        if (e.response?.statusCode == 401) {
          // Handle unauthorized
        }
        return handler.next(e);
      },
    ));

    // Initialize pending count from DB
    _refreshPendingCount();
  }

  Dio get client => _dio;

  Future<void> _refreshPendingCount() async {
    try {
      final count = await DatabaseService.instance.getQueueCount();
      pendingSyncCount.value = count;
    } catch (_) {}
  }

  bool _isOfflineError(dynamic e) {
    if (e is DioException) {
      if (e.type == DioExceptionType.connectionError ||
          e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.sendTimeout ||
          e.type == DioExceptionType.receiveTimeout) {
        return true;
      }
      if (e.error is SocketException) {
        return true;
      }
      final msg = e.message?.toLowerCase() ?? '';
      if (msg.contains('socketexception') ||
          msg.contains('failed host lookup') ||
          msg.contains('network is unreachable') ||
          msg.contains('connection refused') ||
          msg.contains('timed out')) {
        return true;
      }
    }
    if (e is SocketException) {
      return true;
    }
    return false;
  }

  final Map<String, dynamic> _memoryCache = {};

  Future<String> _generateCacheKey(String path, Map<String, dynamic>? queryParameters) async {
    final activeHallId = await _storage.read(key: 'active_hall_id') ?? 'default';
    final normalizedPath = path.startsWith('/') ? path : '/$path';
    if (queryParameters == null || queryParameters.isEmpty) {
      return '[$activeHallId]$normalizedPath';
    }
    final sortedQuery = Uri(queryParameters: queryParameters.map((k, v) => MapEntry(k, v.toString()))).query;
    return '[$activeHallId]$normalizedPath?$sortedQuery';
  }

  // Instant local cache getter (Memory first, then SQLite)
  Future<dynamic> getLocalCache(String path, {Map<String, dynamic>? queryParameters}) async {
    final cleanPath = _normalizePath(path);
    final cacheKey = await _generateCacheKey(cleanPath, queryParameters);
    
    // 1. Instant RAM check
    if (_memoryCache.containsKey(cacheKey)) {
      return _memoryCache[cacheKey];
    }
    
    // 2. Persistent SQLite check
    final sqliteCache = await DatabaseService.instance.getCache(cacheKey);
    if (sqliteCache != null) {
      _memoryCache[cacheKey] = sqliteCache;
      return sqliteCache;
    }
    return null;
  }

  // GET with Caching
  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) async {
    final cleanPath = _normalizePath(path);
    final cacheKey = await _generateCacheKey(cleanPath, queryParameters);
    try {
      final response = await _dio.get(cleanPath, queryParameters: queryParameters);
      isOnline.value = true;
      
      // Cache the successful response in both memory and SQLite
      if (response.statusCode == 200 && response.data != null) {
        _memoryCache[cacheKey] = response.data;
        await DatabaseService.instance.saveCache(cacheKey, response.data);
      }
      
      return response;
    } catch (e) {
      if (_isOfflineError(e)) {
        isOnline.value = false;
      }

      // If network fails, try to return from memory or SQLite cache
      final cachedData = _memoryCache[cacheKey] ?? await DatabaseService.instance.getCache(cacheKey);
      
      if (cachedData != null) {
        _memoryCache[cacheKey] = cachedData;
        return Response(
          data: cachedData,
          statusCode: 200,
          requestOptions: RequestOptions(path: cleanPath),
        );
      }

      // If no cache and network failed, provide safe defaults for standard endpoints
      if (_isOfflineError(e)) {
        if (cleanPath.contains('bookings/check-availability')) {
          return Response(
            data: {'available': true, 'message': 'স্লটটি ফাঁকা রয়েছে।'},
            statusCode: 200,
            requestOptions: RequestOptions(path: cleanPath),
          );
        } else if (cleanPath.contains('bookings') && !cleanPath.contains('invoice')) {
          return Response(
            data: {'data': []},
            statusCode: 200,
            requestOptions: RequestOptions(path: cleanPath),
          );
        } else if (cleanPath.contains('accounting/categories')) {
          return Response(
            data: {'data': {'income': [], 'expense': []}},
            statusCode: 200,
            requestOptions: RequestOptions(path: cleanPath),
          );
        } else if (cleanPath.contains('accounting')) {
          return Response(
            data: {
              'data': {
                'transactions': [],
                'summary': {'total_income': 0, 'total_expense': 0, 'net_profit': 0},
                'pagination': {'current_page': 1, 'last_page': 1}
              }
            },
            statusCode: 200,
            requestOptions: RequestOptions(path: cleanPath),
          );
        } else if (cleanPath.contains('customers') ||
                   cleanPath.contains('staff') ||
                   cleanPath.contains('vendors') ||
                   cleanPath.contains('assets') ||
                   cleanPath.contains('salaries')) {
          return Response(
            data: {'data': []},
            statusCode: 200,
            requestOptions: RequestOptions(path: cleanPath),
          );
        } else if (cleanPath.contains('halls')) {
          return Response(
            data: {'data': []},
            statusCode: 200,
            requestOptions: RequestOptions(path: cleanPath),
          );
        }
      }

      rethrow;
    }
  }

  void invalidateCache([String? pattern]) {
    if (pattern == null) {
      _memoryCache.clear();
      DatabaseService.instance.deleteCache();
    } else {
      _memoryCache.removeWhere((k, _) => k.contains(pattern));
      DatabaseService.instance.deleteCache(pattern);
    }
  }

  void _invalidateRelatedCaches(String path) {
    if (path.contains('bookings')) {
      invalidateCache('bookings');
      invalidateCache('dashboard');
      invalidateCache('accounting');
    } else if (path.contains('accounting')) {
      invalidateCache('accounting');
      invalidateCache('dashboard');
    } else if (path.contains('staff') || path.contains('salaries')) {
      invalidateCache('staff');
      invalidateCache('salaries');
      invalidateCache('dashboard');
    } else if (path.contains('customers')) {
      invalidateCache('customers');
    } else if (path.contains('users')) {
      invalidateCache('users');
    }
  }

  // POST with Sync Queue
  Future<Response> post(String path, {dynamic data}) async {
    final cleanPath = _normalizePath(path);
    try {
      final res = await _dio.post(cleanPath, data: data);
      isOnline.value = true;
      _invalidateRelatedCaches(cleanPath);
      return res;
    } catch (e) {
      if (_isOfflineError(e)) {
        isOnline.value = false;
        final activeHallId = await _storage.read(key: 'active_hall_id');
        await DatabaseService.instance.addToQueue('POST', cleanPath, data, hallId: activeHallId);
        await _refreshPendingCount();
        _invalidateRelatedCaches(cleanPath);
        return Response(
          data: {'message': 'Offline: Action added to sync queue', 'offline': true},
          statusCode: 202,
          requestOptions: RequestOptions(path: cleanPath),
        );
      }
      rethrow;
    }
  }

  Future<Response> put(String path, {dynamic data}) async {
    final cleanPath = _normalizePath(path);
    try {
      final res = await _dio.put(cleanPath, data: data);
      isOnline.value = true;
      _invalidateRelatedCaches(cleanPath);
      return res;
    } catch (e) {
      if (_isOfflineError(e)) {
        isOnline.value = false;
        final activeHallId = await _storage.read(key: 'active_hall_id');
        await DatabaseService.instance.addToQueue('PUT', cleanPath, data, hallId: activeHallId);
        await _refreshPendingCount();
        _invalidateRelatedCaches(cleanPath);
        return Response(
          data: {'message': 'Offline: Action added to sync queue', 'offline': true},
          statusCode: 202,
          requestOptions: RequestOptions(path: cleanPath),
        );
      }
      rethrow;
    }
  }

  Future<Response> patch(String path, {dynamic data}) async {
    final cleanPath = _normalizePath(path);
    try {
      final res = await _dio.patch(cleanPath, data: data);
      isOnline.value = true;
      _invalidateRelatedCaches(cleanPath);
      return res;
    } catch (e) {
      if (_isOfflineError(e)) {
        isOnline.value = false;
        final activeHallId = await _storage.read(key: 'active_hall_id');
        await DatabaseService.instance.addToQueue('PATCH', cleanPath, data, hallId: activeHallId);
        await _refreshPendingCount();
        _invalidateRelatedCaches(cleanPath);
        return Response(
          data: {'message': 'Offline: Action added to sync queue', 'offline': true},
          statusCode: 202,
          requestOptions: RequestOptions(path: cleanPath),
        );
      }
      rethrow;
    }
  }

  Future<Response> delete(String path, {Map<String, dynamic>? queryParameters}) async {
    final cleanPath = _normalizePath(path);
    try {
      final res = await _dio.delete(cleanPath, queryParameters: queryParameters);
      isOnline.value = true;
      _invalidateRelatedCaches(cleanPath);
      return res;
    } catch (e) {
      if (_isOfflineError(e)) {
        isOnline.value = false;
        final activeHallId = await _storage.read(key: 'active_hall_id');
        await DatabaseService.instance.addToQueue('DELETE', cleanPath, queryParameters, hallId: activeHallId);
        await _refreshPendingCount();
        _invalidateRelatedCaches(cleanPath);
        return Response(
          data: {'message': 'Offline: Action added to sync queue', 'offline': true},
          statusCode: 202,
          requestOptions: RequestOptions(path: cleanPath),
        );
      }
      rethrow;
    }
  }

  // Background Sync
  Future<void> processSyncQueue() async {
    if (isSyncing.value) return;

    final queue = await DatabaseService.instance.getQueue();
    if (queue.isEmpty) {
      pendingSyncCount.value = 0;
      return;
    }

    isSyncing.value = true;

    try {
      for (var item in queue) {
        try {
          final method = item['method'];
          final path = item['path'];
          final body = item['body'] != null ? jsonDecode(item['body']) : null;
          final hallId = item['hall_id'];

          final options = Options();
          if (hallId != null) {
            options.headers = {'X-Hall-Id': hallId};
          }

          Response? response;
          if (method == 'POST') {
            response = await _dio.post(path, data: body, options: options);
          } else if (method == 'PUT') {
            response = await _dio.put(path, data: body, options: options);
          } else if (method == 'PATCH') {
            response = await _dio.patch(path, data: body, options: options);
          } else if (method == 'DELETE') {
            response = await _dio.delete(path, options: options);
          }

          if (response != null && (response.statusCode == 200 || response.statusCode == 201 || response.statusCode == 204)) {
            await DatabaseService.instance.removeFromQueue(item['id']);
          }
        } catch (e) {
          debugPrint('Sync failed for item ${item['id']}: $e');
          // If server explicitly rejected with 400 or 422, drop it to prevent deadlock
          if (e is DioException && (e.response?.statusCode == 400 || e.response?.statusCode == 422)) {
            debugPrint('Dropping permanently rejected item ${item['id']}');
            await DatabaseService.instance.removeFromQueue(item['id']);
          } else if (_isOfflineError(e)) {
            // Still offline, abort remaining queue
            isOnline.value = false;
            break;
          }
        }
      }
    } finally {
      await _refreshPendingCount();
      isSyncing.value = false;
      onSyncComplete?.call();
    }
  }
}
