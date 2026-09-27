import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/staff_provider.dart';
import '../../models/staff.dart';
import '../../theme/app_theme.dart';

class SalaryFormScreen extends StatefulWidget {
  final Staff? staff;

  const SalaryFormScreen({super.key, this.staff});

  @override
  State<SalaryFormScreen> createState() => _SalaryFormScreenState();
}

class _SalaryFormScreenState extends State<SalaryFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _amountController;
  late TextEditingController _notesController;
  String _selectedMonth = 'January';
  int _selectedYear = DateTime.now().year;
  DateTime _paymentDate = DateTime.now();
  bool _isLoading = false;
  Staff? _selectedStaff;

  final List<String> _months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  @override
  void initState() {
    super.initState();
    _selectedStaff = widget.staff;
    _amountController = TextEditingController(text: _selectedStaff?.salaryAmount.toString() ?? '');
    _notesController = TextEditingController();
    
    // Set default month to current month
    _selectedMonth = _months[DateTime.now().month - 1];

    if (_selectedStaff == null) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        context.read<StaffProvider>().fetchStaff();
      });
    }
  }

  @override
  void dispose() {
    _amountController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedStaff == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('অনুগ্রহ করে স্টাফ নির্বাচন করুন')),
      );
      return;
    }

    setState(() => _isLoading = true);

    final data = {
      'amount': double.tryParse(_amountController.text) ?? 0,
      'month': _selectedMonth,
      'year': _selectedYear,
      'payment_date': _paymentDate.toIso8601String().split('T')[0],
      'notes': _notesController.text,
    };

    final success = await context.read<StaffProvider>().paySalary(_selectedStaff!.id, data);

    setState(() => _isLoading = false);

    if (success && mounted) {
      Navigator.pop(context, true);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('বেতন প্রদান সফল হয়েছে।')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      insetPadding: const EdgeInsets.all(20),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(28)),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'বেতন প্রদান করুন',
                style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 20),
              ),
              const SizedBox(height: 8),
              if (widget.staff != null)
                Text(
                  widget.staff!.name,
                  style: const TextStyle(color: Colors.grey, fontWeight: FontWeight.bold),
                ),
              if (widget.staff == null)
                Consumer<StaffProvider>(
                  builder: (context, provider, _) => DropdownButtonFormField<Staff>(
                    value: _selectedStaff,
                    decoration: InputDecoration(
                      hintText: 'স্টাফ নির্বাচন করুন',
                      filled: true,
                      fillColor: AppTheme.backgroundSlate,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                    ),
                    items: provider.staffList.map((s) => DropdownMenuItem(value: s, child: Text(s.name))).toList(),
                    onChanged: (v) {
                      setState(() {
                        _selectedStaff = v;
                        _amountController.text = v?.salaryAmount.toString() ?? '';
                      });
                    },
                  ),
                ),
              const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider()),
              
              const Text('টাকার পরিমাণ', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _amountController,
                decoration: InputDecoration(
                  prefixText: '৳ ',
                  filled: true,
                  fillColor: AppTheme.backgroundSlate,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                ),
                validator: (v) => v == null || v.isEmpty ? 'রকাম দিন' : null,
              ),
              
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('মাস', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<String>(
                          value: _selectedMonth,
                          decoration: InputDecoration(
                            filled: true,
                            fillColor: AppTheme.backgroundSlate,
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                          ),
                          items: _months.map((m) => DropdownMenuItem(value: m, child: Text(m))).toList(),
                          onChanged: (v) => setState(() => _selectedMonth = v!),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('বছর', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<int>(
                          value: _selectedYear,
                          decoration: InputDecoration(
                            filled: true,
                            fillColor: AppTheme.backgroundSlate,
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                          ),
                          items: List.generate(5, (i) => DateTime.now().year - 2 + i)
                              .map((y) => DropdownMenuItem(value: y, child: Text(y.toString())))
                              .toList(),
                          onChanged: (v) => setState(() => _selectedYear = v!),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              
              const SizedBox(height: 20),
              const Text('প্রদানের তারিখ', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
              const SizedBox(height: 8),
              InkWell(
                onTap: () async {
                  final picked = await showDatePicker(
                    context: context,
                    initialDate: _paymentDate,
                    firstDate: DateTime(2020),
                    lastDate: DateTime(2030),
                  );
                  if (picked != null) setState(() => _paymentDate = picked);
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                  decoration: BoxDecoration(
                    color: AppTheme.backgroundSlate,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.calendar_month_outlined, color: Colors.grey, size: 20),
                      const SizedBox(width: 12),
                      Text(
                        _paymentDate.toIso8601String().split('T')[0],
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ),
              
              const SizedBox(height: 20),
              const Text('মন্তব্য (ঐচ্ছিক)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _notesController,
                maxLines: 2,
                decoration: InputDecoration(
                  filled: true,
                  fillColor: AppTheme.backgroundSlate,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                ),
              ),
              
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.textNavy,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: _isLoading 
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('পেমেন্ট নিশ্চিত করুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(height: 12),
              SizedBox(
                width: double.infinity,
                child: TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('বাতিল', style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
