import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';
import 'dart:io';
import '../api/api_service.dart';
import '../models/booking.dart';
import '../models/booking_item.dart';

class BookingProvider with ChangeNotifier {
  List<Booking> _bookings = [];
  bool _isLoading = false;
  String? lastErrorMessage;

  List<Booking> get bookings => _bookings;
  bool get isLoading => _isLoading;

  Future<void> fetchBookings({bool isPullToRefresh = false}) async {
    // 1. Instant Cache: if empty, load from local cache in 0ms without blocking UI
    if (_bookings.isEmpty) {
      final cached = await ApiService.instance.getLocalCache('/bookings');
      if (cached != null && cached['data'] is List) {
        final List list = cached['data'];
        _bookings = list.map((item) => Booking.fromJson(Map<String, dynamic>.from(item))).toList();
        _isLoading = false;
        notifyListeners();
      } else if (!isPullToRefresh) {
        _isLoading = true;
        notifyListeners();
      }
    }

    // 2. Background fresh network sync
    try {
      final response = await ApiService.instance.get('/bookings');
      if (response.statusCode == 200 && response.data != null) {
        final List data = response.data['data'] ?? [];
        _bookings = data.map((item) => Booking.fromJson(Map<String, dynamic>.from(item))).toList();
      }
    } catch (e) {
      debugPrint('Error fetching bookings: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createBooking(Map<String, dynamic> data) async {
    lastErrorMessage = null;
    try {
      final response = await ApiService.instance.post('/bookings', data: data);
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final itemsList = (data['items'] as List? ?? []).map((i) {
            if (i is BookingItem) return i;
            return BookingItem(
              hallId: i['hall_id'] ?? 0,
              hallName: i['hall_name'] ?? '',
              eventDate: i['event_date'] ?? '',
              slot: i['slot'] ?? 'day',
              serverCount: i['server_count'] ?? 0,
              serverRate: (i['server_rate'] as num?)?.toDouble() ?? 0.0,
              isServerIncluded: i['is_server_included'] ?? true,
              basePrice: (i['base_price'] as num?)?.toDouble() ?? 0.0,
              subTotal: (i['sub_total'] as num?)?.toDouble() ?? 0.0,
            );
          }).toList();

          final advance = (data['advance_amount'] as num?)?.toDouble() ?? 0.0;
          final total = (data['total_amount'] as num?)?.toDouble() ?? 0.0;
          final optimisticBooking = Booking(
            id: -DateTime.now().millisecondsSinceEpoch,
            customerId: data['customer_id'] ?? 0,
            customerName: data['customer_name'] ?? '',
            customerPhone: data['customer_phone'] ?? '',
            customerAddress: data['customer_address'],
            totalAmount: total,
            paidAmount: advance,
            status: 'confirmed',
            notes: data['notes'],
            items: itemsList,
            payments: advance > 0
                ? [
                    {
                      'id': -DateTime.now().millisecondsSinceEpoch,
                      'amount': advance,
                      'date': DateTime.now().toIso8601String().split('T')[0],
                      'description': 'অগ্রিম জমা (Offline Advance)',
                    }
                  ]
                : [],
          );
          _bookings.insert(0, optimisticBooking);
          notifyListeners();
        } else {
          await fetchBookings();
        }
        return true;
      }
    } catch (e) {
      if (e is DioException && e.response?.data != null) {
        final resData = e.response!.data;
        if (resData is Map) {
          if (resData['errors'] is Map && (resData['errors'] as Map).isNotEmpty) {
            final firstVal = (resData['errors'] as Map).values.first;
            if (firstVal is List && firstVal.isNotEmpty) {
              lastErrorMessage = firstVal.first.toString();
            } else if (firstVal is String) {
              lastErrorMessage = firstVal;
            }
          }
          lastErrorMessage ??= resData['message']?.toString();
        }
      }
      lastErrorMessage ??= 'বুকিং সম্পন্ন করতে সমস্যা হয়েছে।';
      debugPrint('Error creating booking: $e');
    }
    return false;
  }

