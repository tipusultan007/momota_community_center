import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../models/booking.dart';
import '../../models/booking_item.dart';
import '../../providers/booking_provider.dart';
import '../../providers/customer_provider.dart';
import '../../providers/hall_provider.dart';
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
  final List<String> _eventTypes = [
    'বিয়ে (Wedding Reception)',
    'গায়ে হলুদ (Gaye Holud)',
    'মেহেদি নাইট (Mehendi Night)',
    'আকদ / এনগেজমেন্ট (Akht / Engagement)',
    'বৌভাত / ওয়ালিমা (Bou Bhat / Walima)',
    'জন্মদিন (Birthday Party)',
    'আকিকা (Aqiqa)',
    'বিবাহবার্ষিকী (Anniversary)',
    'পারিবারিক পুনর্মিলনী (Family Reunion)',
    'কর্পোরেট এজিএম (AGM / Annual General Meeting)',
    'প্রডাক্ট লঞ্চ (Product Launch)',
    'কনফারেন্স ও সেমিনার (Conferences & Seminars)',
    'কর্পোরেট ডিনার ও গ্যালা নাইট (Corporate Dinner & Gala Night)',
    'মেলা ও প্রদর্শনী (Trade Fairs & Exhibitions)',
    'সমাবর্তন ও র্যাগ ডে (Graduation & Rag Day)',
    'অ্যালামনাই রিইউনিয়ন (Alumni Reunion)',
    'সাংস্কৃতিক অনুষ্ঠান ও কনসার্ট (Cultural Shows & Concerts)',
    'ইফতার মাহফিল (Iftar Mahfil)',
    'দোয়া ও মিলাদ মাহফিল (Prayer Gatherings)',
    'প্রেস কনফারেন্স ও মিটিং (Press Conferences)',
    'অন্যান্য (Other)',
  ];

  String _normalizeEventType(String? type) {
    if (type == null || type.trim().isEmpty) return _eventTypes.first;
    if (_eventTypes.contains(type)) return type;
    switch (type.toLowerCase()) {
      case 'wedding':
        return 'বিয়ে (Wedding Reception)';
      case 'holud':
        return 'গায়ে হলুদ (Gaye Holud)';
      case 'birthday':
        return 'জন্মদিন (Birthday Party)';
      case 'corporate':
        return 'কর্পোরেট ডিনার ও গ্যালা নাইট (Corporate Dinner & Gala Night)';
      case 'seminar':
        return 'কনফারেন্স ও সেমিনার (Conferences & Seminars)';
      case 'exhibition':
        return 'মেলা ও প্রদর্শনী (Trade Fairs & Exhibitions)';
      case 'other':
        return 'অন্যান্য (Other)';
      default:
        return type;
    }
  }

  List<DropdownMenuItem<String>> _getEventDropdownItems(String? currentType) {
    final normalized = _normalizeEventType(currentType);
    final allTypes = List<String>.from(_eventTypes);
    if (!allTypes.contains(normalized)) {
      allTypes.add(normalized);
    }
    return allTypes.map((type) => DropdownMenuItem<String>(
      value: type,
      child: Text(
        type,
        overflow: TextOverflow.ellipsis,
      ),
    )).toList();
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
    final hallProvider = context.read<HallProvider>();
    final activeHall = hallProvider.activeHall;
    final defaultDate = widget.initialDate ?? DateTime.now().toIso8601String().split('T')[0];
    setState(() {
      _items.add(BookingItem(
        hallId: _toInt(activeHall['id']),
        hallName: activeHall['name']?.toString() ?? 'মমতা কমিউনিটি সেন্টার',
        eventDate: defaultDate,
        slot: 'day',
        eventType: _eventTypes.first,
        basePrice: _toDouble(activeHall['price_per_slot'] ?? 30000),
        subTotal: _toDouble(activeHall['price_per_slot'] ?? 30000),
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
      return entry.value.eventDate == item.eventDate && otherSlot == currentSlot && entry.value.hallId == item.hallId;
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
      String path = 'bookings/check-availability?hall_id=${item.hallId}&event_date=${item.eventDate}&slot=$currentSlot';
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

  int _toInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
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
        if (_items[i].eventDate == _items[j].eventDate && s1 == s2 && _items[i].hallId == _items[j].hallId) {
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
          style: GoogleFonts.manrope(fontWeight: FontWeight.w800)),
        centerTitle: false,
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : Form(
            key: _formKey,
            child: Column(
              children: [
                Expanded(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildSectionHeader('গ্রাহকের তথ্য'),
                        _buildCustomerCard(),
                        
                        const SizedBox(height: 24),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            _buildSectionHeader('বুকিং বিস্তারিত'),
                            TextButton.icon(
                              onPressed: _addNewItem,
                              icon: const Icon(Icons.add_circle_outline, size: 20),
                              label: const Text('দিন যোগ করুন', style: TextStyle(fontWeight: FontWeight.bold)),
                              style: TextButton.styleFrom(foregroundColor: AppTheme.primaryEmerald),
                            ),
                          ],
                        ),
                        ...List.generate(_items.length, (index) => _buildItemCard(index)),
                        
                        const SizedBox(height: 24),
                        _buildSectionHeader('সারসংক্ষেপ ও হিসাব'),
                        _buildSummaryCard(),
                        const SizedBox(height: 24),
                        _buildSectionHeader('অতিরিক্ত নোট'),
                        _buildNotesCard(),
                        const SizedBox(height: 100),
                      ],
                    ),
                  ),
                ),
                _buildBottomAction(),
              ],
            ),
          ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 12),
      child: Text(title, style: GoogleFonts.manrope(
        fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.textNavy)),
    );
  }

  Widget _buildCustomerCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          TextFormField(
            controller: _nameController,
            decoration: const InputDecoration(labelText: 'পুরো নাম', prefixIcon: Icon(Icons.person_outline)),
            validator: (v) => v!.isEmpty ? 'নাম লিখুন' : null,
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _phoneController,
            decoration: const InputDecoration(labelText: 'মোবাইল নম্বর', prefixIcon: Icon(Icons.phone_android_outlined)),
            keyboardType: TextInputType.phone,
            validator: (v) => v!.isEmpty ? 'মোবাইল লিখুন' : null,
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _addressController,
            maxLines: 2,
            decoration: const InputDecoration(labelText: 'ঠিকানা', prefixIcon: Icon(Icons.location_on_outlined), alignLabelWithHint: true),
          ),
        ],
      ),
    );
  }

  Widget _buildItemCard(int index) {
    final item = _items[index];
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppTheme.primaryEmerald.withAlpha(30)),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            decoration: BoxDecoration(
              color: AppTheme.primaryEmerald.withAlpha(10),
              borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('দিন ${index + 1}', style: const TextStyle(fontWeight: FontWeight.w800, color: AppTheme.primaryEmerald)),
                if (_items.length > 1)
                  GestureDetector(
                    onTap: () {
                      setState(() {
                        _items.removeAt(index);
                        _conflictErrors.remove(index);
                      });
                      _checkAllAvailability();
                    },
                    child: const Icon(Icons.delete_sweep_outlined, color: Colors.red, size: 24),
                  ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [

                // Event Type Dropdown
                DropdownButtonFormField<String>(
                  value: _normalizeEventType(item.eventType),
                  decoration: const InputDecoration(labelText: 'ইভেন্টের ধরন', prefixIcon: Icon(Icons.celebration_outlined)),
                  isExpanded: true,
                  items: _getEventDropdownItems(item.eventType),
                  onChanged: (val) => setState(() {
                    if (val != null) {
                      _items[index] = item.copyWith(eventType: val);
                    }
                  }),
                ),
                const SizedBox(height: 16),
                
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
                          decoration: const InputDecoration(labelText: 'তারিখ', prefixIcon: Icon(Icons.calendar_today_outlined)),
                          child: Text(item.eventDate, style: const TextStyle(fontWeight: FontWeight.bold)),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: DropdownButtonFormField<String>(
                        value: (item.slot == 'night' || item.slot == 'evening') ? 'night' : 'day',
                        decoration: const InputDecoration(labelText: 'স্লট'),
                        items: const [
                          DropdownMenuItem(value: 'day', child: Text('দিন (Day)')),
                          DropdownMenuItem(value: 'night', child: Text('রাত (Night)')),
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
                  const SizedBox(height: 10),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(
                      color: Colors.red.shade50,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: Colors.red.shade300),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(Icons.warning_amber_rounded, size: 18, color: Colors.red.shade700),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            _conflictErrors[index]!,
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.red.shade800),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 16),

                // Pricing Configuration
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(color: Colors.grey.withAlpha(10), borderRadius: BorderRadius.circular(16)),
                  child: Column(
                    children: [
                      _buildPriceField('ভাড়া (Base Price)', item.basePrice, (v) {
                        _items[index] = item.copyWith(basePrice: _toDouble(v));
                        _recalc(index);
                      }),
                      const Divider(height: 24),
                      Row(
                        children: [
                          Expanded(
                            child: _buildSmallField('মেহমান', item.guestCount.toString(), (v) {
                              setState(() {
                                _items[index] = item.copyWith(guestCount: int.tryParse(v) ?? 0);
                              });
                            }),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _buildSmallField('টেবিল', item.tableCount.toString(), (v) {
                              setState(() {
                                _items[index] = item.copyWith(tableCount: int.tryParse(v) ?? 0);
                              });
                            }),
                          ),
                          const SizedBox(width: 12),
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
                
                const SizedBox(height: 16),
                const Text('অতিরিক্ত সুবিধাসমূহ', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: Colors.grey)),
                const SizedBox(height: 12),
                
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
                    margin: const EdgeInsets.only(top: 4, bottom: 12),
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      color: item.isServerIncluded ? AppTheme.primaryEmerald.withAlpha(12) : Colors.orange.withAlpha(12),
                      borderRadius: BorderRadius.circular(14),
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
                            visualDensity: VisualDensity.compact,
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
                                  style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textNavy),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  item.isServerIncluded
                                      ? 'মোট বিলে যুক্ত হবে (৳ ${(item.serverCount * item.serverRate).toStringAsFixed(0)})'
                                      : 'বিলে যুক্ত হবে না, গ্রাহক সরাসরি প্রদেয় (৳ ${(item.serverCount * item.serverRate).toStringAsFixed(0)})',
                                  style: TextStyle(
                                    fontSize: 11,
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
                
                const Divider(height: 32),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('দিনের মোট বিল:', style: TextStyle(fontWeight: FontWeight.bold)),
                    Text('৳ ${item.subTotal}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppTheme.primaryEmerald)),
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
        Expanded(child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600))),
        SizedBox(
          width: 120,
          child: TextFormField(
            initialValue: value.toString(),
            textAlign: TextAlign.right,
            keyboardType: TextInputType.number,
            style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryEmerald),
            decoration: const InputDecoration(contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8), prefixText: '৳ '),
            onChanged: onChanged,
          ),
        ),
      ],
    );
  }

  Widget _buildSmallField(String label, String value, Function(String) onChanged) {
    return TextFormField(
      initialValue: value,
      decoration: InputDecoration(labelText: label, contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12)),
      keyboardType: TextInputType.number,
      onChanged: onChanged,
    );
  }

  Widget _buildAddonSection(String label, bool isActive, double price, {required Function(bool?) onToggle, required Function(String) onPriceChange, bool isCheckboxVisible = true, Widget? extra}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              if (isCheckboxVisible)
                Transform.scale(
                  scale: 0.9,
                  child: Checkbox(value: isActive, onChanged: onToggle, activeColor: AppTheme.primaryEmerald, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4))),
                ),
              Expanded(child: Text(label, style: TextStyle(fontWeight: isActive ? FontWeight.bold : FontWeight.normal, color: isActive ? AppTheme.textNavy : Colors.grey))),
              if (isActive || !isCheckboxVisible)
                SizedBox(
                  width: 100,
                  height: 40,
                  child: TextFormField(
                    initialValue: price.toString(),
                    textAlign: TextAlign.right,
                    keyboardType: TextInputType.number,
                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                    decoration: const InputDecoration(contentPadding: EdgeInsets.symmetric(horizontal: 8), prefixText: '৳ '),
                    onChanged: onPriceChange,
                  ),
                ),
            ],
          ),
          if (isActive && extra != null) 
            Padding(
              padding: const EdgeInsets.only(left: 48, top: 4, bottom: 8),
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
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            isDense: true,
          ),
          items: [
            const DropdownMenuItem<int>(value: null, child: Text('ভেন্ডর নির্বাচন করুন')),
            ...filteredVendors.map((v) => DropdownMenuItem<int>(
              value: v['id'],
              child: Text(v['name']),
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
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppTheme.primaryEmerald,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: AppTheme.primaryEmerald.withAlpha(50), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          _summaryRow('মোট ইভেন্ট বিল', '৳ $_totalAmount', isInverse: true),
          const SizedBox(height: 16),
          Row(
            children: [
              const Expanded(child: Text('অগ্রিম জমা (Advance)', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.w600))),
              SizedBox(
                width: 120,
                child: TextField(
                  controller: _advanceController,
                  textAlign: TextAlign.right,
                  keyboardType: TextInputType.number,
                  onChanged: (v) => setState(() {}),
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18),
                  decoration: InputDecoration(
                    filled: true,
                    fillColor: Colors.white.withAlpha(30),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                  ),
                ),
              ),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider(color: Colors.white24)),
          _summaryRow('বকেয়া (Due Amount)', '৳ $due', isInverse: true, isLarge: true),
        ],
      ),
    );
  }

  Widget _buildNotesCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: TextFormField(
        controller: _notesController,
        maxLines: 3,
        decoration: const InputDecoration(
          labelText: 'অতিরিক্ত নোট (ঐচ্ছিক)',
          hintText: 'এখানে কিছু লিখুন...',
          prefixIcon: Icon(Icons.note_alt_outlined),
          alignLabelWithHint: true,
        ),
      ),
    );
  }

  Widget _summaryRow(String label, String value, {bool isInverse = false, bool isLarge = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(color: isInverse ? Colors.white70 : Colors.grey, fontWeight: FontWeight.w600)),
        Text(value, style: TextStyle(
          color: isInverse ? Colors.white : AppTheme.textNavy,
          fontWeight: FontWeight.w900,
          fontSize: isLarge ? 24 : 18,
        )),
      ],
    );
  }

  Widget _buildBottomAction() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withAlpha(5), blurRadius: 10, offset: const Offset(0, -5))],
      ),
      child: SafeArea(
        child: ElevatedButton(
          onPressed: _isSaving ? null : _save,
          child: _isSaving 
            ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
            : const Text('বুকিং সম্পন্ন করুন'),
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
