import 'package:flutter/material.dart';
import '../api/api_service.dart';
import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';
import 'dart:io';

class ReportProvider with ChangeNotifier {
  Map<String, dynamic>? _summary;
  bool _isLoading = false;

  Map<String, dynamic>? get summary => _summary;
  bool get isLoading => _isLoading;

  Future<void> fetchSummary(String startDate, String endDate) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await ApiService.instance.get(
        '/reports/summary',
        queryParameters: {
          'start_date': startDate,
          'end_date': endDate,
        },
      );
      if (response.statusCode == 200) {
        _summary = response.data['data'];
      }
    } catch (e) {
      debugPrint('Error fetching report summary: $e');
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<String?> downloadPdf(String type, String startDate, String endDate) async {
    try {
      final dio = ApiService.instance.client;
      final directory = await getApplicationDocumentsDirectory();
      final filePath = '${directory.path}/report_${type}_${startDate}.pdf';

      final response = await dio.get(
        '/reports/export',
        queryParameters: {
          'type': type,
          'start_date': startDate,
          'end_date': endDate,
        },
        options: Options(
          responseType: ResponseType.bytes,
          followRedirects: false,
        ),
      );

      final file = File(filePath);
      await file.writeAsBytes(response.data);
      return filePath;
    } catch (e) {
      debugPrint('Error downloading PDF: $e');
      return null;
    }
  }
}
