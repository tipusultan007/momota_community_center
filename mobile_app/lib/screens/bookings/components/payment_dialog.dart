import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../providers/booking_provider.dart';
import '../../../providers/dashboard_provider.dart';
import '../../../providers/accounting_provider.dart';
import '../../../theme/app_theme.dart';

class PaymentDialog extends StatefulWidget {
  final int bookingId;
  final double dueAmount;

  const PaymentDialog({super.key, required this.bookingId, required this.dueAmount});

  @override
  State<PaymentDialog> createState() => _PaymentDialogState();
}

class _PaymentDialogState extends State<PaymentDialog> {
  final _amountController = TextEditingController();
  final _descController = TextEditingController();
  DateTime _selectedDate = DateTime.now();
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _amountController.text = widget.dueAmount.toString();
    _descController.text = 'বুকিং পেমেন্ট';
  }

  Future<void> _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
    );
    if (picked != null) {
      setState(() => _selectedDate = picked);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
      title: Text('পেমেন্ট গ্রহণ করুন', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('টাকার পরিমাণ', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey)),
            const SizedBox(height: 8),
            TextField(
              controller: _amountController,
              decoration: const InputDecoration(
                hintText: 'পরিমাণ লিখুন',
                prefixText: '৳ ',
              ),
              keyboardType: TextInputType.number,
            ),
            const SizedBox(height: 16),
            const Text('পেমেন্টের তারিখ', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey)),
            const SizedBox(height: 8),
            InkWell(
              onTap: _selectDate,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                decoration: BoxDecoration(
                  border: Border.all(color: Colors.grey.shade300),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.calendar_today, size: 18, color: AppTheme.primaryEmerald),
                    const SizedBox(width: 12),
                    Text('${_selectedDate.day}/${_selectedDate.month}/${_selectedDate.year}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            const Text('বিবরণ', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey)),
            const SizedBox(height: 8),
            TextField(
              controller: _descController,
              decoration: const InputDecoration(
                hintText: 'বিবরণ লিখুন (ঐচ্ছিক)',
              ),
              maxLines: 2,
            ),
          ],
        ),
      ),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context), child: const Text('বাতিল', style: TextStyle(color: Colors.grey))),
        ElevatedButton(
          onPressed: _isSubmitting ? null : () async {
            final amount = double.tryParse(_amountController.text) ?? 0;
            if (amount <= 0) return;

            setState(() => _isSubmitting = true);
            final dateStr = _selectedDate.toIso8601String().split('T')[0];
            final success = await context.read<BookingProvider>().addPayment(
              widget.bookingId, amount, dateStr, _descController.text.trim()
            );
            
            if (mounted) {
              setState(() => _isSubmitting = false);
              if (success) {
                context.read<DashboardProvider>().fetchDashboard();
                context.read<AccountingProvider>().fetchTransactions();
                Navigator.pop(context, true);
              }
            }
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: AppTheme.primaryEmerald,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
          child: _isSubmitting 
            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
            : const Text('জমা দিন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        ),
      ],
    );
  }
}
