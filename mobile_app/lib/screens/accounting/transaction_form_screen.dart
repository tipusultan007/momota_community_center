import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/accounting_provider.dart';
import '../../providers/hall_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../models/transaction.dart';
import '../../api/api_service.dart';
import '../../theme/app_theme.dart';

class TransactionFormScreen extends StatefulWidget {
  final Transaction? transaction;
  final String initialType; // 'income' or 'expense'

  const TransactionFormScreen({super.key, this.transaction, this.initialType = 'income'});

  @override
  State<TransactionFormScreen> createState() => _TransactionFormScreenState();
}

class _TransactionFormScreenState extends State<TransactionFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late String _type;
  final _amountController = TextEditingController();
  final _descriptionController = TextEditingController();
  DateTime _selectedDate = DateTime.now();
  int? _categoryId;
  int? _hallId;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _type = widget.transaction?.type ?? widget.initialType;
    if (widget.transaction != null) {
      _amountController.text = widget.transaction!.amount.toString();
      _descriptionController.text = widget.transaction!.description ?? '';
      _selectedDate = DateTime.parse(widget.transaction!.date);
      _categoryId = widget.transaction!.categoryId;
      _hallId = widget.transaction!.hallId;
    } else {
      _hallId = context.read<HallProvider>().activeHall['id'];
    }
    
    Future.microtask(() => context.read<AccountingProvider>().fetchCategories());
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate() || _categoryId == null) {
      if (_categoryId == null) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('অনুগ্রহ করে একটি ক্যাটাগরি নির্বাচন করুন।')));
      }
      return;
    }

    setState(() => _isSaving = true);

    final data = {
      'amount': double.parse(_amountController.text),
      'date': _selectedDate.toIso8601String().split('T')[0],
      'description': _descriptionController.text,
      'hall_id': _hallId,
      '${_type}_category_id': _categoryId,
    };

    bool success;
    if (widget.transaction == null) {
      success = await context.read<AccountingProvider>().storeTransaction(_type, data);
    } else {
      success = await context.read<AccountingProvider>().updateTransaction(_type, widget.transaction!.id, data);
    }

    if (success && mounted) {
      context.read<DashboardProvider>().fetchDashboard();
      Navigator.pop(context);
      final isOffline = !ApiService.instance.isOnline.value;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: isOffline ? Colors.orange.shade800 : null,
        content: Text(isOffline
            ? '${_type == 'income' ? 'আয়' : 'ব্যয়'} অফলাইনে সংরক্ষণ করা হয়েছে। ইন্টারনেট পেলে সিঙ্ক হবে।'
            : '${_type == 'income' ? 'আয়' : 'ব্যয়'} সফলভাবে সংরক্ষণ করা হয়েছে।'),
      ));
    } else if (mounted) {
      setState(() => _isSaving = false);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('সংরক্ষণ করতে সমস্যা হয়েছে।')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(widget.transaction == null ? 'নতুন লেনদেন' : 'লেনদেন এডিট', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildTypeSelector(),
              const SizedBox(height: 24),
              _buildFormCard(),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: _isSaving ? null : _save,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _type == 'income' ? AppTheme.primaryEmerald : Colors.red,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: _isSaving 
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('সংরক্ষণ করুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTypeSelector() {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16)),
      child: Row(
        children: [
          Expanded(
            child: _typeTab('আয়', 'income', AppTheme.primaryEmerald),
          ),
          Expanded(
            child: _typeTab('ব্যয়', 'expense', Colors.red),
          ),
        ],
      ),
    );
  }

  Widget _typeTab(String label, String type, Color color) {
    final isSelected = _type == type;
    return GestureDetector(
      onTap: () {
        if (widget.transaction == null) {
          setState(() {
            _type = type;
            _categoryId = null; // Reset category when switching type
          });
        }
      },
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? color : Colors.transparent,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Center(
          child: Text(label, style: TextStyle(
            color: isSelected ? Colors.white : Colors.grey,
            fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
          )),
        ),
      ),
    );
  }

  Widget _buildFormCard() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          TextFormField(
            controller: _amountController,
            keyboardType: TextInputType.number,
            style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppTheme.textNavy),
            decoration: const InputDecoration(
              labelText: 'পরিমাণ (৳)',
              prefixIcon: Icon(Icons.account_balance_wallet_outlined),
              border: InputBorder.none,
              contentPadding: EdgeInsets.zero,
            ),
            validator: (v) => v!.isEmpty ? 'পরিমাণ লিখুন' : null,
          ),
          const Divider(height: 32),
          Consumer<AccountingProvider>(
            builder: (context, provider, _) {
              final categories = _type == 'income' ? provider.incomeCategories : provider.expenseCategories;
              return DropdownButtonFormField<int>(
                value: _categoryId != null && categories.any((c) => c.id == _categoryId) ? _categoryId : null,
                decoration: const InputDecoration(labelText: 'ক্যাটাগরি', prefixIcon: Icon(Icons.category_outlined)),
                items: categories.map((c) => DropdownMenuItem(value: c.id, child: Text(c.name))).toList(),
                onChanged: (val) => setState(() => _categoryId = val),
              );
            },
          ),
          const SizedBox(height: 16),
          InkWell(
            onTap: () async {
              final picked = await showDatePicker(
                context: context,
                initialDate: _selectedDate,
                firstDate: DateTime(2020),
                lastDate: DateTime(2030),
              );
              if (picked != null) setState(() => _selectedDate = picked);
            },
            child: InputDecorator(
              decoration: const InputDecoration(labelText: 'তারিখ', prefixIcon: Icon(Icons.calendar_today_outlined)),
              child: Text('${_selectedDate.day}/${_selectedDate.month}/${_selectedDate.year}', style: const TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _descriptionController,
            maxLines: 2,
            decoration: const InputDecoration(labelText: 'বিবরণ', prefixIcon: Icon(Icons.description_outlined), alignLabelWithHint: true),
          ),
        ],
      ),
    );
  }
}
