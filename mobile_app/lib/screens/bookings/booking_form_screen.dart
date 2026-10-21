import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../models/booking.dart';
import '../../models/booking_item.dart';
import '../../providers/booking_provider.dart';
import '../../providers/customer_provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/vendor_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../api/api_service.dart';
import '../../theme/app_theme.dart';

class BookingFormScreen extends StatefulWidget {
  final Booking? booking;
  final String? initialDate;

  const BookingFormScreen({super.key, this.booking, this.initialDate});

  @override
  State<BookingFormScreen> createState() => _BookingFormScreenState();
}

class _BookingFormScreenState extends State<BookingFormScreen> {
  final _formKey = GlobalKey<FormState>();
  
  // Customer Info
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();
  
  // Summary
  final _advanceController = TextEditingController(text: '0');
  final _notesController = TextEditingController();

  List<BookingItem> _items = [];
  final Map<int, String?> _conflictErrors = {};
  bool _isLoading = true;
  bool _isSaving = false;
  static const List<Map<String, String>> _eventTypeList = [
    {'value': 'বিয়ে', 'label': '১. বিয়ে'},
    {'value': 'গায়ে হলুদ', 'label': '২. গায়ে হলুদ'},
    {'value': 'মেহেদি নাইট', 'label': '৩. মেহেদি নাইট'},
    {'value': 'আকদ / এনগেজমেন্ট', 'label': '৪. আকদ / এনগেজমেন্ট'},
    {'value': 'বৌভাত / ওয়ালিমা', 'label': '৫. বৌভাত / ওয়ালিমা'},
    {'value': 'জন্মদিন', 'label': '৬. জন্মদিন'},
    {'value': 'আকিকা', 'label': '৭. আকিকা'},
    {'value': 'বিবাহবার্ষিকী', 'label': '৮. বিবাহবার্ষিকী'},
    {'value': 'পারিবারিক পুনর্মিলনী', 'label': '৯. পারিবারিক পুনর্মিলনী'},
    {'value': 'কর্পোরেট এজিএম', 'label': '১০. কর্পোরেট এজিএম'},
    {'value': 'প্রডাক্ট লঞ্চ', 'label': '১১. প্রডাক্ট লঞ্চ'},
    {'value': 'কনফারেন্স ও সেমিনার', 'label': '১২. কনফারেন্স ও সেমিনার'},
    {'value': 'কর্পোরেট ডিনার ও গ্যালা নাইট', 'label': '১৩. কর্পোরেট ডিনার ও গ্যালা নাইট'},
    {'value': 'মেলা ও প্রদর্শনী', 'label': '১৪. মেলা ও প্রদর্শনী'},
    {'value': 'সমাবর্তন ও র্যাগ ডে', 'label': '১৫. সমাবর্তন ও র্যাগ ডে'},
    {'value': 'অ্যালামনাই রিইউনিয়ন', 'label': '১৬. অ্যালামনাই রিইউনিয়ন'},
    {'value': 'সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট', 'label': '১৭. সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট'},
    {'value': 'ইফতার মাহফিল', 'label': '১৮. ইফতার মাহফিল'},
    {'value': 'দোয়া ও মিলাদ মাহফিল', 'label': '১৯. দোয়া ও মিলাদ মাহফিল'},
    {'value': 'প্রেস কনফারেন্স ও মিটিং', 'label': '২০. প্রেস কনফারেন্স ও মিটিং'},
    {'value': 'অন্যান্য', 'label': 'অন্যান্য'},
  ];

