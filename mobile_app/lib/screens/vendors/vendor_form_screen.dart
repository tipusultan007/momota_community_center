import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/vendor_provider.dart';
import '../../theme/app_theme.dart';

class VendorFormScreen extends StatefulWidget {
  final Map<String, dynamic>? vendor;

  const VendorFormScreen({super.key, this.vendor});

  @override
  State<VendorFormScreen> createState() => _VendorFormScreenState();
}

class _VendorFormScreenState extends State<VendorFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _commissionController;
  String _selectedType = 'Sound';

  final List<String> _types = ['Sound', 'Generator', 'Decoration', 'Catering', 'Other'];

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.vendor?['name'] ?? '');
    _phoneController = TextEditingController(text: widget.vendor?['phone'] ?? '');
    _commissionController = TextEditingController(
      text: widget.vendor?['commission_rate']?.toString() ?? '0'
    );
    if (widget.vendor != null && _types.contains(widget.vendor!['type'])) {
      _selectedType = widget.vendor!['type'];
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _commissionController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final provider = context.read<VendorProvider>();
    final data = {
      'name': _nameController.text.trim(),
      'type': _selectedType,
      'phone': _phoneController.text.trim(),
      'commission_rate': double.tryParse(_commissionController.text) ?? 0,
    };

    bool success;
    if (widget.vendor == null) {
      success = await provider.createVendor(data);
    } else {
      success = await provider.updateVendor(widget.vendor!['id'], data);
    }

    if (mounted) {
      if (success) {
        provider.fetchVendors();
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(widget.vendor == null ? 'ভেন্ডর যুক্ত করা হয়েছে।' : 'ভেন্ডর আপডেট করা হয়েছে।')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('অপারেশন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEdit = widget.vendor != null;

    return Scaffold(
      appBar: AppBar(
        title: Text(isEdit ? 'ভেন্ডর এডিট করুন' : 'নতুন ভেন্ডর যুক্ত করুন',
          style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 18)
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildLabel('ভেন্ডরের নাম *'),
              TextFormField(
                controller: _nameController,
                decoration: _buildInputDecoration('নাম লিখুন'),
                validator: (val) => val == null || val.isEmpty ? 'নাম প্রয়োজন' : null,
              ),
              const SizedBox(height: 20),
              
              _buildLabel('ভেন্ডর টাইপ *'),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  border: Border.all(color: Colors.grey.shade300),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: _selectedType,
                    isExpanded: true,
                    items: _types.map((t) => DropdownMenuItem(value: t, child: Text(t))).toList(),
                    onChanged: (val) => setState(() => _selectedType = val!),
                  ),
                ),
              ),
              const SizedBox(height: 20),

              _buildLabel('ফোন নম্বর'),
              TextFormField(
                controller: _phoneController,
                keyboardType: TextInputType.phone,
                decoration: _buildInputDecoration('ফোন নম্বর লিখুন'),
              ),
              const SizedBox(height: 20),

              _buildLabel('কমিশন রেট (%)'),
              TextFormField(
                controller: _commissionController,
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: _buildInputDecoration('কমিশন শতাংশ'),
              ),
              const SizedBox(height: 40),

              SizedBox(
                width: double.infinity,
                height: 56,
                child: Consumer<VendorProvider>(
                  builder: (context, provider, child) {
                    return ElevatedButton(
                      onPressed: provider.isLoading ? null : _submit,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.primaryEmerald,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      ),
                      child: provider.isLoading 
                        ? const CircularProgressIndicator(color: Colors.white)
                        : Text(isEdit ? 'আপডেট করুন' : 'সংরক্ষণ করুন', 
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(String label) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4),
      child: Text(label, style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.textNavy, fontSize: 13)),
    );
  }

  InputDecoration _buildInputDecoration(String hint) {
    return InputDecoration(
      hintText: hint,
      filled: true,
      fillColor: Colors.grey[50],
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppTheme.primaryEmerald, width: 2)),
    );
  }
}
