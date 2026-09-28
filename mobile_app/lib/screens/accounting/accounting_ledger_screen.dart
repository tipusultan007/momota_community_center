import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/accounting_provider.dart';
import '../../theme/app_theme.dart';
import './transaction_form_screen.dart';
import './category_list_screen.dart';

class AccountingLedgerScreen extends StatefulWidget {
  const AccountingLedgerScreen({super.key});

  @override
  State<AccountingLedgerScreen> createState() => _AccountingLedgerScreenState();
}

class _AccountingLedgerScreenState extends State<AccountingLedgerScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final ScrollController _scrollController = ScrollController();
  int _selectedMonth = DateTime.now().month;
  int _selectedYear = DateTime.now().year;
  String _currentType = 'all';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _tabController.addListener(_handleTabChange);
    _scrollController.addListener(_onScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData(refresh: true);
    });
  }

  void _handleTabChange() {
    final types = ['all', 'income', 'expense'];
    if (_tabController.index >= 0 && _tabController.index < types.length) {
      final selectedType = types[_tabController.index];
      if (_currentType != selectedType && !_tabController.indexIsChanging) {
        setState(() {
          _currentType = selectedType;
        });
        _loadData(refresh: true);
      }
    }
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      context.read<AccountingProvider>().loadMore(month: _selectedMonth, year: _selectedYear);
    }
  }

  void _loadData({bool refresh = true}) {
    if (!mounted) return;
    final provider = context.read<AccountingProvider>();
    provider.fetchTransactions(
      month: _selectedMonth, 
      year: _selectedYear, 
      type: _currentType,
      refresh: refresh,
    );
    provider.fetchCategories();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 8),
            _buildStatsSummary(),
            _buildFilterAndTabs(),
            Expanded(
              child: Consumer<AccountingProvider>(
                builder: (context, provider, _) {
                  if (provider.isLoading && provider.transactions.isEmpty) {
                    return const Center(child: CircularProgressIndicator());
                  }

                  return RefreshIndicator(
                    onRefresh: () async => _loadData(refresh: true),
                    child: provider.transactions.isEmpty
                        ? ListView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            children: [
                              SizedBox(height: MediaQuery.of(context).size.height * 0.15),
                              _buildEmptyState(),
                            ],
                          )
                        : ListView.builder(
                            physics: const AlwaysScrollableScrollPhysics(),
                            controller: _scrollController,
                            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                            itemCount: provider.transactions.length + (provider.hasMore ? 1 : 0),
                            itemBuilder: (context, index) {
                              if (index == provider.transactions.length) {
                                return const Padding(
                                  padding: EdgeInsets.symmetric(vertical: 32),
                                  child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
                                );
                              }
                              final tx = provider.transactions[index];
                              return _buildCompactTransactionCard(tx);
                            },
                          ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: _buildFABs(),
    );
  }

  Widget _buildStatsSummary() {
    return Consumer<AccountingProvider>(
      builder: (context, provider, _) {
        return Container(
          margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppTheme.textNavy,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [BoxShadow(color: AppTheme.textNavy.withAlpha(30), blurRadius: 15, offset: const Offset(0, 8))],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _statItemCompact('মোট আয়', provider.totalIncome, AppTheme.primaryEmerald),
              Container(width: 1, height: 30, color: Colors.white10),
              _statItemCompact('মোট ব্যয়', provider.totalExpense, Colors.redAccent),
              Container(width: 1, height: 30, color: Colors.white10),
              _statItemCompact('নিট', provider.netProfit, Colors.white),
            ],
          ),
        );
      },
    );
  }

  Widget _statItemCompact(String label, double amount, Color color) {
    return Column(
      children: [
        Text(label, style: const TextStyle(color: Colors.white54, fontSize: 10)),
        const SizedBox(height: 2),
        Text('৳${amount.toStringAsFixed(0)}', 
          style: TextStyle(color: color, fontWeight: FontWeight.w900, fontSize: 13)),
      ],
    );
  }

  Widget _buildFilterAndTabs() {
    final months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বের', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
    
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Row(
            children: [
              Expanded(
                child: Container(
                  height: 40,
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
                  child: DropdownButton<int>(
                    value: _selectedMonth,
                    isExpanded: true,
                    underline: const SizedBox(),
                    items: List.generate(12, (i) => DropdownMenuItem(value: i + 1, child: Text(months[i], style: const TextStyle(fontSize: 13)))),
                    onChanged: (v) {
                      if (v != null) {
                        setState(() { _selectedMonth = v; });
                        _loadData(refresh: true);
                      }
                    },
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Container(
                height: 40,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
                child: DropdownButton<int>(
                  value: _selectedYear,
                  underline: const SizedBox(),
                  items: [2024, 2025, 2026].map((y) => DropdownMenuItem(value: y, child: Text(y.toString(), style: const TextStyle(fontSize: 13)))).toList(),
                  onChanged: (v) {
                    if (v != null) {
                      setState(() { _selectedYear = v; });
                      _loadData(refresh: true);
                    }
                  },
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        TabBar(
          controller: _tabController,
          labelColor: AppTheme.textNavy,
          unselectedLabelColor: Colors.grey,
          indicatorSize: TabBarIndicatorSize.label,
          labelPadding: EdgeInsets.zero,
          indicatorColor: AppTheme.primaryEmerald,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
          tabs: const [
            Tab(text: 'সব'),
            Tab(text: 'আয়'),
            Tab(text: 'ব্যয়'),
          ],
        ),
        const Divider(height: 1, thickness: 0.5),
      ],
    );
  }

  Widget _buildCompactTransactionCard(dynamic tx) {
    final isIncome = tx.type == 'income';
    final color = isIncome ? AppTheme.primaryEmerald : Colors.redAccent;

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withAlpha(10)),
      ),
      child: ListTile(
        dense: true,
        contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(color: color.withAlpha(10), borderRadius: BorderRadius.circular(10)),
          child: Icon(isIncome ? Icons.expand_more : Icons.expand_less, color: color, size: 16),
        ),
        title: Text(tx.categoryName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        subtitle: Text('${tx.date} ${tx.description != null ? '• ${tx.description}' : ''}', 
          maxLines: 1, overflow: TextOverflow.ellipsis, 
          style: const TextStyle(color: Colors.grey, fontSize: 11)),
        trailing: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Text(
              '${isIncome ? '+' : '-'} ৳${tx.amount.toStringAsFixed(0)}',
              style: TextStyle(fontWeight: FontWeight.w900, color: color, fontSize: 13),
            ),
            if (tx.bookingId != null)
              const Text('বুকিং', style: TextStyle(color: Colors.blue, fontSize: 8, fontWeight: FontWeight.bold)),
          ],
        ),
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => TransactionFormScreen(transaction: tx))),
        onLongPress: () => _confirmDelete(tx),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.history_toggle_off_outlined, size: 48, color: Colors.grey.withAlpha(60)),
          const SizedBox(height: 12),
          const Text('এই শর্তে কোনো লেনদেন নেই।', style: TextStyle(color: Colors.grey, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildFABs() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        FloatingActionButton.extended(
          heroTag: 'income_fab',
          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (c) => const TransactionFormScreen(initialType: 'income'))),
          backgroundColor: AppTheme.primaryEmerald,
          icon: const Icon(Icons.add, color: Colors.white, size: 18),
          label: const Text('আয়', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
        ),
        const SizedBox(width: 12),
        FloatingActionButton.extended(
          heroTag: 'expense_fab',
          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (c) => const TransactionFormScreen(initialType: 'expense'))),
          backgroundColor: Colors.redAccent,
          icon: const Icon(Icons.remove, color: Colors.white, size: 18),
          label: const Text('ব্যয়', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
        ),
      ],
    );
  }

  Future<void> _confirmDelete(dynamic tx) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('লেনদেন মুছুন'),
        content: const Text('আপনি কি নিশ্চিত যে এই লেনদেনটি মুছে ফেলতে চান?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('না')),
          TextButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('হ্যাঁ', style: TextStyle(color: Colors.red))),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final success = await context.read<AccountingProvider>().deleteTransaction(tx.type, tx.id);
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('লেনদেন মুছে ফেলা হয়েছে।')));
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('মুছে ফেলা সম্ভব হয়নি।')));
      }
    }
  }
}