  Future<bool> updateBooking(int id, Map<String, dynamic> data) async {
    lastErrorMessage = null;
    try {
      final response = await ApiService.instance.patch('/bookings/$id', data: data);
      if (response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final index = _bookings.indexWhere((b) => b.id == id);
          if (index != -1) {
            final old = _bookings[index];
            final total = (data['total_amount'] as num?)?.toDouble() ?? old.totalAmount;
            final advance = (data['advance_amount'] as num?)?.toDouble() ?? old.paidAmount;
            _bookings[index] = Booking(
              id: old.id,
              customerId: old.customerId,
              customerName: data['customer_name'] ?? old.customerName,
              customerPhone: data['customer_phone'] ?? old.customerPhone,
              customerAddress: data['customer_address'] ?? old.customerAddress,
              totalAmount: total,
              paidAmount: advance,
              status: old.status,
              notes: data['notes'] ?? old.notes,
              items: old.items,
              payments: old.payments,
            );
            notifyListeners();
          }
        } else {
          await fetchBookings();
        }
        return true;
      }
    } catch (e) {
      if (e is DioException && e.response?.data != null) {
        final resData = e.response!.data;
        if (resData is Map) {
          if (resData['errors'] is Map && (resData['errors'] as Map).isNotEmpty) {
            final firstVal = (resData['errors'] as Map).values.first;
            if (firstVal is List && firstVal.isNotEmpty) {
              lastErrorMessage = firstVal.first.toString();
            } else if (firstVal is String) {
              lastErrorMessage = firstVal;
            }
          }
          lastErrorMessage ??= resData['message']?.toString();
        }
      }
      lastErrorMessage ??= 'বুকিং আপডেট করতে সমস্যা হয়েছে।';
      debugPrint('Error updating booking: $e');
    }
    return false;
  }

  Future<bool> deleteBooking(int id) async {
    try {
      final response = await ApiService.instance.delete('/bookings/$id');
      if (response.statusCode == 200 || response.statusCode == 202) {
        _bookings.removeWhere((b) => b.id == id);
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Error deleting booking: $e');
    }
    return false;
  }

  Future<bool> updateStatus(int id, String status) async {
    try {
      final response = await ApiService.instance.put('/bookings/$id/status', data: {'status': status});
      if (response.statusCode == 200 || response.statusCode == 202) {
        final index = _bookings.indexWhere((b) => b.id == id);
        if (index != -1) {
          final old = _bookings[index];
          _bookings[index] = Booking(
            id: old.id,
            customerId: old.customerId,
            customerName: old.customerName,
            customerPhone: old.customerPhone,
            customerAddress: old.customerAddress,
            totalAmount: old.totalAmount,
            paidAmount: old.paidAmount,
            status: status,
            notes: old.notes,
            items: old.items,
            payments: old.payments,
          );
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error updating status: $e');
    }
    return false;
  }

  Future<bool> addPayment(int bookingId, double amount, String date, String description) async {
    try {
      final response = await ApiService.instance.post('/bookings/$bookingId/payment', data: {
        'amount': amount,
        'date': date,
        'description': description,
      });
      if (response.statusCode == 200 || response.statusCode == 201 || response.statusCode == 202) {
        final index = _bookings.indexWhere((b) => b.id == bookingId);
        if (index != -1) {
          final old = _bookings[index];
          final updatedPayments = List<Map<String, dynamic>>.from(old.payments)
            ..insert(0, {
              'id': -DateTime.now().millisecondsSinceEpoch,
              'amount': amount,
              'date': date,
              'description': description,
            });
          final newPaid = old.paidAmount + amount;
          _bookings[index] = Booking(
            id: old.id,
            customerId: old.customerId,
            customerName: old.customerName,
            customerPhone: old.customerPhone,
            customerAddress: old.customerAddress,
            totalAmount: old.totalAmount,
            paidAmount: newPaid,
            status: old.status,
            notes: old.notes,
            items: old.items,
            payments: updatedPayments,
          );
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error adding payment: $e');
    }
    return false;
  }

  Future<bool> payServers(int bookingId) async {
    lastErrorMessage = null;
    try {
      final response = await ApiService.instance.post('/bookings/$bookingId/pay-servers');
      if (response.statusCode == 200 || response.statusCode == 201) {
        await fetchBookings();
        return true;
      }
    } catch (e) {
      if (e is DioException && e.response?.data != null) {
        final resData = e.response!.data;
        if (resData is Map) {
          lastErrorMessage = resData['message']?.toString();
        }
      }
      lastErrorMessage ??= 'পরিবেশনকারীদের বিল পরিশোধ করতে সমস্যা হয়েছে।';
      debugPrint('Error paying servers: $e');
    }
    return false;
  }

  Future<String?> downloadInvoice(int bookingId, String bookingLabel) async {
    try {
      final directory = await getApplicationDocumentsDirectory();
      final filePath = '${directory.path}/invoice_$bookingLabel.pdf';
      final file = File(filePath);

      // Check if already available locally offline
      if (await file.exists()) {
        return filePath;
      }

      final dio = ApiService.instance.client;
      final response = await dio.get(
        '/bookings/$bookingId/invoice',
        options: Options(
          responseType: ResponseType.bytes,
          followRedirects: false,
        ),
      );

      await file.writeAsBytes(response.data);
      return filePath;
    } catch (e) {
      try {
        final directory = await getApplicationDocumentsDirectory();
        final filePath = '${directory.path}/invoice_$bookingLabel.pdf';
        final file = File(filePath);
        if (await file.exists()) {
          return filePath;
        }
      } catch (_) {}
      debugPrint('Error downloading invoice: $e');
      return null;
    }
  }

  Map<DateTime, List<Map<String, dynamic>>> get eventsByDate {
    final Map<DateTime, List<Map<String, dynamic>>> events = {};
    for (var booking in _bookings) {
      for (var item in booking.items) {
        try {
          if (item.eventDate.isEmpty) continue;
          final date = DateTime.parse(item.eventDate);
          final normalizedDate = DateTime(date.year, date.month, date.day);
          if (events[normalizedDate] == null) {
            events[normalizedDate] = [];
          }
          events[normalizedDate]!.add({
            'item': item,
            'booking': booking,
          });
        } catch (e) {
          debugPrint('Error parsing date: ${item.eventDate}');
        }
      }
    }
    return events;
  }

  Future<bool> assignAsset(int itemId, Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.post('/booking-items/$itemId/assign-asset', data: data);
      if (response.statusCode == 200 || response.statusCode == 201 || response.statusCode == 202) {
        await fetchBookings();
        return true;
      }
    } catch (e) {
      debugPrint('Error assigning asset: $e');
    }
    return false;
  }

  Future<bool> returnAsset(int assignmentId, Map<String, dynamic> data) async {
    try {
      final response = await ApiService.instance.post('/asset-assignments/$assignmentId/return', data: data);
      if (response.statusCode == 200 || response.statusCode == 201 || response.statusCode == 202) {
        await fetchBookings();
        return true;
      }
    } catch (e) {
      debugPrint('Error returning asset: $e');
    }
    return false;
  }
}
