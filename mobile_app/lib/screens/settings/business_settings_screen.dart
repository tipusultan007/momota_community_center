import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/auth_provider.dart';
import '../../theme/app_theme.dart';

class BusinessSettingsScreen extends StatefulWidget {
  const BusinessSettingsScreen({super.key});

  @override
  State<BusinessSettingsScreen> createState() => _BusinessSettingsScreenState();
}

class _BusinessSettingsScreenState extends State<BusinessSettingsScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _addressController;
  late TextEditingController _phoneController;
  late TextEditingController _invoiceConditionsController;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    final tenant = context.read<AuthProvider>().user?['tenant'];
    _nameController = TextEditingController(text: tenant?['name']);
    _addressController = TextEditingController(text: tenant?['address']);
    _phoneController = TextEditingController(text: tenant?['phone']);
    _invoiceConditionsController = TextEditingController(text: tenant?['invoice_conditions']);
  }

  @override
  void dispose() {
    _nameController.dispose();
    _addressController.dispose();
    _phoneController.dispose();
    _invoiceConditionsController.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    final success = await context.read<AuthProvider>().updateTenantSettings({
      'name': _nameController.text,
      'address': _addressController.text,
      'phone': _phoneController.text,
      'invoice_conditions': _invoiceConditionsController.text,
    });

    setState(() => _isLoading = false);

    if (success && mounted) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('ব্যবসার তথ্য সফলভাবে আপডেট করা হয়েছে।')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('ব্যবসার তথ্য', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withAlpha(5),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    _buildTextField(_nameController, 'প্রতিষ্ঠানের নাম', Icons.business_outlined),
                    const SizedBox(height: 20),
                    _buildTextField(_phoneController, 'ফোন নম্বর', Icons.phone_outlined, keyboardType: TextInputType.phone),
                    const SizedBox(height: 20),
                    _buildTextField(_addressController, 'ঠিকানা', Icons.location_on_outlined, maxLines: 2),
                    const SizedBox(height: 20),
                    _buildTextField(_invoiceConditionsController, 'ইনভয়েস শর্তাবলী', Icons.description_outlined, maxLines: 3),
                  ],
                ),
              ),
              const SizedBox(height: 32),
              _buildSaveButton(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, IconData icon, {TextInputType? keyboardType, int maxLines = 1}) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: AppTheme.primaryEmerald, size: 20),
        filled: true,
        fillColor: AppTheme.backgroundSlate.withAlpha(50),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide.none),
      ),
      validator: (v) => v == null || v.isEmpty ? 'এই ঘরটি পূরণ করুন' : null,
    );
  }

  Widget _buildSaveButton() {
    return SizedBox(
      width: double.infinity,
      height: 60,
      child: ElevatedButton(
        onPressed: _isLoading ? null : _save,
        style: ElevatedButton.styleFrom(
          backgroundColor: AppTheme.textNavy,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          elevation: 0,
        ),
        child: _isLoading 
          ? const CircularProgressIndicator(color: Colors.white)
          : Text(
              'সংরক্ষণ করুন',
              style: GoogleFonts.manrope(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
      ),
    );
  }
}
