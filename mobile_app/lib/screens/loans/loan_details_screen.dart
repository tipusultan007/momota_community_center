import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../api/api_service.dart';
import '../../models/loan.dart';
import '../../models/loan_payment.dart';
import '../../theme/app_theme.dart';
import 'loan_form_screen.dart';

class LoanDetailsScreen extends StatefulWidget {
  final int loanId;

  const LoanDetailsScreen({super.key, required this.loanId});

  @override
  State<LoanDetailsScreen> createState() => _LoanDetailsScreenState();
}

class _LoanDetailsScreenState extends State<LoanDetailsScreen> {
  bool _isLoading = true;
  Loan? _loan;
  bool _hasModified = false;

  final currencyFormat = NumberFormat('#,##,###.##', 'en_US');

  @override
  void initState() {
    super.initState();
    _fetchDetails();
  }

  Future<void> _fetchDetails() async {
    setState(() => _isLoading = true);
    try {
      final response = await ApiService.instance.get('loans/${widget.loanId}');
      if (response.statusCode == 200 && response.data != null) {
        final data = response.data['data'];
        setState(() {
          _loan = Loan.fromJson(data);
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showPaymentModal() {
    if (_loan == null) return;
    final amountController = TextEditingController(text: _loan!.dueAmount.toStringAsFixed(0));
    final dateController = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
    final notesController = TextEditingController();
    String selectedMethod = 'Cash';
    bool isSubmitting = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Container(
              padding: EdgeInsets.only(
                top: 20,
                left: 20,
                right: 20,
                bottom: MediaQuery.of(context).viewInsets.bottom + 20,
              ),
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Center(
                      child: Container(
                        width: 40,
                        height: 4,
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade300,
                          borderRadius: BorderRadius.circular(4),
                        ),
                      ),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'টাকা ফেরত প্রদান (পরিশোধ)',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textNavy),
                        ),
                        IconButton(
                          icon: const Icon(Icons.close, color: Colors.grey),
                          onPressed: () => Navigator.pop(ctx),
                        ),
                      ],
                    ),
                    Text(
                      'বর্তমান বকেয়া: ৳ ${currencyFormat.format(_loan!.dueAmount)}',
                      style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.red),
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: amountController,
                      keyboardType: const TextInputType.numberWithOptions(decimal: true),
                      decoration: InputDecoration(
                        labelText: 'পরিশোধের পরিমাণ (টাকা) *',
                        prefixText: '৳ ',
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        filled: true,
                        fillColor: Colors.grey.shade50,
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: dateController,
                      readOnly: true,
                      decoration: InputDecoration(
                        labelText: 'পরিশোধের তারিখ *',
                        suffixIcon: const Icon(Icons.calendar_today_rounded, size: 20),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        filled: true,
                        fillColor: Colors.grey.shade50,
                      ),
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: DateTime.now(),
                          firstDate: DateTime(2020),
                          lastDate: DateTime(2035),
                        );
                        if (picked != null) {
                          setModalState(() {
                            dateController.text = DateFormat('yyyy-MM-dd').format(picked);
                          });
                        }
                      },
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<String>(
                      value: selectedMethod,
                      decoration: InputDecoration(
                        labelText: 'পরিশোধ মাধ্যম *',
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        filled: true,
                        fillColor: Colors.grey.shade50,
                      ),
                      items: const [
                        DropdownMenuItem(value: 'Cash', child: Text('ক্যাশ (Cash)')),
                        DropdownMenuItem(value: 'bKash', child: Text('বিকাশ (bKash)')),
                        DropdownMenuItem(value: 'Nagad', child: Text('নগদ (Nagad)')),
                        DropdownMenuItem(value: 'Bank Transfer', child: Text('ব্যাংক ট্রান্সফার')),
                        DropdownMenuItem(value: 'Cheque', child: Text('চেক (Cheque)')),
                      ],
                      onChanged: (val) {
                        if (val != null) setModalState(() => selectedMethod = val);
                      },
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: notesController,
                      decoration: InputDecoration(
                        labelText: 'নোট / মন্তব্য (ঐচ্ছিক)',
                        hintText: 'যেমন: কিস্তি ১ বা চেক নম্বর',
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        filled: true,
                        fillColor: Colors.grey.shade50,
                      ),
                    ),
                    const SizedBox(height: 20),
                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: ElevatedButton.icon(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.teal.shade700,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        onPressed: isSubmitting
                            ? null
                            : () async {
                                final payAmount = double.tryParse(amountController.text.trim()) ?? 0;
                                if (payAmount <= 0) {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(content: Text('সঠিক পরিমাণ লিখুন')),
                                  );
                                  return;
                                }
                                if (payAmount > _loan!.dueAmount) {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(content: Text('পরিশোধের পরিমাণ ৳ ${currencyFormat.format(_loan!.dueAmount)} এর বেশি হতে পারে না')),
                                  );
                                  return;
                                }

                                setModalState(() => isSubmitting = true);
                                try {
                                  final res = await ApiService.instance.post('loans/${_loan!.id}/payments', data: {
                                    'amount': payAmount,
                                    'payment_date': dateController.text,
                                    'payment_method': selectedMethod,
                                    'notes': notesController.text.trim(),
                                  });

                                  if (context.mounted) {
                                    Navigator.pop(ctx);
                                    if (res.statusCode == 200 || res.statusCode == 201) {
                                      _hasModified = true;
                                      ScaffoldMessenger.of(this.context).showSnackBar(
                                        const SnackBar(content: Text('পরিশোধ সফলভাবে সম্পন্ন হয়েছে')),
                                      );
                                      _fetchDetails();
                                    }
                                  }
                                } catch (e) {
                                  setModalState(() => isSubmitting = false);
                                  if (context.mounted) {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(content: Text('ত্রুটি: $e')),
                                    );
                                  }
                                }
                              },
                        icon: isSubmitting
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                            : const Icon(Icons.check_circle_outline_rounded),
                        label: Text(isSubmitting ? 'প্রক্রিয়াধীন...' : 'পরিশোধ নিশ্চিত করুন'),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  Future<void> _deletePayment(LoanPayment payment) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('পেমেন্ট মুছে ফেলবেন?'),
        content: Text('৳ ${currencyFormat.format(payment.amount)} টাকার এই কিস্তি এন্ট্রিটি মুছে ফেললে বকেয়া দেনা পুনরায় বৃদ্ধি পাবে।'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('বাতিল')),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('মুছে ফেলুন'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        await ApiService.instance.delete('loans/payments/${payment.id}');
        _hasModified = true;
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('পেমেন্ট এন্ট্রি মুছে ফেলা হয়েছে')),
          );
          _fetchDetails();
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('ত্রুটি: $e')),
          );
        }
      }
    }
  }

  Future<void> _deleteLoan() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('ঋণের সম্পূর্ণ রেকর্ড মুছবেন?'),
        content: const Text('আপনি কি নিশ্চিত? এই ধারের সকল তথ্য ও পরিশোধের ইতিহাস স্থায়ীভাবে মুছে যাবে।'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('বাতিল')),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('মুছে ফেলুন'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        await ApiService.instance.delete('loans/${_loan!.id}');
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('ধারের রেকর্ড মুছে ফেলা হয়েছে')),
          );
          Navigator.pop(context, true);
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('ত্রুটি: $e')),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if (_loan == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('ধারের বিবরণ')),
        body: const Center(child: Text('রেকর্ড পাওয়া যায়নি')),
      );
    }

    final loan = _loan!;
    final isPaid = loan.status == 'paid';
    final isPartial = loan.status == 'partial';
    final percent = loan.amount > 0 ? (loan.paidAmount / loan.amount).clamp(0.0, 1.0) : 1.0;

    return PopScope(
      canPop: true,
      onPopInvokedWithResult: (didPop, _) {
        if (_hasModified && !didPop) {
          Navigator.pop(context, true);
        }
      },
      child: Scaffold(
        appBar: AppBar(
          title: Text(loan.personName),
          backgroundColor: Colors.white,
          elevation: 0.5,
          foregroundColor: AppTheme.textNavy,
          actions: [
            IconButton(
              icon: const Icon(Icons.edit_outlined),
              tooltip: 'এডিট',
              onPressed: () async {
                final updated = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => LoanFormScreen(loan: loan)),
                );
                if (updated == true) {
                  _hasModified = true;
                  _fetchDetails();
                }
              },
            ),
            IconButton(
              icon: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent),
              tooltip: 'মুছুন',
              onPressed: _deleteLoan,
            ),
          ],
        ),
        body: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Top Status & Person Header
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4)),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CircleAvatar(
                          radius: 24,
                          backgroundColor: AppTheme.primaryTeal.withOpacity(0.1),
                          child: Text(
                            loan.personName.isNotEmpty ? loan.personName.substring(0, 1) : '?',
                            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.primaryTeal),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                loan.personName,
                                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textNavy),
                              ),
                              if (loan.phone != null && loan.phone!.isNotEmpty)
                                Row(
                                  children: [
                                    Icon(Icons.phone_rounded, size: 14, color: Colors.grey.shade600),
                                    const SizedBox(width: 4),
                                    Text(loan.phone!, style: TextStyle(fontSize: 13, color: Colors.grey.shade700)),
                                    const SizedBox(width: 8),
                                    IconButton(
                                      icon: const Icon(Icons.call_rounded, size: 18, color: Colors.teal),
                                      visualDensity: VisualDensity.compact,
                                      padding: EdgeInsets.zero,
                                      constraints: const BoxConstraints(),
                                      onPressed: () {
                                        final uri = Uri.parse('tel:${loan.phone!}');
                                        launchUrl(uri);
                                      },
                                    ),
                                  ],
                                ),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                          decoration: BoxDecoration(
                            color: isPaid
                                ? Colors.green.shade50
                                : (isPartial ? Colors.orange.shade50 : Colors.red.shade50),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            isPaid ? 'পরিশোধিত' : (isPartial ? 'আংশিক পরিশোধ' : 'অপরিশোধিত'),
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: isPaid
                                  ? Colors.green.shade800
                                  : (isPartial ? Colors.orange.shade800 : Colors.red.shade800),
                            ),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 16),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('পরিশোধের অগ্রগতি', style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
                        Text('${(percent * 100).toStringAsFixed(0)}%',
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.teal)),
                      ],
                    ),
                    const SizedBox(height: 6),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(6),
                      child: LinearProgressIndicator(
                        value: percent,
                        minHeight: 8,
                        backgroundColor: Colors.grey.shade200,
                        valueColor: AlwaysStoppedAnimation<Color>(
                          isPaid ? Colors.teal : (isPartial ? Colors.orange : Colors.red),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 14),

              // 3 Financial Tiles
              Row(
                children: [
                  Expanded(
                    child: _buildTile(
                      label: 'মোট ধার',
                      amount: '৳ ${currencyFormat.format(loan.amount)}',
                      color: Colors.blue.shade700,
                      subtext: loan.date,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: _buildTile(
                      label: 'পরিশোধিত',
                      amount: '৳ ${currencyFormat.format(loan.paidAmount)}',
                      color: Colors.teal.shade700,
                      subtext: '${loan.payments.length} কিস্তি',
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: _buildTile(
                      label: 'অবশিষ্ট দেনা',
                      amount: '৳ ${currencyFormat.format(loan.dueAmount)}',
                      color: loan.dueAmount > 0 ? Colors.red.shade700 : Colors.grey.shade600,
                      subtext: loan.dueDate ?? 'মেয়াদহীন',
                    ),
                  ),
                ],
              ),

              if (loan.purpose != null && loan.purpose!.isNotEmpty) ...[
                const SizedBox(height: 14),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.info_outline_rounded, size: 18, color: Colors.blueGrey),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'উদ্দেশ্য: ${loan.purpose}',
                          style: const TextStyle(fontSize: 13, color: AppTheme.textNavy),
                        ),
                      ),
                    ],
                  ),
                ),
              ],

              if (loan.notes != null && loan.notes!.isNotEmpty) ...[
                const SizedBox(height: 8),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    'নোট: ${loan.notes}',
                    style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                  ),
                ),
              ],

              const SizedBox(height: 20),

              // Repayments Header
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'পরিশোধের ইতিহাস (কিস্তি সমূহ)',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textNavy),
                  ),
                  if (loan.dueAmount > 0)
                    TextButton.icon(
                      style: TextButton.styleFrom(foregroundColor: Colors.teal.shade700),
                      icon: const Icon(Icons.add_circle_outline, size: 18),
                      label: const Text('টাকা ফেরত দিন'),
                      onPressed: _showPaymentModal,
                    ),
                ],
              ),

              const SizedBox(height: 8),

              // Repayments List
              if (loan.payments.isEmpty)
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Center(
                    child: Column(
                      children: [
                        Icon(Icons.history_rounded, size: 40, color: Colors.grey.shade300),
                        const SizedBox(height: 8),
                        Text(
                          'এখনো কোনো টাকা ফেরত প্রদান করা হয়নি।',
                          style: TextStyle(color: Colors.grey.shade500, fontSize: 13),
                        ),
                      ],
                    ),
                  ),
                )
              else
                ...loan.payments.map((p) => _buildPaymentRow(p)),
            ],
          ),
        ),
        bottomNavigationBar: loan.dueAmount > 0
            ? Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  boxShadow: [
                    BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -4)),
                  ],
                ),
                child: SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.teal.shade700,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    onPressed: _showPaymentModal,
                    icon: const Icon(Icons.payment_rounded),
                    label: Text(
                      'টাকা ফেরত দিন (বাকি: ৳ ${currencyFormat.format(loan.dueAmount)})',
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                    ),
                  ),
                ),
              )
            : null,
      ),
    );
  }

  Widget _buildTile({
    required String label,
    required String amount,
    required Color color,
    required String subtext,
  }) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 6),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: TextStyle(fontSize: 11, color: Colors.grey.shade600)),
          const SizedBox(height: 4),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              amount,
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: color),
            ),
          ),
          const SizedBox(height: 2),
          Text(subtext, style: TextStyle(fontSize: 10, color: Colors.grey.shade500), maxLines: 1),
        ],
      ),
    );
  }

  Widget _buildPaymentRow(LoanPayment payment) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 16,
            backgroundColor: Colors.teal.shade50,
            child: Icon(Icons.arrow_downward_rounded, size: 16, color: Colors.teal.shade700),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '৳ ${currencyFormat.format(payment.amount)}',
                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Colors.teal),
                ),
                Text(
                  '${payment.paymentDate} • ${payment.paymentMethod}',
                  style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                ),
                if (payment.notes != null && payment.notes!.isNotEmpty)
                  Text(
                    payment.notes!,
                    style: TextStyle(fontSize: 11, color: Colors.grey.shade500, fontStyle: FontStyle.italic),
                  ),
              ],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, size: 18, color: Colors.redAccent),
            visualDensity: VisualDensity.compact,
            tooltip: 'মুছুন',
            onPressed: () => _deletePayment(payment),
          ),
        ],
      ),
    );
  }
}
