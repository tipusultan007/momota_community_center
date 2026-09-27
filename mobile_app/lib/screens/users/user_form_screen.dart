import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/user_provider.dart';
import '../../theme/app_theme.dart';

class UserFormScreen extends StatefulWidget {
  final dynamic user;
  const UserFormScreen({super.key, this.user});

  @override
  State<UserFormScreen> createState() => _UserFormScreenState();
}

class _UserFormScreenState extends State<UserFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _passwordController;
  late TextEditingController _phoneController;
  late TextEditingController _addressController;
  String _selectedRole = 'Staff';
  bool _isLoading = false;

  final List<String> _roles = ['Admin', 'Manager', 'Staff'];

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.user?['name']);
    _emailController = TextEditingController(text: widget.user?['email']);
    _passwordController = TextEditingController();
    _phoneController = TextEditingController(text: widget.user?['phone']);
    _addressController = TextEditingController(text: widget.user?['address']);
    
    if (widget.user != null && widget.user['roles'] != null && (widget.user['roles'] as List).isNotEmpty) {
      _selectedRole = widget.user['roles'][0]['name'];
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    final data = {
      'name': _nameController.text,
      'email': _emailController.text,
      'role': _selectedRole,
      'phone': _phoneController.text,
      'address': _addressController.text,
    };

    if (_passwordController.text.isNotEmpty) {
      data['password'] = _passwordController.text;
    } else if (widget.user == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('পাসওয়ার্ড প্রদান করা আবশ্যক')));
      setState(() => _isLoading = false);
      return;
    }

    final result = widget.user == null
        ? await context.read<UserProvider>().createUser(data)
        : await context.read<UserProvider>().updateUser(widget.user['id'], data);

    setState(() => _isLoading = false);

    if (result['success'] == true && mounted) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? (widget.user == null ? 'ব্যবহারকারী তৈরি করা হয়েছে' : 'ব্যবহারকারী আপডেট করা হয়েছে')),
          backgroundColor: AppTheme.primaryEmerald,
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'তৈরি করা সম্ভব হয়নি। পুনরায় চেষ্টা করুন।'),
          backgroundColor: Colors.redAccent,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(widget.user == null ? 'নতুন ব্যবহারকারী' : 'এডিট করুন', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
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
                  borderRadius: BorderRadius.circular(30),
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
                    _buildTextField(_nameController, 'সম্পূর্ণ নাম', Icons.person_outline),
                    const SizedBox(height: 20),
                    _buildTextField(_emailController, 'ইমেইল', Icons.email_outlined, keyboardType: TextInputType.emailAddress),
                    const SizedBox(height: 20),
                    _buildTextField(_passwordController, widget.user == null ? 'পাসওয়ার্ড' : 'নতুন পাসওয়ার্ড (ঐচ্ছিক)', Icons.lock_outline, obscureText: true),
                    const SizedBox(height: 20),
                    _buildTextField(_phoneController, 'ফোন নম্বর', Icons.phone_outlined, keyboardType: TextInputType.phone),
                    const SizedBox(height: 20),
                    _buildRoleDropdown(),
                  ],
                ),
              ),
              const SizedBox(height: 32),
              SizedBox(
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
                          style: GoogleFonts.manrope(fontSize: 16, fontWeight: FontWeight.w800, color: Colors.white),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, IconData icon, {bool obscureText = false, TextInputType? keyboardType}) {
    return TextFormField(
      controller: controller,
      obscureText: obscureText,
      keyboardType: keyboardType,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: AppTheme.primaryEmerald, size: 20),
        filled: true,
        fillColor: AppTheme.backgroundSlate.withAlpha(50),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide.none),
      ),
      validator: (v) {
        if (widget.user == null && label == 'পাসওয়ার্ড') {
          if (v == null || v.isEmpty) return 'পাসওয়ার্ড প্রদান করা আবশ্যক';
          if (v.length < 6) return 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে';
        }
        if (label == 'নতুন পাসওয়ার্ড (ঐচ্ছিক)' && v != null && v.isNotEmpty && v.length < 6) {
          return 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে';
        }
        if (label == 'ইমেইল') {
          if (v == null || v.trim().isEmpty) return 'ইমেইল এড্রেস প্রদান করুন';
          if (!v.contains('@') || !v.contains('.')) return 'সঠিক ইমেইল এড্রেস লিখুন';
        }
        if (label == 'সম্পূর্ণ নাম' && (v == null || v.trim().isEmpty)) {
          return 'সম্পূর্ণ নাম প্রদান করুন';
        }
        return null;
      },
    );
  }

  Widget _buildRoleDropdown() {
    return DropdownButtonFormField<String>(
      value: _selectedRole,
      decoration: InputDecoration(
        labelText: 'রোল / পদবী',
        prefixIcon: const Icon(Icons.admin_panel_settings_outlined, color: AppTheme.primaryEmerald, size: 20),
        filled: true,
        fillColor: AppTheme.backgroundSlate.withAlpha(50),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide.none),
      ),
      items: _roles.map((r) {
        String label = r;
        if (r == 'Admin') label = 'অ্যাডমিন (Admin)';
        if (r == 'Manager') label = 'ম্যানেজার (Manager)';
        if (r == 'Staff') label = 'স্টাফ (Staff)';
        return DropdownMenuItem(value: r, child: Text(label));
      }).toList(),
      onChanged: (val) => setState(() => _selectedRole = val!),
    );
  }
}
