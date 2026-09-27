import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/staff_provider.dart';
import '../../models/staff.dart';
import '../../theme/app_theme.dart';

class StaffFormScreen extends StatefulWidget {
  final Staff? staff;

  const StaffFormScreen({super.key, this.staff});

  @override
  State<StaffFormScreen> createState() => _StaffFormScreenState();
}

class _StaffFormScreenState extends State<StaffFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _designationController;
  late TextEditingController _salaryController;
  late TextEditingController _addressController;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.staff?.name);
    _phoneController = TextEditingController(text: widget.staff?.phone);
    _designationController = TextEditingController(text: widget.staff?.designation);
    _salaryController = TextEditingController(text: widget.staff?.salaryAmount.toString());
    _addressController = TextEditingController(text: widget.staff?.address);
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _designationController.dispose();
    _salaryController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    final data = {
      'name': _nameController.text,
      'phone': _phoneController.text,
      'designation': _designationController.text,
      'salary_amount': double.tryParse(_salaryController.text) ?? 0,
      'address': _addressController.text,
    };

    bool success;
    if (widget.staff == null) {
      success = await context.read<StaffProvider>().createStaff(data);
    } else {
      success = await context.read<StaffProvider>().updateStaff(widget.staff!.id, data);
    }

    setState(() => _isLoading = false);

    if (success && mounted) {
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(
          widget.staff == null ? 'নতুন স্টাফ যোগ করুন' : 'স্টাফ এডিট করুন',
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              _buildInputCard(),
              const SizedBox(height: 32),
              _buildSaveButton(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInputCard() {
    return Container(
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
          _buildTextField(_nameController, 'স্টাফের নাম', Icons.person_outline),
          const SizedBox(height: 20),
          _buildTextField(_phoneController, 'ফোন নম্বর', Icons.phone_outlined, keyboardType: TextInputType.phone),
          const SizedBox(height: 20),
          _buildTextField(_designationController, 'পদবী', Icons.work_outline),
          const SizedBox(height: 20),
          _buildTextField(_salaryController, 'মাসিক বেতন', Icons.payments_outlined, keyboardType: TextInputType.number),
          const SizedBox(height: 20),
          _buildTextField(_addressController, 'ঠিকানা', Icons.location_on_outlined, maxLines: 3),
        ],
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
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(18),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(18),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(18),
          borderSide: const BorderSide(color: AppTheme.primaryEmerald, width: 1),
        ),
        labelStyle: TextStyle(color: Colors.grey.shade600, fontSize: 14),
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
