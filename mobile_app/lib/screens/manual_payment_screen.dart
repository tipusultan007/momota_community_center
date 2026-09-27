import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class ManualPaymentScreen extends StatefulWidget {
  final Map plan;
  final List gateways;

  const ManualPaymentScreen({super.key, required this.plan, required this.gateways});

  @override
  State<ManualPaymentScreen> createState() => _ManualPaymentScreenState();
}

class _ManualPaymentScreenState extends State<ManualPaymentScreen> {
  final _senderNumberController = TextEditingController();
  final _transactionIdController = TextEditingController();
  String? _selectedGateway;
  bool _isLoading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: const Text('পেমেন্ট ডিটেইলস', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildPlanSummary(),
            const SizedBox(height: 32),
            Text(
              'পেমেন্ট মেথড নির্বাচন করুন',
              style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 18),
            ),
            const SizedBox(height: 16),
            ...widget.gateways.map((g) => _buildGatewayCard(g)),
            const SizedBox(height: 32),
            if (_selectedGateway != null) _buildPaymentForm(),
          ],
        ),
      ),
    );
  }

  Widget _buildPlanSummary() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(widget.plan['name'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const Text('সাবস্ক্রিপশন প্ল্যান', style: TextStyle(color: Colors.grey, fontSize: 12)),
            ],
          ),
          Text(
            '৳${widget.plan['price']}',
            style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppTheme.primaryEmerald),
          ),
        ],
      ),
    );
  }

  Widget _buildGatewayCard(Map g) {
    final isSelected = _selectedGateway == g['name'];
    return GestureDetector(
      onTap: () => setState(() => _selectedGateway = g['name']),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: isSelected ? AppTheme.primaryEmerald.withAlpha(20) : Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: isSelected ? AppTheme.primaryEmerald : Colors.grey.shade200),
        ),
        child: Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(color: Colors.grey.shade100, shape: BoxShape.circle),
              child: const Icon(Icons.payment, color: Colors.grey),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(g['name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                  Text(g['account_number'] ?? 'N/A', style: const TextStyle(color: Colors.grey, fontSize: 12)),
                  Text((g['account_type'] ?? 'N/A').toString().toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: AppTheme.primaryEmerald)),
                ],
              ),
            ),
            if (isSelected) const Icon(Icons.check_circle, color: AppTheme.primaryEmerald),
          ],
        ),
      ),
    );
  }

  Widget _buildPaymentForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'আপনার পেমেন্ট তথ্য দিন',
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 18),
        ),
        const SizedBox(height: 16),
        _buildTextField('যে নম্বর থেকে পেমেন্ট করেছেন', _senderNumberController, hint: '01XXXXXXXXX'),
        const SizedBox(height: 20),
        _buildTextField('ট্রানজ্যাকশন আইডি (Transaction ID)', _transactionIdController, hint: 'X7K8L2...'),
        const SizedBox(height: 32),
        SizedBox(
          width: double.infinity,
          height: 56,
          child: ElevatedButton(
            onPressed: _isLoading ? null : _submit,
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.textNavy, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))),
            child: _isLoading ? const CircularProgressIndicator(color: Colors.white) : const Text('নিশ্চিত করুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ),
      ],
    );
  }

  Widget _buildTextField(String label, TextEditingController controller, {String? hint}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),
        TextField(
          controller: controller,
          decoration: InputDecoration(
            hintText: hint,
            filled: true,
            fillColor: Colors.white,
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
          ),
        ),
      ],
    );
  }

  Future<void> _submit() async {
    if (_senderNumberController.text.isEmpty || _transactionIdController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('সবগুলো তথ্য পূরণ করুন।')));
      return;
    }

    setState(() => _isLoading = true);

    final success = await context.read<AuthProvider>().checkoutSubscription({
      'plan': widget.plan['id'],
      'amount': widget.plan['price'],
      'payment_method': _selectedGateway,
      'sender_number': _senderNumberController.text,
      'transaction_id': _transactionIdController.text,
    });

    setState(() => _isLoading = false);

    if (success && mounted) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('অ্যাডমিন কনফার্ম করার পর সাবস্ক্রিপশন অ্যাক্টিভ হবে।')),
      );
    }
  }
}
