import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/staff_provider.dart';
import '../../theme/app_theme.dart';

class StaffStatusScreen extends StatefulWidget {
  const StaffStatusScreen({super.key});

  @override
  State<StaffStatusScreen> createState() => _StaffStatusScreenState();
}

class _StaffStatusScreenState extends State<StaffStatusScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => context.read<StaffProvider>().fetchStaff());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('স্টাফ স্ট্যাটাস', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
      ),
      body: Consumer<StaffProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.staffList.isEmpty) {
            return const Center(child: Text('কোনো স্টাফ পাওয়া যায়নি।'));
          }

          return RefreshIndicator(
            onRefresh: provider.fetchStaff,
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: provider.staffList.length,
              itemBuilder: (context, index) {
                final staff = provider.staffList[index];
                final isActive = staff.status == 'active';

                return Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withAlpha(5),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                    leading: CircleAvatar(
                      radius: 24,
                      backgroundColor: isActive ? AppTheme.primaryEmerald.withAlpha(25) : Colors.grey.withAlpha(25),
                      child: Icon(
                        Icons.person_outline,
                        color: isActive ? AppTheme.primaryEmerald : Colors.grey,
                      ),
                    ),
                    title: Text(
                      staff.name,
                      style: GoogleFonts.manrope(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    subtitle: Text(
                      staff.designation,
                      style: const TextStyle(color: Colors.grey),
                    ),
                    trailing: Switch.adaptive(
                      value: isActive,
                      activeColor: AppTheme.primaryEmerald,
                      activeTrackColor: AppTheme.primaryEmerald.withAlpha(100),
                      onChanged: (value) async {
                        final newStatus = value ? 'active' : 'inactive';
                        final success = await provider.updateStatus(staff.id, newStatus);
                        if (success && mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              behavior: SnackBarBehavior.floating,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              content: Text('${staff.name} এখন ${value ? 'সক্রিয়' : 'নিষ্ক্রিয়'}'),
                            ),
                          );
                        }
                      },
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
