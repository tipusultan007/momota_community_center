import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class SubscriptionHistoryScreen extends StatefulWidget {
  const SubscriptionHistoryScreen({super.key});

  @override
  State<SubscriptionHistoryScreen> createState() => _SubscriptionHistoryScreenState();
}

class _SubscriptionHistoryScreenState extends State<SubscriptionHistoryScreen> {
  late Future<List> _historyFuture;

  @override
  void initState() {
    super.initState();
    _historyFuture = context.read<AuthProvider>().fetchSubscriptionHistory();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: const Text('পেমেন্ট হিস্ট্রি', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: FutureBuilder<List>(
        future: _historyFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.history_toggle_off, size: 64, color: Colors.grey.shade300),
                  const SizedBox(height: 16),
                  Text('কোন পেমেন্ট হিস্ট্রি পাওয়া যায়নি', style: TextStyle(color: Colors.grey.shade500)),
                ],
              ),
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.all(20),
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
              final h = snapshot.data![index];
              final status = h['status'].toString().toLowerCase();
              final date = DateTime.parse(h['created_at']).toLocal();

              return Container(
                margin: const EdgeInsets.only(bottom: 16),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.grey.shade100),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          h['plan'].toString().toUpperCase(),
                          style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textNavy),
                        ),
                        _buildStatusBadge(status),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          '৳${h['amount']}',
                          style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppTheme.primaryEmerald),
                        ),
                        Text(
                          DateFormat('dd MMM yyyy').format(date),
                          style: const TextStyle(color: Colors.grey, fontSize: 12),
                        ),
                      ],
                    ),
                    const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider(height: 1)),
                    _buildInfoRow('পেমেন্ট মেথড', h['payment_method']),
                    const SizedBox(height: 8),
                    _buildInfoRow('নম্বর', h['sender_number']),
                    const SizedBox(height: 8),
                    _buildInfoRow('ট্রানজ্যাকশন আইডি', h['transaction_id']),
                  ],
                ),
              );
            },
          );
        },
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color = Colors.orange;
    String label = 'অপেক্ষমান';

    if (status == 'approved' || status == 'active') {
      color = AppTheme.primaryEmerald;
      label = 'অনুমোদিত';
    } else if (status == 'rejected') {
      color = Colors.redAccent;
      label = 'প্রত্যাখ্যাত';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withAlpha(20),
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: color.withAlpha(50)),
      ),
      child: Text(
        label,
        style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold),
      ),
    );
  }

  Widget _buildInfoRow(String label, dynamic value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(color: Colors.grey, fontSize: 12)),
        Text(value?.toString() ?? 'N/A', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
      ],
    );
  }
}
