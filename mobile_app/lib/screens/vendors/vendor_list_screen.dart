import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../providers/vendor_provider.dart';
import '../../theme/app_theme.dart';
import 'vendor_form_screen.dart';
import 'vendor_details_screen.dart';

class VendorListScreen extends StatefulWidget {
  const VendorListScreen({super.key});

  @override
  State<VendorListScreen> createState() => _VendorListScreenState();
}

class _VendorListScreenState extends State<VendorListScreen> {
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<VendorProvider>().fetchVendors();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text('ভেন্ডর ম্যানেজমেন্ট', 
          style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 18)
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => context.read<VendorProvider>().fetchVendors(),
          ),
        ],
      ),
      body: Column(
        children: [
          _buildSearchBar(),
          Expanded(
            child: Consumer<VendorProvider>(
              builder: (context, provider, child) {
                if (provider.isLoading && provider.vendors.isEmpty) {
                  return const Center(child: CircularProgressIndicator());
                }

                if (provider.error != null && provider.vendors.isEmpty) {
                  return Center(child: Text(provider.error!));
                }

                final filteredVendors = provider.vendors.where((v) {
                  final name = v['name'].toString().toLowerCase();
                  final type = v['type'].toString().toLowerCase();
                  return name.contains(_searchQuery.toLowerCase()) || 
                         type.contains(_searchQuery.toLowerCase());
                }).toList();

                if (filteredVendors.isEmpty) {
                  return const Center(child: Text('কোন ভেন্ডর পাওয়া যায়নি।'));
                }

                return RefreshIndicator(
                  onRefresh: provider.fetchVendors,
                  child: ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    itemCount: filteredVendors.length,
                    itemBuilder: (context, index) {
                      final vendor = filteredVendors[index];
                      return _buildVendorCard(vendor);
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppTheme.primaryEmerald,
        onPressed: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const VendorFormScreen()),
        ),
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildSearchBar() {
    return Container(
      padding: const EdgeInsets.all(16),
      child: TextField(
        onChanged: (value) => setState(() => _searchQuery = value),
        decoration: InputDecoration(
          hintText: 'ভেন্ডর খুঁজুন...',
          prefixIcon: const Icon(Icons.search),
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: BorderSide.none,
          ),
          contentPadding: const EdgeInsets.symmetric(vertical: 0),
        ),
      ),
    );
  }

  Widget _buildVendorCard(Map<String, dynamic> vendor) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
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
          backgroundColor: AppTheme.primaryEmerald.withAlpha(20),
          child: Text(
            vendor['name'][0].toUpperCase(),
            style: const TextStyle(color: AppTheme.primaryEmerald, fontWeight: FontWeight.bold),
          ),
        ),
        title: Text(
          vendor['name'],
          style: GoogleFonts.manrope(fontWeight: FontWeight.w700, color: AppTheme.textNavy),
        ),
        subtitle: Text(
          'টাইপ: ${vendor['type']} | হার: ${vendor['commission_rate'] ?? 0}%',
          style: TextStyle(color: Colors.grey[600], fontSize: 12),
        ),
        trailing: const Icon(Icons.chevron_right, color: Colors.grey),
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => VendorDetailsScreen(vendorId: vendor['id']),
          ),
        ),
      ),
    );
  }
}
