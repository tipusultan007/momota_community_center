import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:share_plus/share_plus.dart';
import 'package:open_filex/open_filex.dart';
import 'package:intl/intl.dart';
import '../../models/booking.dart';
import '../../models/booking_item.dart';
import '../../providers/booking_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../providers/accounting_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/asset_assignment.dart';
import '../../providers/asset_provider.dart';
import 'booking_form_screen.dart';
import 'components/payment_dialog.dart';

class BookingDetailsScreen extends StatefulWidget {
  final Booking booking;

  const BookingDetailsScreen({super.key, required this.booking});

  @override
  State<BookingDetailsScreen> createState() => _BookingDetailsScreenState();
}

class _BookingDetailsScreenState extends State<BookingDetailsScreen> {
  late Booking _currentBooking;

  @override
  void initState() {
    super.initState();
    _currentBooking = widget.booking;
    Future.microtask(() => context.read<AssetProvider>().fetchAssets());
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<BookingProvider>(
      builder: (context, provider, child) {
        // Find the specific booking in the updated list if possible
        final booking = provider.bookings.firstWhere(
          (b) => b.id == _currentBooking.id,
          orElse: () => _currentBooking,
        );
        _currentBooking = booking; // Keep local sync for state-dependent logic

        return Scaffold(
          backgroundColor: AppTheme.backgroundSlate,
          appBar: AppBar(
            title: Text('বুকিং বিস্তারিত', style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
            actions: [
              IconButton(
                icon: const Icon(Icons.edit_outlined),
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => BookingFormScreen(booking: _currentBooking)),
                ),
              ),
            ],
          ),
          body: SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                _buildStatusHeader(),
                const SizedBox(height: 24),
                _buildCustomerCard(),
                const SizedBox(height: 24),
                _buildItemsHeader(),
                ..._currentBooking.items.map((item) => _buildItemCard(item)),
                const SizedBox(height: 24),
                _buildPaymentHistory(),
                const SizedBox(height: 24),
                _buildFinancialCard(),
                if (_currentBooking.allServerCost > 0) ...[
                  const SizedBox(height: 24),
                  _buildServerPayoutCard(),
                ],
                const SizedBox(height: 24),
                _buildInvoiceActions(),
                if (_currentBooking.notes != null && _currentBooking.notes!.isNotEmpty) ...[
                  const SizedBox(height: 24),
                  _buildNotesCard(),
                ],
                const SizedBox(height: 32),
                _buildActionButtons(context),
                const SizedBox(height: 60),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildStatusHeader() {
    Color color;
    String label;
    IconData icon;

    switch (_currentBooking.status.toLowerCase()) {
      case 'confirmed':
        color = Colors.green;
        label = 'বুকিং নিশ্চিত';
        icon = Icons.check_circle_outline;
        break;
      case 'cancelled':
        color = Colors.red;
        label = 'বুকিং বাতিল';
        icon = Icons.cancel_outlined;
        break;
      case 'completed':
        color = Colors.blue;
        label = 'সম্পন্ন হয়েছে';
        icon = Icons.task_alt;
        break;
      default:
        color = Colors.orange;
        label = 'পেন্ডিং বুকিং';
        icon = Icons.hourglass_empty;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      decoration: BoxDecoration(
        color: color.withAlpha(15),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: color.withAlpha(30)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: color.withAlpha(30), shape: BoxShape.circle),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 16),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: GoogleFonts.manrope(color: color, fontWeight: FontWeight.w800, fontSize: 18)),
              Text('আইডি: #${_currentBooking.id}', style: const TextStyle(color: Colors.grey, fontSize: 12, fontWeight: FontWeight.bold)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildCustomerCard() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.person_pin, color: AppTheme.primaryEmerald, size: 22),
              const SizedBox(width: 10),
              Text('গ্রাহকের তথ্য', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16)),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider(height: 1)),
          _infoRow(Icons.person_outline, 'পুরো নাম', _currentBooking.customerName),
          const SizedBox(height: 16),
          _infoRow(Icons.phone_outlined, 'মোবাইল নম্বর', _currentBooking.customerPhone),
          const SizedBox(height: 16),
          _infoRow(Icons.location_on_outlined, 'ঠিকানা', _currentBooking.customerAddress ?? 'তথ্য পাওয়া যায়নি'),
        ],
      ),
    );
  }

  Widget _buildItemsHeader() {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 16),
      child: Row(
        children: [
          const Icon(Icons.event_note_outlined, color: Colors.grey, size: 20),
          const SizedBox(width: 10),
          Text('বুকিং আইটেম (${_currentBooking.items.length})', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16, color: Colors.grey.shade700)),
        ],
      ),
    );
  }

  Widget _buildItemCard(BookingItem item) {
    final eventTitle = item.eventType.isNotEmpty ? item.eventType : 'ইভেন্ট / অনুষ্ঠান';
    IconData eventIcon = Icons.celebration_rounded;
    final lower = item.eventType.toLowerCase();
    if (lower.contains('corporate') || lower.contains('অফিস') || lower.contains('মিটিং') || lower.contains('office') || lower.contains('meeting') || lower.contains('কনফারেন্স') || lower.contains('সেমিনার') || lower.contains('এজিএম') || lower.contains('প্রেস')) {
      eventIcon = Icons.business_center_rounded;
    } else if (lower.contains('wedding') || lower.contains('বিয়ে') || lower.contains('বিবাহ') || lower.contains('reception') || lower.contains('আকদ') || lower.contains('এনগেজমেন্ট') || lower.contains('বৌভাত') || lower.contains('ওয়ালিমা') || lower.contains('বিবাহবার্ষিকী')) {
      eventIcon = Icons.favorite_rounded;
    } else if (lower.contains('birthday') || lower.contains('জন্মদিন') || lower.contains('আকিকা')) {
      eventIcon = Icons.cake_rounded;
    } else if (lower.contains('হলুদ') || lower.contains('মেহেদি') || lower.contains('holud') || lower.contains('mehendi')) {
      eventIcon = Icons.celebration_rounded;
    } else if (lower.contains('ইফতার') || lower.contains('দোয়া') || lower.contains('মিলাদ')) {
      eventIcon = Icons.nights_stay_rounded;
    } else if (lower.contains('সাংস্কৃতিক') || lower.contains('কনসার্ট')) {
      eventIcon = Icons.music_note_rounded;
    } else if (lower.contains('সমাবর্তন') || lower.contains('র্যাগ')) {
      eventIcon = Icons.school_rounded;
    } else if (lower.contains('মেলা') || lower.contains('প্রদর্শনী')) {
      eventIcon = Icons.storefront_rounded;
    } else if (lower.contains('পুনর্মিলনী') || lower.contains('রিইউনিয়ন')) {
      eventIcon = Icons.groups_rounded;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppTheme.primaryEmerald.withAlpha(20)),
      ),
      child: ExpansionTile(
        tilePadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        title: Text(
          eventTitle, 
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16, color: AppTheme.textNavy),
        ),
        subtitle: Text(
          '${DateFormat('dd MMM yyyy').format(DateTime.parse(item.eventDate))} • ${(item.slot == 'night' || item.slot == 'evening') ? 'NIGHT (রাত)' : 'DAY (দিন)'}',
          style: const TextStyle(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 13),
        ),
        leading: Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(color: AppTheme.primaryEmerald.withAlpha(15), borderRadius: BorderRadius.circular(15)),
          child: Icon(eventIcon, color: AppTheme.primaryEmerald, size: 22),
        ),
        children: [
          Padding(
            padding: const EdgeInsets.all(24),
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: AppTheme.backgroundSlate, borderRadius: BorderRadius.circular(16)),
              child: Column(
                children: [
                  _detailRow('ইভেন্ট ধরণ', item.eventType),
                  _detailRow('মেহমান সংখ্যা', '${item.guestCount} জন'),
                  _detailRow('টেবিল সংখ্যা', '${item.tableCount} টি'),
                  _detailRow('কর্মী (পরিবেশনকারী)', '${item.serverCount} জন (@ ৳${item.serverRate.toStringAsFixed(0)})'),
                  if (item.serverCount > 0)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 4),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('সার্ভার খরচ', style: TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.bold)),
                          Row(
                            children: [
                              Text('৳ ${(item.serverCount * item.serverRate).toStringAsFixed(0)} ', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppTheme.textNavy)),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: item.isServerIncluded ? Colors.green.withAlpha(25) : Colors.orange.withAlpha(25),
                                  borderRadius: BorderRadius.circular(6),
                                  border: Border.all(color: item.isServerIncluded ? Colors.green.shade400 : Colors.orange.shade400),
                                ),
                                child: Text(
                                  item.isServerIncluded ? '✓ বিলে অন্তর্ভুক্ত' : '* বিলে অন্তর্ভুক্ত নয় (সরাসরি প্রদেয়)',
                                  style: TextStyle(
                                    fontSize: 10,
                                    fontWeight: FontWeight.bold,
                                    color: item.isServerIncluded ? Colors.green.shade800 : Colors.deepOrange.shade800,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  const Divider(height: 24),
                  _detailRow('ভাড়া (বেস)', '৳ ${item.basePrice}'),
                  if (item.isAc) _detailRow('এসি চার্জ', '৳ ${item.acPrice}'),
                  if (item.extraSound) ...[
                    _detailRow('সাউন্ড সিস্টেম', '৳ ${item.soundPrice}'),
                    if (item.soundVendorName != null) _detailRow('সাউন্ড ভেন্ডর', item.soundVendorName!, isSubRow: true),
                  ],
                  if (item.extraGenerator) ...[
                    _detailRow('জেনারেটর', '৳ ${item.generatorPrice}'),
                    if (item.generatorVendorName != null) _detailRow('জেনারেটর ভেন্ডর', item.generatorVendorName!, isSubRow: true),
                  ],
                  if (item.extraDecoration) ...[
                    _detailRow('ডেকোরেশন', '৳ ${item.decorationPrice}'),
                    if (item.decorationVendorName != null) _detailRow('ডেকোরেশন ভেন্ডর', item.decorationVendorName!, isSubRow: true),
                  ],
                  const Divider(height: 24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('আইটেম মোট', style: TextStyle(fontWeight: FontWeight.bold)),
                      Text('৳ ${item.subTotal}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: AppTheme.primaryEmerald)),
                    ],
                  ),
                  const Divider(height: 32),
                  _buildAssetSection(item),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAssetSection(BookingItem item) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.inventory_2_outlined, color: Colors.blue, size: 18),
                const SizedBox(width: 8),
                Text('মালামাল/ইনভেন্টরি', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 14)),
              ],
            ),
            TextButton.icon(
              onPressed: () => _showAssignAssetDialog(item),
              icon: const Icon(Icons.add, size: 16),
              label: const Text('বরাদ্দ দিন', style: TextStyle(fontSize: 12)),
              style: TextButton.styleFrom(visualDensity: VisualDensity.compact),
            ),
          ],
        ),
        const SizedBox(height: 8),
        if (item.assetAssignments.isEmpty)
          const Center(
            child: Padding(
              padding: EdgeInsets.symmetric(vertical: 16),
              child: Text('কোনো মালামাল বরাদ্দ দেওয়া হয়নি', style: TextStyle(color: Colors.grey, fontSize: 12)),
            ),
          )
        else
          ...item.assetAssignments.map((a) => _buildAssetAssignmentRow(a)),
      ],
    );
  }

  Widget _buildAssetAssignmentRow(AssetAssignment a) {
    final isFullyReturned = a.quantityIn + a.damagedQuantity >= a.quantityOut;
    
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.withAlpha(15)),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(a.assetName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                    Text(
                      'আউট: ${a.quantityOut} | ইন: ${a.quantityIn} | ক্ষতিগ্রস্ত: ${a.damagedQuantity}',
                      style: const TextStyle(color: Colors.grey, fontSize: 11),
                    ),
                  ],
                ),
              ),
              if (!isFullyReturned)
                IconButton(
                  onPressed: () => _showReturnAssetDialog(a),
                  icon: const Icon(Icons.keyboard_return, color: Colors.blue, size: 20),
                  visualDensity: VisualDensity.compact,
                )
              else
                const Icon(Icons.check_circle, color: Colors.green, size: 20),
            ],
          ),
          if (a.notes != null && a.notes!.isNotEmpty)
            Padding(
              padding: const EdgeInsets.only(top: 4),
              child: Align(
                alignment: Alignment.centerLeft,
                child: Text('নোট: ${a.notes}', style: const TextStyle(color: Colors.redAccent, fontSize: 10, fontStyle: FontStyle.italic)),
              ),
            ),
        ],
      ),
    );
  }

  void _showAssignAssetDialog(BookingItem item) async {
    final assetProvider = context.read<AssetProvider>();
    if (assetProvider.assets.isEmpty) {
      await assetProvider.fetchAssets();
    }

    if (mounted) {
      showDialog(
        context: context,
        builder: (context) {
          int? selectedAssetId;
          final quantityController = TextEditingController();

          return StatefulBuilder(
            builder: (context, setState) {
              return AlertDialog(
                title: Text('মালামাল বরাদ্দ দিন', style: GoogleFonts.manrope(fontWeight: FontWeight.bold)),
                content: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    DropdownButtonFormField<int>(
                      decoration: const InputDecoration(labelText: 'মালামাল নির্বাচন করুন'),
                      value: selectedAssetId,
                      items: assetProvider.assets.map((a) {
                        return DropdownMenuItem(
                          value: a.id,
                          child: Text('${a.name} (স্টক: ${a.availableStock})'),
                        );
                      }).toList(),
                      onChanged: (val) => setState(() => selectedAssetId = val),
                    ),
                    const SizedBox(height: 16),
                    TextField(
                      controller: quantityController,
                      decoration: const InputDecoration(labelText: 'পরিমাণ'),
                      keyboardType: TextInputType.number,
                    ),
                  ],
                ),
                actions: [
                  TextButton(onPressed: () => Navigator.pop(context), child: const Text('বাতিল')),
                  ElevatedButton(
                    onPressed: () async {
                      if (selectedAssetId == null || quantityController.text.isEmpty) return;
                      final success = await context.read<BookingProvider>().assignAsset(item.id!, {
                        'asset_id': selectedAssetId,
                        'quantity': int.parse(quantityController.text),
                      });
                      if (success && mounted) {
                        Navigator.pop(context);
                      }
                    },
                    child: const Text('নিশ্চিত করুন'),
                  ),
                ],
              );
            },
          );
        },
      );
    }
  }

  void _showReturnAssetDialog(AssetAssignment a) {
    showDialog(
      context: context,
      builder: (context) {
        final qInController = TextEditingController(text: (a.quantityOut - a.quantityIn - a.damagedQuantity).toString());
        final qDamagedController = TextEditingController(text: '0');
        final notesController = TextEditingController();

        return AlertDialog(
          title: Text('মালামাল ফেরত নিন', style: GoogleFonts.manrope(fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text('আইটেম: ${a.assetName}', style: const TextStyle(fontWeight: FontWeight.bold)),
              const SizedBox(height: 16),
              TextField(
                controller: qInController,
                decoration: const InputDecoration(labelText: 'ভালো অবস্থায় ফেরত'),
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 12),
              TextField(
                controller: qDamagedController,
                decoration: const InputDecoration(labelText: 'ক্ষতিগ্রস্ত সংখ্যা'),
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 12),
              TextField(
                controller: notesController,
                decoration: const InputDecoration(labelText: 'নোট (ঐচ্ছিক)'),
              ),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('বাতিল')),
            ElevatedButton(
              onPressed: () async {
                final success = await context.read<BookingProvider>().returnAsset(a.id, {
                  'quantity_in': int.parse(qInController.text),
                  'damaged_quantity': int.parse(qDamagedController.text),
                  'notes': notesController.text,
                });
                if (success && mounted) {
                  Navigator.pop(context);
                }
              },
              child: const Text('ফেরত নিন'),
            ),
          ],
        );
      },
    );
  }

  Widget _buildPaymentHistory() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  const Icon(Icons.history, color: Colors.blue, size: 22),
                  const SizedBox(width: 10),
                  Text('পেমেন্ট ইতিহাস', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16)),
                ],
              ),
              Text('${_currentBooking.payments.length} টি পেমেন্ট', style: const TextStyle(fontSize: 11, color: Colors.grey, fontWeight: FontWeight.bold)),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider(height: 1)),
          if (_currentBooking.payments.isEmpty)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 20),
              child: Center(child: Text('কোনো পেমেন্ট পাওয়া যায়নি', style: TextStyle(color: Colors.grey, fontSize: 13))),
            )
          else
            ..._currentBooking.payments.map((p) => _paymentRow(p)),
        ],
      ),
    );
  }

  Widget _paymentRow(Map<String, dynamic> p) {
    final dateStr = p['date'] != null ? DateFormat('dd MMM yyyy').format(DateTime.parse(p['date'].toString())) : 'N/A';
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: Colors.blue.withAlpha(10), borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.receipt_long_outlined, color: Colors.blue, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('৳ ${p['amount']}', style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textNavy)),
                Text(p['description'] ?? 'বুকিং পেমেন্ট', style: const TextStyle(color: Colors.grey, fontSize: 11), maxLines: 1, overflow: TextOverflow.ellipsis),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Text(dateStr, style: const TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildFinancialCard() {
    final paidPercent = _currentBooking.totalAmount > 0 
        ? (_currentBooking.paidAmount / _currentBooking.totalAmount) 
        : 0.0;

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppTheme.textNavy,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: AppTheme.textNavy.withAlpha(40), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('অর্থনৈতিক হিসাব', style: GoogleFonts.manrope(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 18)),
              const Icon(Icons.account_balance_wallet_outlined, color: Colors.white, size: 24),
            ],
          ),
          const SizedBox(height: 24),
          _financialRow('মোট বিল', '৳ ${_currentBooking.totalAmount}', isBold: true),
          if (_currentBooking.totalServerCost > 0) ...[
            const SizedBox(height: 8),
            _financialRow('হল আয় (নেট)', '৳ ${(_currentBooking.totalAmount - _currentBooking.totalServerCost).toStringAsFixed(0)}', color: Colors.white70),
            const SizedBox(height: 6),
            _financialRow('পরিবেশন খরচ', '৳ ${_currentBooking.totalServerCost.toStringAsFixed(0)}', color: Colors.amberAccent),
          ],
          const SizedBox(height: 12),
          _financialRow('পরিশোধিত', '৳ ${_currentBooking.paidAmount}', color: Colors.greenAccent),
          const SizedBox(height: 16),
          ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: LinearProgressIndicator(
              value: paidPercent,
              backgroundColor: Colors.white.withAlpha(30),
              valueColor: const AlwaysStoppedAnimation<Color>(Colors.greenAccent),
              minHeight: 8,
            ),
          ),
          const SizedBox(height: 20),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(color: Colors.white.withAlpha(15), borderRadius: BorderRadius.circular(15)),
            child: _financialRow('বকেয়া (Due)', '৳ ${_currentBooking.dueAmount}', color: Colors.redAccent, isBold: true, isLarge: true),
          ),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(color: AppTheme.backgroundSlate, borderRadius: BorderRadius.circular(10)),
          child: Icon(icon, size: 18, color: Colors.grey.shade600),
        ),
        const SizedBox(width: 16),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: TextStyle(color: Colors.grey.shade500, fontSize: 11, fontWeight: FontWeight.bold)),
            Text(value, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppTheme.textNavy)),
          ],
        ),
      ],
    );
  }

  Widget _detailRow(String label, String value, {bool isSubRow = false}) {
    return Padding(
      padding: EdgeInsets.only(top: 4, bottom: 4, left: isSubRow ? 16 : 0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(
            fontSize: isSubRow ? 12 : 13, 
            color: isSubRow ? Colors.grey[600] : Colors.grey, 
            fontWeight: isSubRow ? FontWeight.normal : FontWeight.bold
          )),
          Text(value, style: TextStyle(
            fontWeight: FontWeight.bold, 
            color: AppTheme.textNavy,
            fontSize: isSubRow ? 12 : 14,
          )),
        ],
      ),
    );
  }

  Widget _financialRow(String label, String value, {Color? color, bool isBold = false, bool isLarge = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(color: Colors.white70, fontWeight: FontWeight.w600, fontSize: 14)),
        Text(value, style: TextStyle(
          color: color ?? Colors.white,
          fontWeight: FontWeight.w900,
          fontSize: isLarge ? 22 : 16,
        )),
      ],
    );
  }

  Widget _buildServerPayoutCard() {
    final isPaid = _currentBooking.serverPayoutStatus == 'paid';
    final totalServerCost = _currentBooking.totalServerCost;
    final excludedServerCost = _currentBooking.excludedServerCost;

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Colors.orange.withAlpha(60), width: 1.5),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(color: Colors.orange.withAlpha(30), shape: BoxShape.circle),
                child: const Icon(Icons.soup_kitchen_outlined, color: Colors.deepOrange, size: 20),
              ),
              const SizedBox(width: 10),
              Text('পরিবেশনকারী বিল ব্যবস্থাপনা', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16)),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider(height: 1)),
          
          if (totalServerCost > 0) ...[
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('বিলে অন্তর্ভুক্ত পরিবেশন খরচ:', style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 13)),
                Text('৳ ${totalServerCost.toStringAsFixed(0)}', style: GoogleFonts.manrope(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textNavy)),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('পেমেন্ট স্ট্যাটাস:', style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 13)),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: isPaid ? Colors.green.withAlpha(20) : Colors.orange.withAlpha(20),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: isPaid ? Colors.green : Colors.orange),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(isPaid ? Icons.check_circle : Icons.pending, size: 14, color: isPaid ? Colors.green : Colors.orange),
                      const SizedBox(width: 4),
                      Text(
                        isPaid ? 'পরিশোধিত (Paid)' : 'অপরিশোধিত (Pending)',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: isPaid ? Colors.green.shade800 : Colors.deepOrange.shade800),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            if (isPaid) ...[
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.green.withAlpha(15),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.green.withAlpha(50)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (_currentBooking.serverPayoutDate != null)
                      Text(
                        'পরিশোধের তারিখ: ${DateFormat('dd MMM yyyy, hh:mm a').format(DateTime.tryParse(_currentBooking.serverPayoutDate!) ?? DateTime.now())}',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Colors.green),
                      ),
                    const SizedBox(height: 4),
                    Text(
                      'অ্যাকাউন্টিংয়ে ৳ ${totalServerCost.toStringAsFixed(0)} খরচ (Expense) হিসেবে রেকর্ড রয়েছে।',
                      style: TextStyle(fontSize: 11, color: Colors.grey.shade700),
                    ),
                  ],
                ),
              ),
            ] else ...[
              const Text(
                'পরিবেশনকারীদের বিল পরিশোধ নিশ্চিত করলে এটি স্বয়ংক্রিয়ভাবে অ্যাকাউন্টিংয়ে ব্যয় (Expense) হিসেবে যুক্ত হবে।',
                style: TextStyle(fontSize: 12, color: Colors.grey),
              ),
              const SizedBox(height: 12),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.payments_outlined, size: 18),
                  label: const Text('পরিবেশনকারীদের বিল পরিশোধ করুন', style: TextStyle(fontWeight: FontWeight.bold)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.orange.shade700,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () => _confirmPayServers(context),
                ),
              ),
            ],
          ],

          if (excludedServerCost > 0) ...[
            const SizedBox(height: 12),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey.withAlpha(15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                '* বিলে অন্তর্ভুক্ত নয়: ৳ ${excludedServerCost.toStringAsFixed(0)} (গ্রাহক কর্তৃক সরাসরি পরিবেশনকারীদের প্রদেয়)',
                style: TextStyle(fontSize: 12, color: Colors.grey.shade700, fontStyle: FontStyle.italic),
              ),
            ),
          ],
        ],
      ),
    );
  }

  void _confirmPayServers(BuildContext context) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('বিল পরিশোধ নিশ্চিতকরণ'),
        content: Text('আপনি কি নিশ্চিত যে পরিবেশনকারীদের ৳ ${_currentBooking.totalServerCost.toStringAsFixed(0)} পরিশোধ করতে চান? এটি অ্যাকাউন্টিংয়ে খরচ (Expense) হিসেবে রেকর্ড হবে।'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('না')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.orange.shade700),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('হ্যাঁ, পরিশোধ করুন', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final success = await context.read<BookingProvider>().payServers(_currentBooking.id);
      if (success && mounted) {
        context.read<AccountingProvider>().fetchTransactions(refresh: true);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: Colors.green,
            content: Text('পরিবেশনকারীদের বিল সফলভাবে পরিশোধ করা হয়েছে এবং খরচে অন্তর্ভুক্ত হয়েছে।'),
          ),
        );
      } else if (mounted) {
        final err = context.read<BookingProvider>().lastErrorMessage ?? 'বিল পরিশোধ ব্যর্থ হয়েছে।';
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(err)),
        );
      }
    }
  }

  Widget _buildInvoiceActions() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.description_outlined, color: AppTheme.primaryEmerald, size: 22),
              const SizedBox(width: 10),
              Text('ইনভয়েস ও রিসিট', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16)),
            ],
          ),
          const SizedBox(height: 20),
          Row(
            children: [
              _invoiceActionItem(Icons.download_outlined, 'ডাউনলোড', () => _handleInvoiceAction('download')),
              _invoiceActionItem(Icons.share_outlined, 'শেয়ার', () => _handleInvoiceAction('share')),
              _invoiceActionItem(Icons.print_outlined, 'প্রিন্ট', () => _handleInvoiceAction('print')),
              _invoiceActionItem(Icons.send, 'হোয়াটসঅ্যাপ', () => _handleInvoiceAction('whatsapp'), color: Colors.green),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildNotesCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.note_alt_outlined, color: Colors.orange, size: 22),
              const SizedBox(width: 10),
              Text('অতিরিক্ত নোট', style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 16)),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            _currentBooking.notes!,
            style: const TextStyle(fontSize: 14, color: AppTheme.textNavy, height: 1.5),
          ),
        ],
      ),
    );
  }

  Widget _invoiceActionItem(IconData icon, String label, VoidCallback onTap, {Color? color}) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: (color ?? AppTheme.primaryEmerald).withAlpha(15),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color ?? AppTheme.primaryEmerald, size: 24),
            ),
            const SizedBox(height: 8),
            Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey), textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  Future<void> _handleInvoiceAction(String action) async {
    final provider = context.read<BookingProvider>();
    final label = _currentBooking.customerName.replaceAll(' ', '_');
    
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('ইনভয়েস তৈরি করা হচ্ছে...'), duration: Duration(seconds: 2)),
    );

    final path = await provider.downloadInvoice(_currentBooking.id, label);
    
    if (path == null) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('ইনভয়েস ডাউনলোড করতে সমস্যা হয়েছে।')));
      }
      return;
    }

    if (mounted) {
      switch (action) {
        case 'download':
          await OpenFilex.open(path);
          break;
        case 'share':
        case 'whatsapp':
          await Share.shareXFiles([XFile(path)], text: 'Booking Invoice for ${_currentBooking.customerName}');
          break;
        case 'print':
          await OpenFilex.open(path); // Print usually handled by system viewer or specialized package
          break;
      }
    }
  }

  Widget _buildActionButtons(BuildContext context) {
    return Column(
      children: [
        if (_currentBooking.dueAmount > 0)
          SizedBox(
            width: double.infinity,
            height: 64,
            child: ElevatedButton.icon(
              icon: const Icon(Icons.add_card, color: Colors.white),
              onPressed: () => _takePayment(context),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryEmerald,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                elevation: 0,
              ),
              label: const Text('পেমেন্ট গ্রহণ করুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 16)),
            ),
          ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: OutlinedButton(
                onPressed: () => _updateStatus(context, 'cancelled'),
                style: OutlinedButton.styleFrom(
                  foregroundColor: Colors.red,
                  side: const BorderSide(color: Colors.red, width: 2),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                ),
                child: const Text('বুকিং বাতিল', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: ElevatedButton(
                onPressed: () => _updateStatus(context, 'completed'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.textNavy,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                  elevation: 0,
                ),
                child: const Text('সম্পন্ন করুন', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
        const SizedBox(height: 24),
        TextButton.icon(
          onPressed: () => _confirmDelete(context),
          icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
          label: const Text('বুকিংটি মুছে ফেলুন', style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
        ),
      ],
    );
  }

  Future<void> _takePayment(BuildContext context) async {
    final success = await showDialog<bool>(
      context: context,
      builder: (_) => PaymentDialog(
        bookingId: _currentBooking.id,
        dueAmount: _currentBooking.dueAmount,
      ),
    );

    if (success == true && mounted) {
      // Refresh after payment - ideally would fetch from provider
      Navigator.pop(context);
    }
  }

  Future<void> _updateStatus(BuildContext context, String status) async {
    final success = await context.read<BookingProvider>().updateStatus(_currentBooking.id, status);
    if (success && mounted) {
      Navigator.pop(context);
    }
  }

  Future<void> _confirmDelete(BuildContext context) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('বুকিং মুছুন'),
        content: const Text('আপনি কি নিশ্চিত যে এই বুকিংটি মুছে ফেলতে চান?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('না')),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('হ্যাঁ', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final success = await context.read<BookingProvider>().deleteBooking(_currentBooking.id);
      if (success && mounted) {
        context.read<DashboardProvider>().fetchDashboard(isPullToRefresh: true);
        context.read<AccountingProvider>().fetchTransactions(refresh: true);
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('বুকিং সফলভাবে মুছে ফেলা হয়েছে।')));
      }
    }
  }
}