  String _normalizeEventType(String? type) {
    if (type == null || type.trim().isEmpty) return 'বিয়ে';
    final clean = type.trim();
    for (final e in _eventTypeList) {
      if (e['value'] == clean || e['label'] == clean) return e['value']!;
    }
    final lower = clean.toLowerCase();
    if (lower.contains('বিয়ে') || lower.contains('wedding')) return 'বিয়ে';
    if (lower.contains('হলুদ') || lower.contains('holud')) return 'গায়ে হলুদ';
    if (lower.contains('মেহেদি') || lower.contains('mehendi')) return 'মেহেদি নাইট';
    if (lower.contains('আকদ') || lower.contains('এনগেজমেন্ট') || lower.contains('akht') || lower.contains('engagement')) return 'আকদ / এনগেজমেন্ট';
    if (lower.contains('বৌভাত') || lower.contains('ওয়ালিমা') || lower.contains('walima') || lower.contains('bou bhat')) return 'বৌভাত / ওয়ালিমা';
    if (lower.contains('জন্মদিন') || lower.contains('birthday')) return 'জন্মদিন';
    if (lower.contains('আকিকা') || lower.contains('aqiqa')) return 'আকিকা';
    if (lower.contains('বিবাহবার্ষিকী') || lower.contains('anniversary')) return 'বিবাহবার্ষিকী';
    if (lower.contains('পুনর্মিলনী') || lower.contains('reunion')) return 'পারিবারিক পুনর্মিলনী';
    if (lower.contains('এজিএম') || lower.contains('agm')) return 'কর্পোরেট এজিএম';
    if (lower.contains('প্রডাক্ট') || lower.contains('product launch')) return 'প্রডাক্ট লঞ্চ';
    if (lower.contains('কনফারেন্স') || lower.contains('সেমিনার') || lower.contains('seminar')) return 'কনফারেন্স ও সেমিনার';
    if (lower.contains('গ্যালা নাইট') || lower.contains('dinner')) return 'কর্পোরেট ডিনার ও গ্যালা নাইট';
    if (lower.contains('মেলা') || lower.contains('প্রদর্শনী') || lower.contains('fair') || lower.contains('exhibition')) return 'মেলা ও প্রদর্শনী';
    if (lower.contains('সমাবর্তন') || lower.contains('র্যাগ ডে') || lower.contains('rag day')) return 'সমাবর্তন ও র্যাগ ডে';
    if (lower.contains('অ্যালামনাই') || lower.contains('alumni')) return 'অ্যালামনাই রিইউনিয়ন';
    if (lower.contains('সাংস্কৃতিক') || lower.contains('কনসার্ট') || lower.contains('concert')) return 'সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট';
    if (lower.contains('ইফতার') || lower.contains('iftar')) return 'ইফতার মাহফিল';
    if (lower.contains('দোয়া') || lower.contains('মিলাদ')) return 'দোয়া ও মিলাদ মাহফিল';
    if (lower.contains('প্রেস') || lower.contains('মিটিং') || lower.contains('press')) return 'প্রেস কনফারেন্স ও মিটিং';
    if (lower.contains('অন্যান্য') || lower.contains('other')) return 'অন্যান্য';
    return clean;
  }

  List<DropdownMenuItem<String>> _getEventDropdownItems(String? currentType) {
    final normalized = _normalizeEventType(currentType);
    final items = _eventTypeList.map((e) => DropdownMenuItem<String>(
      value: e['value']!,
      child: Text(
        e['label']!,
        overflow: TextOverflow.ellipsis,
        style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13.5),
      ),
    )).toList();

