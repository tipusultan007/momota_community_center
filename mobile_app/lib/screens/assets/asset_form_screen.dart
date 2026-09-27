import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../models/asset.dart';
import '../../providers/asset_provider.dart';
import '../../theme/app_theme.dart';

class AssetFormScreen extends StatefulWidget {
  final Asset? asset;

  const AssetFormScreen({super.key, this.asset});

  @override
  State<AssetFormScreen> createState() => _AssetFormScreenState();
}

class _AssetFormScreenState extends State<AssetFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _stockController;
  late TextEditingController _descriptionController;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.asset?.name ?? '');
    _stockController = TextEditingController(text: widget.asset?.totalStock.toString() ?? '');
    _descriptionController = TextEditingController(text: widget.asset?.description ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _stockController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _saveAsset() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSaving = true);

    final data = {
      'name': _nameController.text,
      'total_stock': int.parse(_stockController.text),
      'description': _descriptionController.text,
    };

    final provider = context.read<AssetProvider>();
    bool success;

    if (widget.asset == null) {
      success = await provider.createAsset(data);
    } else {
      success = await provider.updateAsset(widget.asset!.id, data);
    }

    if (mounted) {
      setState(() => _isSaving = false);
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(widget.asset == null ? 'মালামাল যোগ করা হয়েছে' : 'মালামাল আপডেট করা হয়েছে')),
        );
        Navigator.pop(context);
      }
    }
  }

  Future<void> _deleteAsset() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('মালামাল মুছে ফেলুন'),
        content: const Text('আপনি কি নিশ্চিত যে আপনি এটি মুছে ফেলতে চান?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('না')),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('হ্যাঁ, মুছুন'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      setState(() => _isSaving = true);
      final success = await context.read<AssetProvider>().deleteAsset(widget.asset!.id);
      if (mounted) {
        setState(() => _isSaving = false);
        if (success) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('মালামাল মুছে ফেলা হয়েছে')));
          Navigator.pop(context);
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(widget.asset == null ? 'নতুন মালামাল' : 'মালামাল সংশোধন', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        actions: [
          if (widget.asset != null)
            IconButton(
              icon: const Icon(Icons.delete_outline, color: Colors.red),
              onPressed: _isSaving ? null : _deleteAsset,
            ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildFieldCard('মালামালের তথ্য', [
                _buildTextField(
                  controller: _nameController,
                  label: 'নাম',
                  hint: 'উদাহরণ: চেয়ার, টেবিল, লাইট',
                  icon: Icons.inventory_2_outlined,
                  validator: (v) => v!.isEmpty ? 'নাম লিখুন' : null,
                ),
                const SizedBox(height: 20),
                _buildTextField(
                  controller: _stockController,
                  label: 'মোট স্টক',
                  hint: '০',
                  icon: Icons.numbers_outlined,
                  keyboardType: TextInputType.number,
                  validator: (v) => v!.isEmpty ? 'স্টক সংখ্যা লিখুন' : null,
                ),
              ]),
              const SizedBox(height: 24),
              _buildFieldCard('বিস্তারিত (ঐচ্ছিক)', [
                _buildTextField(
                  controller: _descriptionController,
                  label: 'বিবরণ',
                  hint: 'মালামাল সম্পর্কে বিস্তারিত লিখুন...',
                  icon: Icons.description_outlined,
                  maxLines: 4,
                ),
              ]),
              const SizedBox(height: 40),
              ElevatedButton(
                onPressed: _isSaving ? null : _saveAsset,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryEmerald,
                  padding: const EdgeInsets.symmetric(vertical: 18),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                ),
                child: _isSaving
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : Text(widget.asset == null ? 'সংরক্ষণ করুন' : 'হালনাগাদ করুন',
                        style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16)),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFieldCard(String title, List<Widget> children) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: GoogleFonts.manrope(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.grey)),
          const SizedBox(height: 20),
          ...children,
        ],
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    TextInputType? keyboardType,
    int maxLines = 1,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        prefixIcon: Icon(icon, color: AppTheme.primaryEmerald, size: 20),
        filled: true,
        fillColor: AppTheme.backgroundSlate,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none),
        labelStyle: TextStyle(color: Colors.grey.shade600, fontWeight: FontWeight.w600),
      ),
    );
  }
}
