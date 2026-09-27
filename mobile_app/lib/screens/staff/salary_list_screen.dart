import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../providers/staff_provider.dart';
import '../../theme/app_theme.dart';
import 'salary_form_screen.dart';

class SalaryListScreen extends StatefulWidget {
  const SalaryListScreen({super.key});

  @override
  State<SalaryListScreen> createState() => _SalaryListScreenState();
}

class _SalaryListScreenState extends State<SalaryListScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<StaffProvider>().fetchAllSalaries();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('বেতন ম্যানেজমেন্ট', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        centerTitle: true,
      ),
      body: Consumer<StaffProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading && provider.salaryList.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.salaryList.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.payments_outlined, size: 64, color: Colors.grey.shade300),
                  const SizedBox(height: 16),
                  Text('কোন বেতন প্রদান পাওয়া যায়নি', style: TextStyle(color: Colors.grey.shade500)),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchAllSalaries(),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: provider.salaryList.length,
              itemBuilder: (context, index) {
                final salary = provider.salaryList[index];
                final staff = provider.staffList.firstWhere(
                  (s) => s.id == salary.employeeId,
                  orElse: () => provider.staffList.isNotEmpty ? provider.staffList[0] : null as dynamic, // Fallback
                );

                return Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 10, offset: const Offset(0, 4)),
                    ],
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: AppTheme.primaryEmerald.withAlpha(20),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.person_outline, color: AppTheme.primaryEmerald),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              salary.month + " " + salary.year.toString(),
                              style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: Colors.grey),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              staff.name,
                              style: GoogleFonts.manrope(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.textNavy),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'প্রদান: ${DateFormat('dd MMM yyyy').format(DateTime.parse(salary.paymentDate))}',
                              style: const TextStyle(fontSize: 12, color: Colors.grey),
                            ),
                          ],
                        ),
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            '৳${salary.amount.toStringAsFixed(0)}',
                            style: GoogleFonts.manrope(
                              fontWeight: FontWeight.w900,
                              fontSize: 18,
                              color: AppTheme.primaryEmerald,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.green.shade50,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: const Text(
                              'পরিশোধিত',
                              style: TextStyle(color: Colors.green, fontSize: 10, fontWeight: FontWeight.bold),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          final result = await showDialog(
            context: context,
            builder: (context) => const SalaryFormScreen(),
          );
          if (result == true) {
            context.read<StaffProvider>().fetchAllSalaries();
          }
        },
        backgroundColor: AppTheme.textNavy,
        icon: const Icon(Icons.add, color: Colors.white),
        label: const Text('বেতন প্রদান', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }
}