    if (!_eventTypeList.any((e) => e['value'] == normalized)) {
      items.add(DropdownMenuItem<String>(
        value: normalized,
        child: Text(normalized, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13.5)),
      ));
    }
    return items;
  }

  @override
  void initState() {
    super.initState();
    _fetchInitialData();
  }

  Future<void> _fetchInitialData() async {
    try {
      if (widget.booking != null) {
        _nameController.text = widget.booking!.customerName;
        _phoneController.text = widget.booking!.customerPhone;
        _addressController.text = widget.booking!.customerAddress ?? '';
        _advanceController.text = widget.booking!.paidAmount.toString();
        _notesController.text = widget.booking!.notes ?? '';
        _items = widget.booking!.items.map((i) {
          final s = (i.slot == 'night' || i.slot == 'evening') ? 'night' : 'day';
          return i.copyWith(
            slot: s,
            eventType: _normalizeEventType(i.eventType),
          );
        }).toList();
        _checkAllAvailability();
      } else {
        _addNewItem();
      }

      // Fetch vendors
      if (mounted) {
        context.read<VendorProvider>().fetchVendors();
      }

      if (mounted) setState(() => _isLoading = false);
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  void _addNewItem() {
    final tenant = context.read<AuthProvider>().user?['tenant'];
    final defaultDate = widget.initialDate ?? DateTime.now().toIso8601String().split('T')[0];
    final pricePerSlot = _toDouble(tenant?['price_per_slot'] ?? 30000);
    setState(() {
      _items.add(BookingItem(
        eventDate: defaultDate,
        slot: 'day',
        eventType: 'বিয়ে',
        serverRate: _toDouble(tenant?['default_server_rate'] ?? 500),
        basePrice: pricePerSlot,
        subTotal: pricePerSlot,
      ));
    });
    _checkAvailability(_items.length - 1);
  }

  Future<void> _checkAvailability(int index) async {
    if (index >= _items.length) return;
    final item = _items[index];
    if (item.eventDate.isEmpty) return;

    final currentSlot = (item.slot == 'night' || item.slot == 'evening') ? 'night' : 'day';

    // 1. Check duplicate within items list
    final hasDup = _items.asMap().entries.any((entry) {
      if (entry.key == index) return false;
      final otherSlot = (entry.value.slot == 'night' || entry.value.slot == 'evening') ? 'night' : 'day';
      return entry.value.eventDate == item.eventDate && otherSlot == currentSlot;
    });

    if (hasDup) {
      final slotLabel = currentSlot == 'day' ? 'দিন (Day)' : 'রাত (Night)';
      setState(() {
        _conflictErrors[index] = 'একই বুকিংয়ে একই তারিখ (${item.eventDate}) ও স্লট ($slotLabel) একাধিকবার যোগ করা যাবে না।';
      });
      return;
    } else {
      if (_conflictErrors[index] != null && _conflictErrors[index]!.contains('একই বুকিংয়ে')) {
        setState(() {
          _conflictErrors.remove(index);
        });
      }
    }

    // 2. Query check-availability API
    try {
      String path = 'bookings/check-availability?event_date=${item.eventDate}&slot=$currentSlot';
      if (widget.booking != null) {
        path += '&exclude_booking_id=${widget.booking!.id}';
      }
      final res = await ApiService.instance.get(path);
      if (res.statusCode == 200 && res.data is Map && res.data.containsKey('available')) {
        final available = res.data['available'] == true;
        setState(() {
          if (!available) {
            _conflictErrors[index] = res.data['message']?.toString() ?? 'এই স্লটটি ইতিমধ্যেই বুকড রয়েছে।';
          } else {
            _conflictErrors.remove(index);
          }
        });
      }
    } catch (_) {}
  }

  void _checkAllAvailability() {
    for (int i = 0; i < _items.length; i++) {
      _checkAvailability(i);
    }
  }

  double get _totalAmount => _items.fold(0, (sum, item) => sum + item.subTotal);

  void _recalc(int index) {
    final item = _items[index];
    final serverCost = item.isServerIncluded ? (item.serverCount * item.serverRate) : 0.0;
    double extras = (item.isAc ? item.acPrice : 0) +
                   (item.extraSound ? item.soundPrice : 0) +
                   (item.extraGenerator ? item.generatorPrice : 0) +
                   (item.extraDecoration ? item.decorationPrice : 0) +
                   serverCost;
    
    setState(() {
      _items[index] = item.copyWith(
        subTotal: item.basePrice + extras,
      );
    });
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    if (_items.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('কমপক্ষে একটি দিন নির্বাচন করুন।')));
      return;
    }

    // 1. Check duplicate slots within the form items
    for (int i = 0; i < _items.length; i++) {
      for (int j = i + 1; j < _items.length; j++) {
        final s1 = (_items[i].slot == 'night' || _items[i].slot == 'evening') ? 'night' : 'day';
        final s2 = (_items[j].slot == 'night' || _items[j].slot == 'evening') ? 'night' : 'day';
        if (_items[i].eventDate == _items[j].eventDate && s1 == s2) {
          final slotLabel = s1 == 'day' ? 'দিন (Day)' : 'রাত (Night)';
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
            backgroundColor: Colors.red.shade700,
            content: Text('একই বুকিংয়ে একই তারিখ (${_items[i].eventDate}) ও স্লট ($slotLabel) একাধিকবার যোগ করা যাবে না।'),
            behavior: SnackBarBehavior.floating,
          ));
          return;
        }
      }
    }

    // 2. Check live conflict errors
    if (_conflictErrors.values.any((e) => e != null && e.isNotEmpty)) {
      final msg = _conflictErrors.values.firstWhere((e) => e != null && e.isNotEmpty)!;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: Colors.red.shade700,
        content: Text(msg),
        behavior: SnackBarBehavior.floating,
      ));
      return;
    }

    setState(() => _isSaving = true);

    final data = {
      'customer_name': _nameController.text,
      'customer_phone': _phoneController.text,
      'customer_address': _addressController.text,
      'notes': _notesController.text,
      'total_amount': _totalAmount,
      'advance_amount': double.tryParse(_advanceController.text) ?? 0,
      'items': _items.map((i) => i.toJson()).toList(),
    };

    bool success;
    if (widget.booking == null) {
      success = await context.read<BookingProvider>().createBooking(data);
      if (success && mounted) {
        context.read<CustomerProvider>().addOptimisticCustomer(
          _nameController.text,
          _phoneController.text,
        );
      }
    } else {
      success = await context.read<BookingProvider>().updateBooking(widget.booking!.id, data);
    }

    if (success && mounted) {
      context.read<DashboardProvider>().fetchDashboard();
      Navigator.pop(context);
      final isOffline = !ApiService.instance.isOnline.value;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: isOffline ? Colors.orange.shade800 : null,
        content: Text(isOffline
            ? 'বুকিং অফলাইনে সংরক্ষিত হয়েছে। ইন্টারনেট পেলে সিঙ্ক হবে।'
            : 'বুকিং সফলভাবে সম্পন্ন হয়েছে'),
      ));
    } else if (mounted) {
      setState(() => _isSaving = false);
      final errorMsg = context.read<BookingProvider>().lastErrorMessage ?? 'অপারেশন ব্যর্থ হয়েছে।';
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: Colors.red.shade700,
        content: Text(errorMsg),
        behavior: SnackBarBehavior.floating,
      ));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.backgroundSlate,
      appBar: AppBar(
        title: Text(widget.booking == null ? 'নতুন বুকিং' : 'বুকিং এডিট', 
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800, fontSize: 18)),
        centerTitle: false,
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : Theme(
            data: Theme.of(context).copyWith(
              inputDecorationTheme: Theme.of(context).inputDecorationTheme.copyWith(
                isDense: true,
                contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide.none,
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide(color: Colors.grey.withAlpha(25)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppTheme.primaryEmerald, width: 1.8),
                ),
              ),
            ),
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  Expanded(
                    child: SingleChildScrollView(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildSectionHeader('গ্রাহকের তথ্য'),
                          _buildCustomerCard(),
                          
                          const SizedBox(height: 16),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              _buildSectionHeader('বুকিং বিস্তারিত'),
                              TextButton.icon(
                                onPressed: _addNewItem,
                                icon: const Icon(Icons.add_circle_outline, size: 18),
                                label: const Text('দিন যোগ করুন', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                                style: TextButton.styleFrom(
                                  foregroundColor: AppTheme.primaryEmerald,
                                  visualDensity: VisualDensity.compact,
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                ),
                              ),
                            ],
                          ),
                          ...List.generate(_items.length, (index) => _buildItemCard(index)),
                          
                          const SizedBox(height: 14),
                          _buildSectionHeader('সারসংক্ষেপ ও হিসাব'),
                          _buildSummaryCard(),
                          const SizedBox(height: 14),
                          _buildSectionHeader('অতিরিক্ত নোট'),
                          _buildNotesCard(),
                          const SizedBox(height: 24),
                        ],
                      ),
                    ),
                  ),
                  _buildBottomAction(),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 8),
      child: Text(title, style: GoogleFonts.manrope(
        fontSize: 16, fontWeight: FontWeight.w800, color: AppTheme.textNavy)),
    );
  }

  Widget _buildCustomerCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 14, offset: const Offset(0, 6))],
      ),
      child: Column(
        children: [
          TextFormField(
            controller: _nameController,
            decoration: const InputDecoration(labelText: 'পুরো নাম', prefixIcon: Icon(Icons.person_outline, size: 20)),
            validator: (v) => v!.isEmpty ? 'নাম লিখুন' : null,
          ),
          const SizedBox(height: 10),
          TextFormField(
            controller: _phoneController,
            decoration: const InputDecoration(labelText: 'মোবাইল নম্বর', prefixIcon: Icon(Icons.phone_android_outlined, size: 20)),
            keyboardType: TextInputType.phone,
            validator: (v) => v!.isEmpty ? 'মোবাইল লিখুন' : null,
          ),
          const SizedBox(height: 10),
          TextFormField(
            controller: _addressController,
            maxLines: 2,
            decoration: const InputDecoration(labelText: 'ঠিকানা', prefixIcon: Icon(Icons.location_on_outlined, size: 20), alignLabelWithHint: true),
          ),
        ],
      ),
    );
  }

  Widget _buildItemCard(int index) {
    final item = _items[index];
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppTheme.primaryEmerald.withAlpha(30)),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(
              color: AppTheme.primaryEmerald.withAlpha(10),
              borderRadius: const BorderRadius.vertical(top: Radius.circular(18)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('দিন ${index + 1}', style: const TextStyle(fontWeight: FontWeight.w800, color: AppTheme.primaryEmerald, fontSize: 14)),
                if (_items.length > 1)
                  GestureDetector(
                    onTap: () {
                      setState(() {
                        _items.removeAt(index);
                        _conflictErrors.remove(index);
                      });
                      _checkAllAvailability();
                    },
                    child: const Icon(Icons.delete_sweep_outlined, color: Colors.red, size: 20),
                  ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [

                // Event Type Dropdown
                DropdownButtonFormField<String>(
                  value: _normalizeEventType(item.eventType),
                  decoration: const InputDecoration(labelText: 'ইভেন্টের ধরন', prefixIcon: Icon(Icons.celebration_outlined, size: 20)),
                  isExpanded: true,
                  items: _getEventDropdownItems(item.eventType),
                  onChanged: (val) => setState(() {
                    if (val != null) {
                      _items[index] = item.copyWith(eventType: val);
                    }
                  }),
                ),
                const SizedBox(height: 10),
                
                // Date & Slot
                Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        onTap: () async {
                          final picked = await showDatePicker(
                            context: context,
                            initialDate: DateTime.tryParse(item.eventDate) ?? DateTime.now(),
                            firstDate: DateTime.now().subtract(const Duration(days: 365)),
                            lastDate: DateTime.now().add(const Duration(days: 730)),
                          );
                          if (picked != null) {
                            setState(() {
                              _items[index] = item.copyWith(
                                eventDate: picked.toIso8601String().split('T')[0],
                              );
                            });
                            _checkAllAvailability();
                          }
                        },
                        child: InputDecorator(
                          decoration: const InputDecoration(labelText: 'তারিখ', prefixIcon: Icon(Icons.calendar_today_outlined, size: 20)),
                          child: Text(item.eventDate, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13.5)),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: (item.slot == 'night' || item.slot == 'evening') ? 'night' : 'day',
                        decoration: const InputDecoration(labelText: 'স্লট'),
                        items: const [
                          DropdownMenuItem(value: 'day', child: Text('দিন (Day)', style: TextStyle(fontSize: 13.5))),
                          DropdownMenuItem(value: 'night', child: Text('রাত (Night)', style: TextStyle(fontSize: 13.5))),
                        ],
                        onChanged: (val) {
                          if (val != null) {
                            setState(() {
                              _items[index] = item.copyWith(slot: val);
                            });
                            _checkAllAvailability();
                          }
                        },
                      ),
                    ),
                  ],
                ),
                if (_conflictErrors[index] != null && _conflictErrors[index]!.isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.red.shade50,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.red.shade300),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(Icons.warning_amber_rounded, size: 16, color: Colors.red.shade700),
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            _conflictErrors[index]!,
                            style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: Colors.red.shade800),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 10),

                // Pricing Configuration
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  decoration: BoxDecoration(color: Colors.grey.withAlpha(10), borderRadius: BorderRadius.circular(14)),
                  child: Column(
                    children: [
                      _buildPriceField('ভাড়া (Base Price)', item.basePrice, (v) {
                        _items[index] = item.copyWith(basePrice: _toDouble(v));
                        _recalc(index);
                      }),
                      const Divider(height: 14),
                      Row(
                        children: [
                          Expanded(
                            child: _buildSmallField('মেহমান', item.guestCount.toString(), (v) {
                              setState(() {
                                _items[index] = item.copyWith(guestCount: int.tryParse(v) ?? 0);
                              });
                            }),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _buildSmallField('টেবিল', item.tableCount.toString(), (v) {
                              setState(() {
                                _items[index] = item.copyWith(tableCount: int.tryParse(v) ?? 0);
                              });
                            }),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _buildSmallField('কর্মী সংখ্যা', item.serverCount.toString(), (v) {
                              _items[index] = item.copyWith(serverCount: int.tryParse(v) ?? 0);
                              _recalc(index);
                            }),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                
                const SizedBox(height: 10),
                const Text('অতিরিক্ত সুবিধাসমূহ', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12.5, color: Colors.grey)),
                const SizedBox(height: 4),
                
                // Addons with Prices
                _buildAddonSection(
                  'এসি সার্ভিস (AC)', item.isAc, item.acPrice,
                  onToggle: (v) {
                    _items[index] = item.copyWith(isAc: v!);
                    _recalc(index);
                  },
                  onPriceChange: (v) {
                    _items[index] = item.copyWith(acPrice: _toDouble(v));
                    _recalc(index);
                  }
                ),
                _buildAddonSection(
                  'সাউন্ড সিস্টেম', item.extraSound, item.soundPrice,
                  onToggle: (v) {
                    _items[index] = item.copyWith(extraSound: v!);
                    _recalc(index);
                  },
                  onPriceChange: (v) {
                    _items[index] = item.copyWith(soundPrice: _toDouble(v));
                    _recalc(index);
                  },
                  extra: _buildVendorDropdown('Sound', item.soundVendorId, (val) {
                    setState(() {
                      _items[index] = item.copyWith(soundVendorId: val);
                    });
                  }),
                ),
                _buildAddonSection(
                  'জেনারেটর', item.extraGenerator, item.generatorPrice,
                  onToggle: (v) {
                    _items[index] = item.copyWith(extraGenerator: v!);
                    _recalc(index);
                  },
                  onPriceChange: (v) {
                    _items[index] = item.copyWith(generatorPrice: _toDouble(v));
                    _recalc(index);
                  },
                  extra: _buildVendorDropdown('Generator', item.generatorVendorId, (val) {
                    setState(() {
                      _items[index] = item.copyWith(generatorVendorId: val);
                    });
                  }),
                ),
                _buildAddonSection(
                  'ডেকোরেশন', item.extraDecoration, item.decorationPrice,
                  onToggle: (v) {
                    _items[index] = item.copyWith(extraDecoration: v!);
                    _recalc(index);
                  },
                  onPriceChange: (v) {
                    _items[index] = item.copyWith(decorationPrice: _toDouble(v));
                    _recalc(index);
                  },
                  extra: _buildVendorDropdown('Decoration', item.decorationVendorId, (val) {
                    setState(() {
                      _items[index] = item.copyWith(decorationVendorId: val);
                    });
                  }),
                ),
                _buildAddonSection(
                  'কর্মী রেট', item.serverCount > 0, item.serverRate,
                  onToggle: (v) {},
                  onPriceChange: (v) {
                    _items[index] = item.copyWith(serverRate: _toDouble(v));
                    _recalc(index);
                  },
                  isCheckboxVisible: false,
                ),
                if (item.serverCount > 0)
                  Container(
                    margin: const EdgeInsets.only(top: 2, bottom: 6),
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: item.isServerIncluded ? AppTheme.primaryEmerald.withAlpha(12) : Colors.orange.withAlpha(12),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: item.isServerIncluded ? AppTheme.primaryEmerald.withAlpha(60) : Colors.orange.withAlpha(80),
                      ),
                    ),
                    child: InkWell(
                      onTap: () {
                        _items[index] = item.copyWith(isServerIncluded: !item.isServerIncluded);
                        _recalc(index);
                      },
                      child: Row(
                        children: [
                          Checkbox(
                            value: item.isServerIncluded,
                            activeColor: AppTheme.primaryEmerald,
                            materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                            visualDensity: const VisualDensity(horizontal: -4, vertical: -4),
                            onChanged: (val) {
                              _items[index] = item.copyWith(isServerIncluded: val ?? true);
                              _recalc(index);
                            },
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'সার্ভার খরচ বিলে অন্তর্ভুক্ত',
                                  style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.bold, color: AppTheme.textNavy),
                                ),
                                const SizedBox(height: 1),
                                Text(
                                  item.isServerIncluded
                                      ? 'মোট বিলে যুক্ত হবে (৳ ${(item.serverCount * item.serverRate).toStringAsFixed(0)})'
                                      : 'বিলে যুক্ত হবে না, গ্রাহক সরাসরি প্রদেয় (৳ ${(item.serverCount * item.serverRate).toStringAsFixed(0)})',
                                  style: TextStyle(
                                    fontSize: 10.5,
                                    fontWeight: FontWeight.w600,
                                    color: item.isServerIncluded ? Colors.green.shade800 : Colors.deepOrange.shade800,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                
                const Divider(height: 18),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('দিনের মোট বিল:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    Text('৳ ${item.subTotal}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: AppTheme.primaryEmerald)),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPriceField(String label, double value, Function(String) onChanged) {
    return Row(
      children: [
        Expanded(child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13.5))),
        SizedBox(
          width: 110,
          height: 36,
          child: TextFormField(
            initialValue: value.toString(),
            textAlign: TextAlign.right,
            keyboardType: TextInputType.number,
            style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryEmerald, fontSize: 14),
            decoration: const InputDecoration(
              isDense: true,
              contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 6),
              prefixText: '৳ ',
            ),
            onChanged: onChanged,
          ),
        ),
      ],
    );
  }

  Widget _buildSmallField(String label, String value, Function(String) onChanged) {
    return TextFormField(
      initialValue: value,
      decoration: InputDecoration(
        labelText: label,
        isDense: true,
        contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      ),
      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
      keyboardType: TextInputType.number,
      onChanged: onChanged,
    );
  }

  Widget _buildAddonSection(String label, bool isActive, double price, {required Function(bool?) onToggle, required Function(String) onPriceChange, bool isCheckboxVisible = true, Widget? extra}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              if (isCheckboxVisible)
                Checkbox(
                  value: isActive,
                  onChanged: onToggle,
                  activeColor: AppTheme.primaryEmerald,
                  materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  visualDensity: const VisualDensity(horizontal: -4, vertical: -4),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4)),
                )
              else
                const SizedBox(width: 4),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  label,
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: isActive ? FontWeight.bold : FontWeight.w500,
                    color: isActive ? AppTheme.textNavy : Colors.grey.shade600,
                  ),
                ),
              ),
              if (isActive || !isCheckboxVisible)
                SizedBox(
                  width: 95,
                  height: 34,
                  child: TextFormField(
                    initialValue: price.toString(),
                    textAlign: TextAlign.right,
                    keyboardType: TextInputType.number,
                    style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.bold),
                    decoration: const InputDecoration(
                      isDense: true,
                      contentPadding: EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                      prefixText: '৳ ',
                    ),
                    onChanged: onPriceChange,
                  ),
                ),
            ],
          ),
          if (isActive && extra != null) 
            Padding(
              padding: const EdgeInsets.only(left: 32, top: 4, bottom: 4),
              child: extra,
            ),
        ],
      ),
    );
  }

  Widget _buildVendorDropdown(String type, int? selectedId, Function(int?) onChanged) {
    return Consumer<VendorProvider>(
      builder: (context, provider, _) {
        final filteredVendors = provider.vendors.where((v) => 
          v['type'].toString().toLowerCase() == type.toLowerCase()
        ).toList();
        
        return DropdownButtonFormField<int>(
          value: selectedId,
          isExpanded: true,
          decoration: InputDecoration(
            labelText: '$type ভেন্ডর নির্বাচন করুন',
            contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            isDense: true,
          ),
          items: [
            const DropdownMenuItem<int>(value: null, child: Text('ভেন্ডর নির্বাচন করুন', style: TextStyle(fontSize: 13))),
            ...filteredVendors.map((v) => DropdownMenuItem<int>(
              value: v['id'],
              child: Text(v['name'], style: const TextStyle(fontSize: 13)),
            )),
          ],
          onChanged: onChanged,
        );
      },
    );
  }

  Widget _buildSummaryCard() {
    final due = _totalAmount - (double.tryParse(_advanceController.text) ?? 0);
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.primaryEmerald,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [BoxShadow(color: AppTheme.primaryEmerald.withAlpha(40), blurRadius: 14, offset: const Offset(0, 6))],
      ),
      child: Column(
        children: [
          _summaryRow('মোট ইভেন্ট বিল', '৳ $_totalAmount', isInverse: true),
          const SizedBox(height: 10),
          Row(
            children: [
              const Expanded(child: Text('অগ্রিম জমা (Advance)', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.w600, fontSize: 13.5))),
              SizedBox(
                width: 110,
                height: 38,
                child: TextField(
                  controller: _advanceController,
                  textAlign: TextAlign.right,
                  keyboardType: TextInputType.number,
                  onChanged: (v) => setState(() {}),
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
                  decoration: InputDecoration(
                    filled: true,
                    fillColor: Colors.white.withAlpha(30),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                  ),
                ),
              ),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 10), child: Divider(color: Colors.white24, height: 1)),
          _summaryRow('বকেয়া (Due Amount)', '৳ $due', isInverse: true, isLarge: true),
        ],
      ),
    );
  }

  Widget _buildNotesCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 14, offset: const Offset(0, 6))],
      ),
      child: TextFormField(
        controller: _notesController,
        maxLines: 2,
        decoration: const InputDecoration(
          labelText: 'অতিরিক্ত নোট (ঐচ্ছিক)',
          hintText: 'এখানে কিছু লিখুন...',
          prefixIcon: Icon(Icons.note_alt_outlined, size: 20),
          alignLabelWithHint: true,
        ),
      ),
    );
  }

  Widget _summaryRow(String label, String value, {bool isInverse = false, bool isLarge = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(color: isInverse ? Colors.white70 : Colors.grey.shade600, fontWeight: FontWeight.w600, fontSize: 13.5)),
        Text(value, style: TextStyle(
          color: isInverse ? Colors.white : AppTheme.textNavy,
          fontWeight: FontWeight.w900,
          fontSize: isLarge ? 20 : 16,
        )),
      ],
    );
  }

  Widget _buildBottomAction() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 10, offset: const Offset(0, -4))],
      ),
      child: SafeArea(
        child: SizedBox(
          height: 48,
          child: ElevatedButton(
            onPressed: _isSaving ? null : _save,
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 0),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            ),
            child: _isSaving 
              ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : const Text('বুকিং সম্পন্ন করুন', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    _advanceController.dispose();
    _notesController.dispose();
    super.dispose();
  }
}
