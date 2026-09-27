import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/vendor_provider.dart';
import '../../theme/app_theme.dart';
import 'vendor_form_screen.dart';

class VendorDetailsScreen extends StatefulWidget {
  final int vendorId;

  const VendorDetailsScreen({super.key, required this.vendorId});

  @override
  State<VendorDetailsScreen> createState() => _VendorDetailsScreenState();
}

class _VendorDetailsScreenState extends State<VendorDetailsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<VendorProvider>().fetchVendorDetails(widget.vendorId);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text('ভেন্ডর প্রোফাইল'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            onPressed: () {
              final vendor = context.read<VendorProvider>().vendorDetails?['vendor'];
              if (vendor != null) {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => VendorFormScreen(vendor: vendor)),
                );
              }
            },
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.redAccent),
            onPressed: _confirmDelete,
          ),
        ],
      ),
      body: Consumer<VendorProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.error != null) {
            return Center(child: Text(provider.error!));
          }

          final details = provider.vendorDetails;
          if (details == null) return const SizedBox();

          final vendor = details['vendor'];
          final summary = details['summary'];
          final history = details['history'] as List;
          final commissions = details['commissions'] as List;

          return Column(
            children: [
              _buildHeader(vendor, summary),
              TabBar(
                controller: _tabController,
                labelColor: AppTheme.primaryEmerald,
                unselectedLabelColor: Colors.grey,
                indicatorColor: AppTheme.primaryEmerald,
                tabs: const [
                  Tab(text: 'বুকিং হিস্টোরি'),
                  Tab(text: 'কমিশন ও পেমেন্ট'),
                ],
              ),
              Expanded(
                child: TabBarView(
                  controller: _tabController,
                  children: [
                    _buildHistoryTab(history),
                    _buildCommissionsTab(commissions),
                  ],
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildHeader(Map<String, dynamic> vendor, Map<String, dynamic> summary) {
    return Container(
      padding: const EdgeInsets.all(20),
      color: Colors.white,
      child: Column(
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 30,
                backgroundColor: AppTheme.primaryEmerald.withAlpha(20),
                child: const Icon(Icons.person, color: AppTheme.primaryEmerald, size: 30),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(vendor['name'], style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 20)),
                    Text('${vendor['type']} | ${vendor['phone'] ?? 'No Phone'}', 
                      style: TextStyle(color: Colors.grey[600], fontSize: 13)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          Row(
            children: [
              _buildSummaryItem('মোট কাজ', '৳${summary['total_service_value']}'),
              const SizedBox(width: 12),
              _buildSummaryItem('নিট আয়', '৳${summary['net_earnings']}', color: AppTheme.primaryEmerald),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              _buildSummaryItem('পেমেন্ট বাকি', '৳${summary['total_payout_pending']}', color: Colors.orange),
              const SizedBox(width: 12),
              _buildSummaryItem('পেমেন্ট হয়েছে', '৳${summary['total_payout_paid']}', color: Colors.blue),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryItem(String label, String value, {Color? color}) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: (color ?? AppTheme.textNavy).withAlpha(10),
          borderRadius: BorderRadius.circular(16),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 4),
            Text(value, style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 16, color: color ?? AppTheme.textNavy)),
          ],
        ),
      ),
    );
  }

  Widget _buildHistoryTab(List history) {
    if (history.isEmpty) return const Center(child: Text('কোন বুকিং রেকর্ড নেই।'));

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: history.length,
      itemBuilder: (context, index) {
        final item = history[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          child: ListTile(
            title: Text(item['customer_name'] ?? 'Guest', style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text('${item['event_date']} | ${item['hall_name']}'),
            trailing: Text('৳${item['total_price']}', style: const TextStyle(fontWeight: FontWeight.w900, color: AppTheme.textNavy)),
          ),
        );
      },
    );
  }

  Widget _buildCommissionsTab(List commissions) {
    if (commissions.isEmpty) return const Center(child: Text('কোন পেমেন্ট রেকর্ড নেই।'));

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: commissions.length,
      itemBuilder: (context, index) {
        final comm = commissions[index];
        final bool isCollected = comm['status'] == 'paid';
        final bool isPaidOut = comm['payout_status'] == 'paid';

        return Card(
          margin: const EdgeInsets.only(bottom: 16),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('বুকিং #${comm['booking_id']}', style: const TextStyle(fontWeight: FontWeight.bold)),
                    _statusBadge(isCollected ? 'সংগৃহীত' : 'বাকি', isCollected ? Colors.green : Colors.orange),
                  ],
                ),
                const Divider(height: 24),
                _rowDetail('কমিশন:', '৳${comm['amount']}'),
                _rowDetail('ভেন্ডর পেমেন্ট:', '৳${comm['net_payout']}'),
                const SizedBox(height: 16),
                Row(
                  children: [
                    if (!isCollected)
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => _collectCommission(comm['id']),
                          child: const Text('কমিশন নিন', style: TextStyle(fontSize: 12)),
                        ),
                      ),
                    if (!isCollected) const SizedBox(width: 8),
                    if (!isPaidOut)
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () => _payVendor(comm['id']),
                          style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryEmerald),
                          child: const Text('পেমেন্ট দিন', style: TextStyle(fontSize: 12, color: Colors.white)),
                        ),
                      ),
                    if (isPaidOut)
                      const Expanded(child: Center(child: Text('পেমেন্ট সম্পন্ন হয়েছে', style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold, fontSize: 12)))),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _rowDetail(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _statusBadge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withAlpha(20), borderRadius: BorderRadius.circular(8)),
      child: Text(text, style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold)),
    );
  }

  void _collectCommission(int id) async {
    final success = await context.read<VendorProvider>().collectCommission(id);
    if (success && mounted) {
      context.read<VendorProvider>().fetchVendorDetails(widget.vendorId);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('কমিশন সংগ্রহ করা হয়েছে।')));
    }
  }

  void _payVendor(int id) async {
    final success = await context.read<VendorProvider>().payVendor(id);
    if (success && mounted) {
      context.read<VendorProvider>().fetchVendorDetails(widget.vendorId);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('ভেন্ডর পেমেন্ট সম্পন্ন হয়েছে।')));
    }
  }

  void _confirmDelete() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('মুছে ফেলুন'),
        content: const Text('আপনি কি নিশ্চিত যে আপনি এই ভেন্ডরটিকে মুছে ফেলতে চান?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('বাতিল')),
          TextButton(
            onPressed: () async {
              final success = await context.read<VendorProvider>().deleteVendor(widget.vendorId);
              if (mounted) {
                Navigator.pop(context); // Close dialog
                if (success) {
                  Navigator.pop(context); // Close details
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('ভেন্ডর মুছে ফেলা হয়েছে।')));
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('মুছে ফেলা সম্ভব নয়। বুকিং রেকর্ড চেক করুন।')));
                }
              }
            },
            child: const Text('নিশ্চিত', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}
