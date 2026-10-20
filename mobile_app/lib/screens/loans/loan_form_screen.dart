import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../api/api_service.dart';
import '../../models/loan.dart';
import '../../theme/app_theme.dart';

class LoanFormScreen extends StatefulWidget {
  final Loan? loan;

  const LoanFormScreen({super.key, this.loan});

  @override
  State<LoanFormScreen> createState() => _LoanFormScreenState();
}

class _LoanFormScreenState extends State<LoanFormScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _amountController;
  late TextEditingController _dateController;
  late TextEditingController _dueDateController;
  late TextEditingController _purposeController;
  late TextEditingController _notesController;

  bool _isSaving = false;

  bool get isEditing => widget.loan != null;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.loan?.personName ?? '');
    _phoneController = TextEditingController(text: widget.loan?.phone ?? '');
    _amountController = TextEditingController(
        text: widget.loan != null ? widget.loan!.amount.toStringAsFixed(0) : '');
    _dateController = TextEditingController(
        text: widget.loan?.date ?? DateFormat('yyyy-MM-dd').format(DateTime.now()));
    _dueDateController = TextEditingController(text: widget.loan?.dueDate ?? '');
    _purposeController = TextEditingController(text: widget.loan?.purpose ?? '');
    _notesController = TextEditingController(text: widget.loan?.notes ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _amountController.dispose();
    _dateController.dispose();
    _dueDateController.dispose();
    _purposeController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _submitForm() async {
    if (!_formKey.currentState!.validate()) return;

    final amount = double.tryParse(_amountController.text.trim()) ?? 0;
    if (isEditing && amount < widget.loan!.paidAmount) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
              'ধারের পরিমাণ ইতিমধ্যে পরিশোধিত ৳ ${widget.loan!.paidAmount} এর চেয়ে কম হতে পারে না'),
        ),
      );
      return;
    }

    setState(() => _isSaving = true);

    try {
      final payload = {
        'person_name': _nameController.text.trim(),
        'phone': _phoneController.text.trim(),
        'amount': amount,
        'date': _dateController.text.trim(),
        'due_date': _dueDateController.text.trim().isNotEmpty
            ? _dueDateController.text.trim()
            : null,
        'purpose': _purposeController.text.trim().isNotEmpty
            ? _purposeController.text.trim()
            : null,
        'notes': _notesController.text.trim().isNotEmpty
            ? _notesController.text.trim()
            : null,
      };

      if (isEditing) {
        final res = await ApiService.instance.put('loans/${widget.loan!.id}', data: payload);
        if (res.statusCode == 200 || res.statusCode == 201) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('ধারের তথ্য সফলভাবে আপডেট হয়েছে')),
            );
            Navigator.pop(context, true);
          }
        }
      } else {
        final res = await ApiService.instance.post('loans', data: payload);
        if (res.statusCode == 200 || res.statusCode == 201) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('টাকা ধার গ্রহণের তথ্য সফলভাবে সংরক্ষণ করা হয়েছে')),
            );
            Navigator.pop(context, true);
          }
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('সংরক্ষণ ব্যর্থ হয়েছে: $e')),
        );
      }
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'ধারের তথ্য সম্পাদন' : 'টাকা ধার গ্রহণ এন্ট্রি'),
        backgroundColor: Colors.white,
        elevation: 0.5,
        foregroundColor: AppTheme.textNavy,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextFormField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: 'ঋণদাতা / ব্যক্তির নাম *',
                  hintText: 'যেমন: মো: রহিম উদ্দিন',
                  prefixIcon: const Icon(Icons.person_rounded),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'ব্যক্তির নাম লিখুন';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 14),

              TextFormField(
                controller: _phoneController,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                  labelText: 'ফোন নম্বর (ঐচ্ছিক)',
                  hintText: 'যেমন: 01712345678',
                  prefixIcon: const Icon(Icons.phone_rounded),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
              ),
              const SizedBox(height: 14),

              TextFormField(
                controller: _amountController,
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: InputDecoration(
                  labelText: 'ধারের পরিমাণ (টাকা) *',
                  hintText: '0.00',
                  prefixText: '৳ ',
                  prefixIcon: const Icon(Icons.account_balance_wallet_rounded),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'টাকার পরিমাণ লিখুন';
                  }
                  final parsed = double.tryParse(val.trim());
                  if (parsed == null || parsed <= 0) {
                    return 'সঠিক টাকার পরিমাণ লিখুন';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 14),

              TextFormField(
                controller: _dateController,
                readOnly: true,
                decoration: InputDecoration(
                  labelText: 'টাকা গ্রহণের তারিখ *',
                  prefixIcon: const Icon(Icons.calendar_today_rounded),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
                onTap: () async {
                  final picked = await showDatePicker(
                    context: context,
                    initialDate: DateTime.tryParse(_dateController.text) ?? DateTime.now(),
                    firstDate: DateTime(2020),
                    lastDate: DateTime(2035),
                  );
                  if (picked != null) {
                    _dateController.text = DateFormat('yyyy-MM-dd').format(picked);
                  }
                },
              ),
              const SizedBox(height: 14),

              TextFormField(
                controller: _dueDateController,
                readOnly: true,
                decoration: InputDecoration(
                  labelText: 'ফেরত দেওয়ার সম্ভাব্য তারিখ (ঐচ্ছিক)',
                  hintText: 'কোনো নির্দিষ্ট মেয়াদের প্রতিশ্রুতি থাকলে নির্বাচন করুন',
                  prefixIcon: const Icon(Icons.event_available_rounded),
                  suffixIcon: _dueDateController.text.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear, size: 18),
                          onPressed: () => setState(() => _dueDateController.clear()),
                        )
                      : null,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
                onTap: () async {
                  final picked = await showDatePicker(
                    context: context,
                    initialDate: DateTime.tryParse(_dueDateController.text) ?? DateTime.now(),
                    firstDate: DateTime(2020),
                    lastDate: DateTime(2035),
                  );
                  if (picked != null) {
                    setState(() {
                      _dueDateController.text = DateFormat('yyyy-MM-dd').format(picked);
                    });
                  }
                },
              ),
              const SizedBox(height: 14),

              TextFormField(
                controller: _purposeController,
                decoration: InputDecoration(
                  labelText: 'টাকা গ্রহণের উদ্দেশ্য / কারণ (ঐচ্ছিক)',
                  hintText: 'যেমন: এসি মেরামত, জরুরি তহবিল, লাইটিং ক্রয়',
                  prefixIcon: const Icon(Icons.info_outline_rounded),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
              ),
              const SizedBox(height: 14),

              TextFormField(
                controller: _notesController,
                maxLines: 3,
                decoration: InputDecoration(
                  labelText: 'অতিরিক্ত নোট / বিবরণ (ঐচ্ছিক)',
                  hintText: 'কোনো বিশেষ শর্ত বা বিবরণ থাকলে লিখুন...',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                ),
              ),
              const SizedBox(height: 24),

              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primaryTeal,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: _isSaving ? null : _submitForm,
                  icon: _isSaving
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                        )
                      : const Icon(Icons.check_circle_outline_rounded),
                  label: Text(
                    _isSaving ? 'সংরক্ষণ হচ্ছে...' : (isEditing ? 'আপডেট সম্পন্ন করুন' : 'ধার সংরক্ষণ করুন'),
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
