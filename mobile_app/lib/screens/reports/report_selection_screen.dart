import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import 'report_view_screen.dart';

class ReportSelectionScreen extends StatefulWidget {
  const ReportSelectionScreen({super.key});

  @override
  State<ReportSelectionScreen> createState() => _ReportSelectionScreenState();
}

class _ReportSelectionScreenState extends State<ReportSelectionScreen> {
  DateTime _startDate = DateTime.now().subtract(const Duration(days: 30));
  DateTime _endDate = DateTime.now();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('রিপোর্ট এবং অ্যানালিটিক্স', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            _buildDateRangeSelector(),
            const SizedBox(height: 32),
            _buildReportOption(
              context,
              'আর্থিক রিপোর্ট (Financial)',
              'আয়, ব্যয় এবং লাভের বিস্তারিত বিবরণ',
              Icons.account_balance_outlined,
              AppTheme.primaryEmerald,
              'financial',
            ),
            const SizedBox(height: 16),
            _buildReportOption(
              context,
              'বুকিং রিপোর্ট (Bookings)',
              'বুকিং স্ট্যাটাস এবং ইভেন্টের তালিকা',
              Icons.calendar_today_outlined,
              Colors.blue,
              'bookings',
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDateRangeSelector() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppTheme.textNavy,
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(
            color: AppTheme.textNavy.withAlpha(50),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'রিপোর্ট পিরিয়ড সিলেক্ট করুন',
            style: GoogleFonts.manrope(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.white.withAlpha(180)),
          ),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(child: _buildDateItem('শুরু', _startDate, (d) => setState(() => _startDate = d))),
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 16),
                child: Icon(Icons.arrow_forward, color: Colors.white24, size: 20),
              ),
              Expanded(child: _buildDateItem('শেষ', _endDate, (d) => setState(() => _endDate = d))),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildDateItem(String label, DateTime date, Function(DateTime) onSelect) {
    return InkWell(
      onTap: () async {
        final d = await showDatePicker(
          context: context,
          initialDate: date,
          firstDate: DateTime(2020),
          lastDate: DateTime.now(),
        );
        if (d != null) onSelect(d);
      },
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(color: Colors.white70, fontSize: 11)),
          const SizedBox(height: 6),
          Text(
            DateFormat('dd MMM, yyyy').format(date),
            style: GoogleFonts.manrope(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 14),
          ),
        ],
      ),
    );
  }

  Widget _buildReportOption(BuildContext context, String title, String subtitle, IconData icon, Color color, String type) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(5),
            blurRadius: 15,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: ListTile(
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => ReportViewScreen(
              type: type,
              startDate: DateFormat('yyyy-MM-dd').format(_startDate),
              endDate: DateFormat('yyyy-MM-dd').format(_endDate),
              title: title,
            ),
          ),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        leading: Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: color.withAlpha(20),
            borderRadius: BorderRadius.circular(14),
          ),
          child: Icon(icon, color: color, size: 24),
        ),
        title: Text(
          title,
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16, color: AppTheme.textNavy),
        ),
        subtitle: Text(
          subtitle,
          style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
        ),
        trailing: const Icon(Icons.chevron_right, color: Colors.grey),
      ),
    );
  }
}
