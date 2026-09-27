import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/report_provider.dart';
import '../../theme/app_theme.dart';

class ReportViewScreen extends StatefulWidget {
  final String type;
  final String startDate;
  final String endDate;
  final String title;

  const ReportViewScreen({
    super.key,
    required this.type,
    required this.startDate,
    required this.endDate,
    required this.title,
  });

  @override
  State<ReportViewScreen> createState() => _ReportViewScreenState();
}

class _ReportViewScreenState extends State<ReportViewScreen> {
  bool _isDownloading = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ReportProvider>().fetchSummary(widget.startDate, widget.endDate);
    });
  }

  Future<void> _download() async {
    setState(() => _isDownloading = true);
    final path = await context.read<ReportProvider>().downloadPdf(
      widget.type,
      widget.startDate,
      widget.endDate,
    );
    setState(() => _isDownloading = false);

    if (path != null && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('ডাউনলোড সফল হয়েছে: $path'),
          action: SnackBarAction(label: 'OK', onPressed: () {}),
        ),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('ডাউনলোড ব্যর্থ হয়েছে')),
      );
    }
  }

  double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is num) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(widget.title, style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        actions: [
          IconButton(
            icon: _isDownloading 
              ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primaryEmerald))
              : const Icon(Icons.download_outlined, color: AppTheme.primaryEmerald),
            onPressed: _isDownloading ? null : _download,
          ),
        ],
      ),
      body: Consumer<ReportProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator(color: AppTheme.primaryEmerald));
          }

          final summary = provider.summary;
          if (summary == null) {
            return const Center(child: Text('তথ্য পাওয়া যায়নি'));
          }

          return ListView(
            padding: const EdgeInsets.all(24),
            children: [
              _buildSummaryCard(summary),
              const SizedBox(height: 24),
              _buildPricingSection(summary),
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity,
                height: 60,
                child: ElevatedButton.icon(
                  onPressed: _isDownloading ? null : _download,
                  icon: const Icon(Icons.picture_as_pdf, color: Colors.white),
                  label: Text(
                    _isDownloading ? 'ডাউনলোড হচ্ছে...' : 'PDF ডাউনলোড করুন',
                    style: GoogleFonts.manrope(fontSize: 16, fontWeight: FontWeight.w800, color: Colors.white),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primaryEmerald,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildSummaryCard(Map<String, dynamic> summary) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppTheme.textNavy,
        borderRadius: BorderRadius.circular(32),
      ),
      child: Column(
        children: [
          Text(
            'মোট বুকিং',
            style: TextStyle(color: Colors.white.withAlpha(150), fontSize: 13, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Text(
            summary['total_bookings'].toString(),
            style: GoogleFonts.manrope(color: Colors.white, fontSize: 48, fontWeight: FontWeight.w900),
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildMiniStat('কনফার্মড', summary['confirmed_bookings'].toString(), AppTheme.primaryEmerald),
              _buildMiniStat('পেন্ডিং', summary['pending_bookings'].toString(), Colors.orange),
              _buildMiniStat('বাতিল', summary['cancelled_bookings'].toString(), Colors.red),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMiniStat(String label, String value, Color color) {
    return Column(
      children: [
        Text(value, style: GoogleFonts.manrope(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
        const SizedBox(height: 4),
        Text(label, style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
      ],
    );
  }

  Widget _buildPricingSection(Map<String, dynamic> summary) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
      ),
      child: Column(
        children: [
          _buildPriceRow('মোট আয়', _toDouble(summary['total_income']), AppTheme.primaryEmerald),
          const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider()),
          _buildPriceRow('মোট ব্যয়', _toDouble(summary['total_expense']), Colors.red),
          const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider()),
          _buildPriceRow('নিট লাভ/ক্ষতি', _toDouble(summary['net_profit']), _toDouble(summary['net_profit']) >= 0 ? Colors.blue : Colors.red, isBold: true),
        ],
      ),
    );
  }

  Widget _buildPriceRow(String label, double amount, Color color, {bool isBold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: GoogleFonts.manrope(fontWeight: isBold ? FontWeight.w800 : FontWeight.w600, fontSize: 14, color: AppTheme.textNavy)),
        Text(
          '৳${amount.toStringAsFixed(2)}',
          style: GoogleFonts.manrope(fontWeight: isBold ? FontWeight.w900 : FontWeight.w700, fontSize: 16, color: color),
        ),
      ],
    );
  }
}
