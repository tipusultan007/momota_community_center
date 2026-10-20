import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../api/api_service.dart';
import '../../models/loan.dart';
import '../../theme/app_theme.dart';
import 'loan_details_screen.dart';
import 'loan_form_screen.dart';

class LoanListScreen extends StatefulWidget {
  const LoanListScreen({super.key});

  @override
  State<LoanListScreen> createState() => _LoanListScreenState();
}

class _LoanListScreenState extends State<LoanListScreen> {
  bool _isLoading = true;
  List<Loan> _loans = [];
  double _totalBorrowed = 0.0;
  double _totalRepaid = 0.0;
  double _totalDue = 0.0;
  int _unsettledCount = 0;

  String _selectedFilter = 'all'; // 'all', 'unsettled', 'paid'
  final TextEditingController _searchController = TextEditingController();

  final currencyFormat = NumberFormat('#,##,###.##', 'en_US');

  @override
  void initState() {
    super.initState();
    _fetchLoans();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchLoans() async {
    setState(() => _isLoading = true);
    try {
      final queryParams = <String, dynamic>{};
      if (_selectedFilter != 'all') {
        queryParams['status'] = _selectedFilter;
      }
      if (_searchController.text.trim().isNotEmpty) {
        queryParams['search'] = _searchController.text.trim();
      }

      final response = await ApiService.instance.get('loans', queryParameters: queryParams);
      if (response.statusCode == 200 && response.data != null) {
        final data = response.data['data'];
        final summary = data['summary'] ?? {};
        final rawLoans = data['loans'] ?? [];

        final List<Loan> loaded = [];
        for (var item in rawLoans) {
          if (item is Map<String, dynamic>) {
            loaded.add(Loan.fromJson(item));
          }
        }

        setState(() {
          _loans = loaded;
          _totalBorrowed = (summary['total_borrowed'] as num?)?.toDouble() ?? 0.0;
          _totalRepaid = (summary['total_repaid'] as num?)?.toDouble() ?? 0.0;
          _totalDue = (summary['total_due'] as num?)?.toDouble() ?? 0.0;
          _unsettledCount = (summary['unsettled_count'] as num?)?.toInt() ?? 0;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  void _showQuickPaymentModal(Loan loan) {
    final amountController = TextEditingController(text: loan.dueAmount.toStringAsFixed(0));
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
                      'ঋণদাতা: ${loan.personName} | বর্তমান দেনা: ৳ ${currencyFormat.format(loan.dueAmount)}',
                      style: TextStyle(fontSize: 13, color: Colors.grey.shade700),
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
                      initialValue: selectedMethod,
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
                                    const SnackBar(content: Text('সঠিক পরিমাণ উল্লেখ করুন')),
                                  );
                                  return;
                                }
                                if (payAmount > loan.dueAmount) {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(content: Text('পরিশোধের পরিমাণ ৳ ${currencyFormat.format(loan.dueAmount)} এর বেশি হতে পারে না')),
                                  );
                                  return;
                                }

                                setModalState(() => isSubmitting = true);
                                try {
                                  final res = await ApiService.instance.post('loans/${loan.id}/payments', data: {
                                    'amount': payAmount,
                                    'payment_date': dateController.text,
                                    'payment_method': selectedMethod,
                                    'notes': notesController.text.trim(),
                                  });

                                  if (context.mounted) {
                                    Navigator.pop(ctx);
                                    if (res.statusCode == 200 || res.statusCode == 201) {
                                      ScaffoldMessenger.of(this.context).showSnackBar(
                                        const SnackBar(content: Text('টাকা ফেরত সফলভাবে রেকর্ড করা হয়েছে।')),
                                      );
                                      _fetchLoans();
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('হাওলাত / ঋণ ও ধার-দেনা'),
        backgroundColor: Colors.white,
        elevation: 0.5,
        foregroundColor: AppTheme.textNavy,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded),
            tooltip: 'রিফ্রেশ',
            onPressed: _fetchLoans,
          ),
        ],
      ),
      body: Column(
        children: [
          // KPI Summary Cards
          Container(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
            color: Colors.white,
            child: Row(
              children: [
                Expanded(
                  child: _buildSummaryBox(
                    title: 'মোট ধার',
                    amount: _totalBorrowed,
                    color: Colors.blue.shade700,
                    icon: Icons.account_balance_rounded,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _buildSummaryBox(
                    title: 'মোট ফেরত',
                    amount: _totalRepaid,
                    color: Colors.teal.shade700,
                    icon: Icons.check_circle_outline_rounded,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _buildSummaryBox(
                    title: 'অবশিষ্ট দেনা',
                    amount: _totalDue,
                    color: Colors.red.shade700,
                    icon: Icons.warning_amber_rounded,
                  ),
                ),
              ],
            ),
          ),

          // Search and Filters
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            color: Colors.white,
            child: Column(
              children: [
                TextField(
                  controller: _searchController,
                  decoration: InputDecoration(
                    hintText: 'নাম, ফোন বা কারণ অনুসন্ধান...',
                    prefixIcon: const Icon(Icons.search, size: 20),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear, size: 18),
                            onPressed: () {
                              _searchController.clear();
                              _fetchLoans();
                            },
                          )
                        : null,
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(vertical: 10),
                    filled: true,
                    fillColor: Colors.grey.shade100,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                  ),
                  onSubmitted: (_) => _fetchLoans(),
                ),
                const SizedBox(height: 8),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _buildFilterChip('all', 'সকল ঋণ'),
                      const SizedBox(width: 8),
                      _buildFilterChip('unsettled', 'বকেয়া আছে ($_unsettledCount)'),
                      const SizedBox(width: 8),
                      _buildFilterChip('paid', 'পরিশোধিত'),
                    ],
                  ),
                ),
              ],
            ),
          ),

          const Divider(height: 1, thickness: 1, color: Color(0xFFEEEEEE)),

          // Loans List
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _loans.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.receipt_long_rounded, size: 64, color: Colors.grey.shade300),
                            const SizedBox(height: 12),
                            Text(
                              'কোনো ধারের রেকর্ড পাওয়া যায়নি',
                              style: TextStyle(color: Colors.grey.shade600, fontSize: 15),
                            ),
                            const SizedBox(height: 16),
                            ElevatedButton.icon(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: AppTheme.primaryTeal,
                                foregroundColor: Colors.white,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                              ),
                              onPressed: () async {
                                final created = await Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (_) => const LoanFormScreen()),
                                );
                                if (created == true) _fetchLoans();
                              },
                              icon: const Icon(Icons.add),
                              label: const Text('নতুন ধার গ্রহণ যুক্ত করুন'),
                            ),
                          ],
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: _fetchLoans,
                        child: ListView.builder(
                          padding: const EdgeInsets.all(12),
                          itemCount: _loans.length,
                          itemBuilder: (context, index) {
                            final loan = _loans[index];
                            return _buildLoanCard(loan);
                          },
                        ),
                      ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: AppTheme.primaryTeal,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: const Text('ধার গ্রহণ'),
        onPressed: () async {
          final created = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const LoanFormScreen()),
          );
          if (created == true) {
            _fetchLoans();
          }
        },
      ),
    );
  }

  Widget _buildSummaryBox({
    required String title,
    required double amount,
    required Color color,
    required IconData icon,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withValues(alpha: 0.2)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 14, color: color),
              const SizedBox(width: 4),
              Expanded(
                child: Text(
                  title,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: color),
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              '৳ ${currencyFormat.format(amount)}',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w800,
                color: color,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String key, String label) {
    final isSelected = _selectedFilter == key;
    return ChoiceChip(
      label: Text(
        label,
        style: TextStyle(
          fontSize: 12,
          fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
          color: isSelected ? Colors.white : AppTheme.textNavy,
        ),
      ),
      selected: isSelected,
      selectedColor: AppTheme.primaryTeal,
      backgroundColor: Colors.grey.shade100,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      onSelected: (selected) {
        if (selected) {
          setState(() => _selectedFilter = key);
          _fetchLoans();
        }
      },
    );
  }

  Widget _buildLoanCard(Loan loan) {
    final isPaid = loan.status == 'paid';
    final isPartial = loan.status == 'partial';

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 1,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () async {
          final changed = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => LoanDetailsScreen(loanId: loan.id)),
          );
          if (changed == true) _fetchLoans();
        },
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CircleAvatar(
                    radius: 20,
                    backgroundColor: isPaid
                        ? Colors.teal.shade50
                        : (isPartial ? Colors.amber.shade50 : Colors.red.shade50),
                    child: Icon(
                      isPaid
                          ? Icons.check_circle_rounded
                          : (isPartial ? Icons.hourglass_bottom_rounded : Icons.money_off_rounded),
                      color: isPaid
                          ? Colors.teal
                          : (isPartial ? Colors.amber.shade800 : Colors.red.shade700),
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          loan.personName,
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.textNavy,
                          ),
                        ),
                        if (loan.phone != null && loan.phone!.isNotEmpty)
                          Text(
                            loan.phone!,
                            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                          ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: isPaid
                          ? Colors.green.shade50
                          : (isPartial ? Colors.orange.shade50 : Colors.red.shade50),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      isPaid ? 'পরিশোধিত' : (isPartial ? 'আংশিক পরিশোধ' : 'অপরিশোধিত'),
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: isPaid
                            ? Colors.green.shade800
                            : (isPartial ? Colors.orange.shade800 : Colors.red.shade800),
                      ),
                    ),
                  ),
                ],
              ),

              if (loan.purpose != null && loan.purpose!.isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(
                  'উদ্দেশ্য: ${loan.purpose}',
                  style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],

              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                decoration: BoxDecoration(
                  color: Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('মোট ধার', style: TextStyle(fontSize: 10, color: Colors.grey.shade600)),
                        Text('৳ ${currencyFormat.format(loan.amount)}',
                            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('পরিশোধ', style: TextStyle(fontSize: 10, color: Colors.grey.shade600)),
                        Text('৳ ${currencyFormat.format(loan.paidAmount)}',
                            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.teal)),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text('বকেয়া দেনা', style: TextStyle(fontSize: 10, color: Colors.grey.shade600)),
                        Text(
                          '৳ ${currencyFormat.format(loan.dueAmount)}',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.bold,
                            color: loan.dueAmount > 0 ? Colors.red.shade700 : Colors.grey.shade600,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'তারিখ: ${loan.date}',
                    style: TextStyle(fontSize: 11, color: Colors.grey.shade500),
                  ),
                  if (loan.dueAmount > 0)
                    TextButton.icon(
                      style: TextButton.styleFrom(
                        foregroundColor: Colors.teal.shade700,
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        visualDensity: VisualDensity.compact,
                      ),
                      icon: const Icon(Icons.payment_rounded, size: 16),
                      label: const Text('ফেরত দিন', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                      onPressed: () => _showQuickPaymentModal(loan),
                    ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
