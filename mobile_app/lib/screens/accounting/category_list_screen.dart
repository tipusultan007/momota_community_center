import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/accounting_provider.dart';
import '../../models/transaction.dart';
import '../../theme/app_theme.dart';

class CategoryListScreen extends StatefulWidget {
  const CategoryListScreen({super.key});

  @override
  State<CategoryListScreen> createState() => _CategoryListScreenState();
}

class _CategoryListScreenState extends State<CategoryListScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    Future.microtask(() => context.read<AccountingProvider>().fetchCategories());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('ক্যাটাগরি ব্যবস্থাপনা', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.textNavy,
          unselectedLabelColor: Colors.grey,
          indicatorColor: AppTheme.primaryEmerald,
          tabs: const [
            Tab(text: 'আয় ক্যাটাগরি'),
            Tab(text: 'ব্যয় ক্যাটাগরি'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildCategoryList('income'),
          _buildCategoryList('expense'),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showCategoryForm(context, _tabController.index == 0 ? 'income' : 'expense'),
        backgroundColor: AppTheme.textNavy,
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildCategoryList(String type) {
    return Consumer<AccountingProvider>(
      builder: (context, provider, _) {
        final categories = type == 'income' ? provider.incomeCategories : provider.expenseCategories;
        
        if (categories.isEmpty) {
          return const Center(child: Text('কোনো ক্যাটাগরি পাওয়া যায়নি।', style: TextStyle(color: Colors.grey)));
        }

        return ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: categories.length,
          itemBuilder: (context, index) {
            final category = categories[index];
            return Card(
              margin: const EdgeInsets.only(bottom: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: ListTile(
                title: Text(category.name, style: const TextStyle(fontWeight: FontWeight.bold)),
                subtitle: category.description != null ? Text(category.description!) : null,
                trailing: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    IconButton(
                      icon: const Icon(Icons.edit_outlined, size: 20),
                      onPressed: () => _showCategoryForm(context, type, category: category),
                    ),
                    IconButton(
                      icon: const Icon(Icons.delete_outline, size: 20, color: Colors.red),
                      onPressed: () => _confirmDelete(context, type, category),
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

  void _showCategoryForm(BuildContext context, String type, {TransactionCategory? category}) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _CategoryFormSheet(type: type, category: category),
    );
  }

  Future<void> _confirmDelete(BuildContext context, String type, TransactionCategory category) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('ক্যাটাগরি মুছুন'),
        content: Text('আপনি কি নিশ্চিত যে "${category.name}" ক্যাটাগরি মুছে ফেলতে চান?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('না')),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('হ্যাঁ', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final success = await context.read<AccountingProvider>().deleteCategory(type, category.id);
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('ক্যাটাগরি সফলভাবে মুছে ফেলা হয়েছে।')));
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('মুছে ফেলা সম্ভব হয়নি। সম্ভবত এটি কোনো লেনদেনে ব্যবহৃত হচ্ছে।')));
      }
    }
  }
}

class _CategoryFormSheet extends StatefulWidget {
  final String type;
  final TransactionCategory? category;

  const _CategoryFormSheet({required this.type, this.category});

  @override
  State<_CategoryFormSheet> createState() => _CategoryFormSheetState();
}

class _CategoryFormSheetState extends State<_CategoryFormSheet> {
  final _nameController = TextEditingController();
  final _descController = TextEditingController();
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    if (widget.category != null) {
      _nameController.text = widget.category!.name;
      _descController.text = widget.category!.description ?? '';
    }
  }

  Future<void> _save() async {
    if (_nameController.text.isEmpty) return;
    
    setState(() => _isSaving = true);
    
    bool success;
    if (widget.category == null) {
      success = await context.read<AccountingProvider>().storeCategory(
        widget.type, _nameController.text, _descController.text);
    } else {
      success = await context.read<AccountingProvider>().updateCategory(
        widget.type, widget.category!.id, _nameController.text, _descController.text);
    }

    if (success && mounted) {
      Navigator.pop(context);
    } else if (mounted) {
      setState(() => _isSaving = false);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('সংরক্ষণ করতে সমস্যা হয়েছে।')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
        top: 24, left: 24, right: 24, 
        bottom: MediaQuery.of(context).viewInsets.bottom + 24),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(widget.category == null ? 'নতুন ক্যাটাগরি' : 'ক্যাটাগরি এডিট', 
            style: GoogleFonts.manrope(fontSize: 20, fontWeight: FontWeight.w800)),
          const SizedBox(height: 24),
          TextField(
            controller: _nameController,
            decoration: const InputDecoration(labelText: 'নাম*', prefixIcon: Icon(Icons.label_outline)),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _descController,
            decoration: const InputDecoration(labelText: 'বিবরণ', prefixIcon: Icon(Icons.notes_outlined)),
          ),
          const SizedBox(height: 32),
          SizedBox(
            width: double.infinity,
            height: 56,
            child: ElevatedButton(
              onPressed: _isSaving ? null : _save,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.textNavy,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              child: _isSaving 
                ? const CircularProgressIndicator(color: Colors.white)
                : const Text('সংরক্ষণ করুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }
}
