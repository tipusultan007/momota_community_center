import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/auth_provider.dart';
import '../../theme/app_theme.dart';
import 'edit_profile_screen.dart';
import 'change_password_screen.dart';
import 'business_settings_screen.dart';
import '../users/user_list_screen.dart';
import '../reports/report_selection_screen.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('সেটিংস', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            _buildProfileCard(context, user),
            const SizedBox(height: 32),
            _buildSettingsSection(
              context,
              'একাউন্ট সেটিংস',
              [
                _buildSettingsTile(
                  context,
                  Icons.person_outline,
                  'প্রোফাইল এডিট করুন',
                  'আপনার নাম ও ইমেইল পরিবর্তন করুন',
                  () => Navigator.push(context, MaterialPageRoute(builder: (_) => const EditProfileScreen())),
                ),
                _buildSettingsTile(
                  context,
                  Icons.lock_outline,
                  'পাসওয়ার্ড পরিবর্তন',
                  'আপনার একাউন্টের নিরাপত্তা নিশ্চিত করুন',
                  () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ChangePasswordScreen())),
                ),
              ],
            ),
            const SizedBox(height: 24),
            _buildSettingsSection(
              context,
              'ব্যবসা সেটিংস',
              [
                _buildSettingsTile(
                  context,
                  Icons.business_outlined,
                  'ব্যবসার তথ্য',
                  'ঠিকানা, ফোন নম্বর ও ইনভয়েস সেটিংস',
                  () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BusinessSettingsScreen())),
                ),
              ],
            ),
            const SizedBox(height: 24),
            _buildSettingsSection(
              context,
              'ম্যানেজমেন্ট সেটিংস',
              [
                _buildSettingsTile(
                  context,
                  Icons.people_alt_outlined,
                  'ব্যবহারকারী ব্যবস্থাপনা',
                  'সিস্টেম ব্যবহারকারী ও রোল নিয়ন্ত্রণ করুন',
                  () => Navigator.push(context, MaterialPageRoute(builder: (_) => const UserListScreen())),
                ),
                _buildSettingsTile(
                  context,
                  Icons.analytics_outlined,
                  'রিপোর্ট এবং অ্যানালিটিক্স',
                  'আর্থিক ও বুকিং রিপোর্ট ডাউনলোড করুন',
                  () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ReportSelectionScreen())),
                ),
              ],
            ),
            const SizedBox(height: 24),
            _buildSettingsSection(
              context,
              'অ্যাপ সেটিংস',
              [
                _buildSettingsTile(
                  context,
                  Icons.info_outline,
                  'অ্যাপ সম্পর্কে',
                  'ভার্সন 1.0.0',
                  null,
                ),
              ],
            ),
            const SizedBox(height: 40),
            _buildLogoutButton(context),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildProfileCard(BuildContext context, Map<String, dynamic>? user) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppTheme.textNavy,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: AppTheme.textNavy.withAlpha(50),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 35,
            backgroundColor: Colors.white.withAlpha(20),
            child: Text(
              user?['name']?[0] ?? 'U',
              style: GoogleFonts.manrope(
                fontSize: 28,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(width: 20),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  user?['name'] ?? 'ব্যবহারকারী',
                  style: GoogleFonts.manrope(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                  ),
                ),
                Text(
                  user?['email'] ?? 'ইমেইল পাওয়া যায়নি',
                  style: TextStyle(
                    fontSize: 13,
                    color: Colors.white.withAlpha(150),
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppTheme.primaryEmerald,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    user?['role'] ?? 'ADMIN',
                    style: const TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSettingsSection(BuildContext context, String title, List<Widget> children) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 8, bottom: 12),
          child: Text(
            title,
            style: GoogleFonts.manrope(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: Colors.grey.shade600,
            ),
          ),
        ),
        Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withAlpha(5),
                blurRadius: 15,
                offset: const Offset(0, 5),
              ),
            ],
          ),
          child: Column(
            children: children,
          ),
        ),
      ],
    );
  }

  Widget _buildSettingsTile(BuildContext context, IconData icon, String title, String subtitle, VoidCallback? onTap) {
    return ListTile(
      onTap: onTap,
      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      leading: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: AppTheme.backgroundSlate,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Icon(icon, color: AppTheme.primaryEmerald, size: 22),
      ),
      title: Text(
        title,
        style: GoogleFonts.manrope(fontWeight: FontWeight.w700, fontSize: 15, color: AppTheme.textNavy),
      ),
      subtitle: Text(
        subtitle,
        style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
      ),
      trailing: onTap != null ? const Icon(Icons.chevron_right, color: Colors.grey, size: 20) : null,
    );
  }

  Widget _buildLogoutButton(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 60,
      child: OutlinedButton.icon(
        onPressed: () {
          _showLogoutDialog(context);
        },
        icon: const Icon(Icons.logout, color: Colors.red),
        label: Text(
          'লগআউট করুন',
          style: GoogleFonts.manrope(
            fontSize: 16,
            fontWeight: FontWeight.w800,
            color: Colors.red,
          ),
        ),
        style: OutlinedButton.styleFrom(
          side: const BorderSide(color: Colors.red, width: 1.5),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        ),
      ),
    );
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('লগআউট নিশ্চিত করুন'),
        content: const Text('আপনি কি নিশ্চিত যে আপনি লগআউট করতে চান?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('বাতিল'),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              context.read<AuthProvider>().logout();
            },
            child: const Text('লগআউট', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}
