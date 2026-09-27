import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'manual_payment_screen.dart';
import 'subscription_history_screen.dart';

class SubscriptionPlansScreen extends StatefulWidget {
  const SubscriptionPlansScreen({super.key});

  @override
  State<SubscriptionPlansScreen> createState() => _SubscriptionPlansScreenState();
}

class _SubscriptionPlansScreenState extends State<SubscriptionPlansScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AuthProvider>().fetchSubscription();
    });
  }

  @override
  Widget build(BuildContext context) {
    final subscription = context.watch<AuthProvider>().subscription;
    final plans = subscription?['plans'] as List? ?? [];

    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: const Text('সাবস্ক্রিপশন প্ল্যান', style: TextStyle(fontWeight: FontWeight.bold)),
        actions: [
          IconButton(
            icon: const Icon(Icons.history),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const SubscriptionHistoryScreen())),
          ),
        ],
      ),
      body: subscription == null
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildCurrentStatusCard(context),
                  const SizedBox(height: 32),
                  Text(
                    'আপনার জন্য সেরা প্ল্যানটি বেছে নিন',
                    style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 18),
                  ),
                  const SizedBox(height: 16),
                  ...plans.map((p) => _buildPlanCard(context, p, subscription['gateways'] as List? ?? [])),
                ],
              ),
            ),
    );
  }

  Widget _buildCurrentStatusCard(BuildContext context) {
    final auth = context.read<AuthProvider>();
    final sub = auth.subscription!['current_subscription'];
    final isActive = sub['is_active'];

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.textNavy,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: AppTheme.textNavy.withAlpha(50), blurRadius: 20, offset: const Offset(0, 8))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('বর্তমান স্ট্যাটাস', style: TextStyle(color: Colors.white70, fontSize: 14)),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: isActive ? AppTheme.primaryEmerald : Colors.redAccent,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  isActive ? 'সক্রিয়' : 'নিষ্ক্রিয়',
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Text(
            sub['plan'].toString().toUpperCase(),
            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 24, letterSpacing: 1),
          ),
          const SizedBox(height: 8),
          if (sub['subscription_ends_at'] != null)
            Text(
              'মেয়াদ শেষ হবে: ${DateTime.parse(sub['subscription_ends_at']).toLocal().toString().split(' ')[0]}',
              style: const TextStyle(color: Colors.white60, fontSize: 13),
            )
          else if (sub['trial_ends_at'] != null)
            Text(
              'ট্রায়াল শেষ হবে: ${DateTime.parse(sub['trial_ends_at']).toLocal().toString().split(' ')[0]}',
              style: const TextStyle(color: Colors.white60, fontSize: 13),
            ),
        ],
      ),
    );
  }

  Widget _buildPlanCard(BuildContext context, Map plan, List gateways) {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            plan['name'],
            style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 20, color: AppTheme.textNavy),
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              const Text('৳', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 24, color: AppTheme.primaryEmerald)),
              Text(
                plan['price'].toString(),
                style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 32, color: AppTheme.textNavy),
              ),
              const Text(' / মাস', style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider()),
          ... (plan['features'] as List).map((f) => Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Row(
              children: [
                const Icon(Icons.check_circle, color: AppTheme.primaryEmerald, size: 18),
                const SizedBox(width: 12),
                Text(f, style: const TextStyle(color: Colors.black87, fontWeight: FontWeight.w500)),
              ],
            ),
          )),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            height: 52,
            child: ElevatedButton(
              onPressed: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => ManualPaymentScreen(plan: plan, gateways: gateways)));
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.textNavy,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              child: const Text('এই প্ল্যানটি কিনুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }
}
