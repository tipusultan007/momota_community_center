import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/staff_provider.dart';
import '../../models/staff.dart';
import '../../theme/app_theme.dart';
import 'staff_form_screen.dart';
import 'salary_form_screen.dart';

class StaffDetailsScreen extends StatefulWidget {
  final Staff staff;

  const StaffDetailsScreen({super.key, required this.staff});

  @override
  State<StaffDetailsScreen> createState() => _StaffDetailsScreenState();
}

class _StaffDetailsScreenState extends State<StaffDetailsScreen> {
  late Staff _currentStaff;

  @override
  void initState() {
    super.initState();
    _currentStaff = widget.staff;
    _refreshDetails();
  }

  Future<void> _refreshDetails() async {
    final updated = await context.read<StaffProvider>().getStaffDetails(_currentStaff.id);
    if (updated != null && mounted) {
      setState(() => _currentStaff = updated);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('স্টাফ প্রোফাইল', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_outlined),
            onPressed: () async {
              await Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => StaffFormScreen(staff: _currentStaff)),
              );
              _refreshDetails();
            },
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            _buildProfileHeader(),
            const SizedBox(height: 24),
            _buildInfoCard(),
            const SizedBox(height: 24),
            _buildSalarySection(),
            const SizedBox(height: 40),
          ],
        ),
      ),
      bottomNavigationBar: _buildBottomAction(),
    );
  }

  Widget _buildProfileHeader() {
    return Column(
      children: [
        CircleAvatar(
          radius: 50,
          backgroundColor: AppTheme.primaryEmerald.withAlpha(15),
          child: Text(
            _currentStaff.name[0],
            style: GoogleFonts.manrope(
              fontSize: 40,
              fontWeight: FontWeight.w800,
              color: AppTheme.primaryEmerald,
            ),
          ),
        ),
        const SizedBox(height: 16),
        Text(
          _currentStaff.name,
          style: GoogleFonts.manrope(
            fontSize: 22,
            fontWeight: FontWeight.w800,
            color: AppTheme.textNavy,
          ),
        ),
        Text(
          _currentStaff.designation,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: Colors.grey,
          ),
        ),
      ],
    );
  }

  Widget _buildInfoCard() {
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
          _infoRow(Icons.phone_outlined, 'মোবাইল নম্বর', _currentStaff.phone),
          const Divider(height: 32),
          _infoRow(Icons.location_on_outlined, 'ঠিকানা', _currentStaff.address ?? 'তথ্য পাওয়া যায়নি'),
          const Divider(height: 32),
          _infoRow(Icons.payments_outlined, 'মাসিক বেতন', '৳ ${_currentStaff.salaryAmount}'),
          const Divider(height: 32),
          _infoRow(Icons.calendar_today_outlined, 'যোগদানের তারিখ', _currentStaff.joinDate?.split('T')[0] ?? 'তথ্য পাওয়া যায়নি'),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppTheme.backgroundSlate,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: AppTheme.primaryEmerald, size: 20),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: const TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.bold),
              ),
              Text(
                value,
                style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 15, color: AppTheme.textNavy),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildSalarySection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'বেতন প্রদানের ইতিহাস',
              style: GoogleFonts.manrope(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: AppTheme.textNavy,
              ),
            ),
            Text(
              '${_currentStaff.salaries.length} টি রেকর্ড',
              style: const TextStyle(color: Colors.grey, fontSize: 12, fontWeight: FontWeight.bold),
            ),
          ],
        ),
        const SizedBox(height: 16),
        if (_currentStaff.salaries.isEmpty)
          _buildEmptyHistory()
        else
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _currentStaff.salaries.length,
            itemBuilder: (context, index) {
              final salary = _currentStaff.salaries[index];
              return _buildSalaryCard(salary);
            },
          ),
      ],
    );
  }

  Widget _buildSalaryCard(salary) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.blue.withAlpha(20)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: Colors.blue.withAlpha(10),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.receipt_long_outlined, color: Colors.blue, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${salary.month} ${salary.year}',
                  style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16, color: AppTheme.textNavy),
                ),
                Text(
                  'প্রদান: ${salary.paymentDate.split('T')[0]}',
                  style: const TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
          Text(
            '৳ ${salary.amount}',
            style: GoogleFonts.manrope(
              fontWeight: FontWeight.w900,
              fontSize: 18,
              color: AppTheme.primaryEmerald,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyHistory() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(32),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.withAlpha(20)),
      ),
      child: Column(
        children: [
          Icon(Icons.history, color: Colors.grey.shade300, size: 48),
          const SizedBox(height: 12),
          Text(
            'এখনো কোনো বেতন প্রদান করা হয়নি',
            style: TextStyle(color: Colors.grey.shade400, fontWeight: FontWeight.bold, fontSize: 13),
          ),
        ],
      ),
    );
  }

  Widget _buildBottomAction() {
    return Container(
      padding: const EdgeInsets.fromLTRB(24, 12, 24, 24),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(5),
            blurRadius: 20,
            offset: const Offset(0, -10),
          ),
        ],
      ),
      child: SizedBox(
        height: 56,
        child: ElevatedButton.icon(
          onPressed: () async {
            await showDialog(
              context: context,
              builder: (_) => SalaryFormScreen(staff: _currentStaff),
            );
            _refreshDetails();
          },
          icon: const Icon(Icons.add_card_outlined, color: Colors.white),
          label: Text(
            'বেতন প্রদান করুন',
            style: GoogleFonts.manrope(fontWeight: FontWeight.w800, color: Colors.white, fontSize: 16),
          ),
          style: ElevatedButton.styleFrom(
            backgroundColor: AppTheme.textNavy,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            elevation: 0,
          ),
        ),
      ),
    );
  }
}
