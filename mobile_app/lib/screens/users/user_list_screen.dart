import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/user_provider.dart';
import '../../theme/app_theme.dart';
import 'user_form_screen.dart';

class UserListScreen extends StatefulWidget {
  const UserListScreen({super.key});

  @override
  State<UserListScreen> createState() => _UserListScreenState();
}

class _UserListScreenState extends State<UserListScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<UserProvider>().fetchUsers();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text('ব্যবহারকারী ব্যবস্থাপনা', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        actions: [
          IconButton(
            icon: const Icon(Icons.add_circle_outline, color: AppTheme.primaryEmerald),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const UserFormScreen())),
          ),
        ],
      ),
      body: Consumer<UserProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.users.isEmpty) {
            return const Center(child: CircularProgressIndicator(color: AppTheme.primaryEmerald));
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchUsers(isPullToRefresh: true),
            child: provider.users.isEmpty
                ? ListView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    children: [
                      SizedBox(height: MediaQuery.of(context).size.height * 0.2),
                      _buildEmptyState(),
                    ],
                  )
                : ListView.separated(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(20),
                    itemCount: provider.users.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 16),
                    itemBuilder: (context, index) {
                      final user = provider.users[index];
                      return _buildUserCard(context, user);
                    },
                  ),
          );
        },
      ),
    );
  }

  Widget _buildUserCard(BuildContext context, dynamic user) {
    final roles = (user['roles'] as List? ?? []).map((r) => r['name'].toString()).join(', ');
    
    return Container(
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
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        leading: CircleAvatar(
          radius: 25,
          backgroundColor: AppTheme.primaryEmerald.withAlpha(20),
          child: Text(
            user['name'][0].toUpperCase(),
            style: GoogleFonts.manrope(fontWeight: FontWeight.w800, color: AppTheme.primaryEmerald),
          ),
        ),
        title: Text(
          user['name'],
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16, color: AppTheme.textNavy),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 4),
            Text(user['email'], style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
            const SizedBox(height: 4),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(
                color: AppTheme.textNavy.withAlpha(10),
                borderRadius: BorderRadius.circular(6),
              ),
              child: Text(
                roles.toUpperCase(),
                style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: AppTheme.textNavy),
              ),
            ),
          ],
        ),
        trailing: PopupMenuButton(
          icon: const Icon(Icons.more_vert, color: Colors.grey),
          itemBuilder: (context) => [
            const PopupMenuItem(value: 'edit', child: Text('এডিট করুন')),
            const PopupMenuItem(value: 'delete', child: Text('মুছে ফেলুন', style: TextStyle(color: Colors.red))),
          ],
          onSelected: (val) {
            if (val == 'edit') {
              Navigator.push(context, MaterialPageRoute(builder: (_) => UserFormScreen(user: user)));
            } else if (val == 'delete') {
              _showDeleteConfirm(context, user);
            }
          },
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.person_off_outlined, size: 80, color: Colors.grey.shade300),
          const SizedBox(height: 16),
          Text(
            'কোন ব্যবহারকারী পাওয়া যায়নি',
            style: GoogleFonts.manrope(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey),
          ),
        ],
      ),
    );
  }

  void _showDeleteConfirm(BuildContext context, dynamic user) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('নিশ্চিত করুন'),
        content: Text('${user['name']} কে কি সত্যি মুছে ফেলতে চান?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('না')),
          TextButton(
            onPressed: () async {
              Navigator.pop(ctx);
              final res = await context.read<UserProvider>().deleteUser(user['id']);
              if (context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message'] ?? 'মুছে ফেলা হয়েছে'),
                    backgroundColor: res['success'] == true ? AppTheme.primaryEmerald : Colors.redAccent,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
              }
            },
            child: const Text('হ্যাঁ, মুছুন', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}
