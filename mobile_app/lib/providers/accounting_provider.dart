import 'package:flutter/material.dart';
import '../api/api_service.dart';
import '../models/transaction.dart';

class AccountingProvider with ChangeNotifier {
  List<Transaction> _transactions = [];
  List<TransactionCategory> _incomeCategories = [];
  List<TransactionCategory> _expenseCategories = [];
  bool _isLoading = false;
  bool _isMoreLoading = false;
  
  // Pagination
  int _currentPage = 1;
  int _lastPage = 1;
  String _currentType = 'all';

  // Summary Stats
  double _totalIncome = 0;
  double _totalExpense = 0;
  double _netProfit = 0;

  List<Transaction> get transactions => _transactions;
  List<TransactionCategory> get incomeCategories => _incomeCategories;
  List<TransactionCategory> get expenseCategories => _expenseCategories;
  bool get isLoading => _isLoading;
  bool get isMoreLoading => _isMoreLoading;
  double get totalIncome => _totalIncome;
  double get totalExpense => _totalExpense;
  double get netProfit => _netProfit;
  int get currentPage => _currentPage;
  bool get hasMore => _currentPage < _lastPage;

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  Future<void> fetchTransactions({int? month, int? year, String type = 'all', bool refresh = true}) async {
    _currentType = type;
    final queryParams = {
      'type': type,
      'page': refresh ? 1 : _currentPage,
      if (month != null) 'month': month,
      if (year != null) 'year': year,
    };

    if (refresh) {
      _currentPage = 1;
      // Instant cache check if empty
      if (_transactions.isEmpty) {
        final cached = await ApiService.instance.getLocalCache('/accounting', queryParameters: queryParams);
        if (cached != null && cached['data'] != null) {
          final data = cached['data'];
          _totalIncome = _toDouble(data['summary']?['total_income']);
          _totalExpense = _toDouble(data['summary']?['total_expense']);
          _netProfit = _toDouble(data['summary']?['net_profit']);
          final List txList = data['transactions'] ?? [];
          _transactions = txList.map((item) {
            try {
              return Transaction.fromJson(Map<String, dynamic>.from(item), item['type']?.toString());
            } catch (_) {
              return null;
            }
          }).whereType<Transaction>().toList();
          _isLoading = false;
          notifyListeners();
        } else {
          _isLoading = true;
          notifyListeners();
        }
      }
    } else {
      _isMoreLoading = true;
      notifyListeners();
    }

    try {
      final response = await ApiService.instance.get('/accounting', queryParameters: queryParams);
      if (response.statusCode == 200) {
        final data = response.data['data'];
        
        // Stats
        _totalIncome = _toDouble(data['summary']?['total_income']);
        _totalExpense = _toDouble(data['summary']?['total_expense']);
        _netProfit = _toDouble(data['summary']?['net_profit']);

        // Pagination info (may not exist for combined 'all' type)
        final pagination = data['pagination'];
        _currentPage = pagination?['current_page'] ?? 1;
        _lastPage = pagination?['last_page'] ?? 1;

        // Transactions
        final List txList = data['transactions'] ?? [];
        final newTxs = txList.map((item) {
          try {
            return Transaction.fromJson(Map<String, dynamic>.from(item), item['type']?.toString());
          } catch (_) {
            return null;
          }
        }).whereType<Transaction>().toList();
        
        if (refresh) {
          _transactions = newTxs;
        } else {
          _transactions.addAll(newTxs);
        }
      }
    } catch (e) {
      debugPrint('Error fetching transactions: $e');
    } finally {
      _isLoading = false;
      _isMoreLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadMore({int? month, int? year}) async {
    if (_isMoreLoading || !hasMore) return;
    _currentPage++;
    await fetchTransactions(month: month, year: year, type: _currentType, refresh: false);
  }

  Future<void> fetchCategories() async {
    try {
      final response = await ApiService.instance.get('/accounting/categories');
      if (response.statusCode == 200) {
        final data = response.data['data'];
        final List incomeList = data['income'] ?? [];
        final List expenseList = data['expense'] ?? [];
        
        _incomeCategories = incomeList.map((i) => TransactionCategory.fromJson(Map<String, dynamic>.from(i), 'income')).toList();
        _expenseCategories = expenseList.map((i) => TransactionCategory.fromJson(Map<String, dynamic>.from(i), 'expense')).toList();
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error fetching categories: $e');
    }
  }

  String? _getCategoryName(String type, dynamic catId) {
    if (catId == null) return null;
    final id = int.tryParse(catId.toString());
    if (id == null) return null;
    final list = type == 'income' ? _incomeCategories : _expenseCategories;
    try {
      return list.firstWhere((c) => c.id == id).name;
    } catch (_) {
      return null;
    }
  }

  Future<bool> storeTransaction(String type, Map<String, dynamic> data) async {
    try {
      final endpoint = type == 'income' ? '/accounting/income' : '/accounting/expense';
      final response = await ApiService.instance.post(endpoint, data: data);
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final amt = _toDouble(data['amount']);
          final catId = data['${type}_category_id'];
          final optimisticTx = Transaction(
            id: -DateTime.now().millisecondsSinceEpoch,
            type: type,
            amount: amt,
            date: data['date']?.toString() ?? DateTime.now().toIso8601String().split('T')[0],
            description: data['description']?.toString(),
            categoryName: _getCategoryName(type, catId) ?? 'N/A',
            categoryId: catId != null ? int.tryParse(catId.toString()) ?? 0 : 0,
            hallId: data['hall_id'] != null ? int.tryParse(data['hall_id'].toString()) : null,
          );
          _transactions.insert(0, optimisticTx);
          if (type == 'income') {
            _totalIncome += amt;
          } else {
            _totalExpense += amt;
          }
          _netProfit = _totalIncome - _totalExpense;
          notifyListeners();
        } else {
          await fetchTransactions();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error storing transaction: $e');
    }
    return false;
  }

  Future<bool> updateTransaction(String type, int id, Map<String, dynamic> data) async {
    try {
      final endpoint = type == 'income' ? '/accounting/income/$id' : '/accounting/expense/$id';
      final response = await ApiService.instance.patch(endpoint, data: data);
      if (response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final index = _transactions.indexWhere((t) => t.id == id);
          if (index != -1) {
            final old = _transactions[index];
            final newAmt = data['amount'] != null ? _toDouble(data['amount']) : old.amount;
            final catId = data['${type}_category_id'];
            if (type == 'income') {
              _totalIncome = _totalIncome - old.amount + newAmt;
            } else {
              _totalExpense = _totalExpense - old.amount + newAmt;
            }
            _netProfit = _totalIncome - _totalExpense;
            _transactions[index] = Transaction(
              id: old.id,
              type: type,
              amount: newAmt,
              date: data['date']?.toString() ?? old.date,
              description: data['description']?.toString() ?? old.description,
              categoryName: _getCategoryName(type, catId) ?? old.categoryName,
              categoryId: catId != null ? int.tryParse(catId.toString()) ?? old.categoryId : old.categoryId,
              hallId: data['hall_id'] != null ? int.tryParse(data['hall_id'].toString()) : old.hallId,
            );
            notifyListeners();
          }
        } else {
          await fetchTransactions();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error updating transaction: $e');
    }
    return false;
  }

  Future<bool> deleteTransaction(String type, int id) async {
    try {
      final endpoint = type == 'income' ? '/accounting/income/$id' : '/accounting/expense/$id';
      final response = await ApiService.instance.delete(endpoint);
      if (response.statusCode == 200 || response.statusCode == 202) {
        final index = _transactions.indexWhere((t) => t.id == id);
        if (index != -1) {
          final old = _transactions.removeAt(index);
          if (type == 'income') {
            _totalIncome -= old.amount;
          } else {
            _totalExpense -= old.amount;
          }
          _netProfit = _totalIncome - _totalExpense;
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error deleting transaction: $e');
    }
    return false;
  }

  // Category CRUD
  Future<bool> storeCategory(String type, String name, String? description) async {
    try {
      final response = await ApiService.instance.post('/accounting/categories', data: {
        'type': type,
        'name': name,
        'description': description,
      });
      if (response.statusCode == 201 || response.statusCode == 200 || response.statusCode == 202) {
        if (response.statusCode == 202) {
          final newCat = TransactionCategory(
            id: -DateTime.now().millisecondsSinceEpoch,
            name: name,
            type: type,
            description: description,
          );
          if (type == 'income') {
            _incomeCategories.add(newCat);
          } else {
            _expenseCategories.add(newCat);
          }
          notifyListeners();
        } else {
          await fetchCategories();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error storing category: $e');
    }
    return false;
  }

  Future<bool> updateCategory(String type, int id, String name, String? description) async {
    try {
      final response = await ApiService.instance.patch('/accounting/categories/$id', data: {
        'type': type,
        'name': name,
        'description': description,
      });
      if (response.statusCode == 200 || response.statusCode == 202) {
        final list = type == 'income' ? _incomeCategories : _expenseCategories;
        final index = list.indexWhere((c) => c.id == id);
        if (index != -1) {
          list[index] = TransactionCategory(id: id, name: name, type: type, description: description);
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error updating category: $e');
    }
    return false;
  }

  Future<bool> deleteCategory(String type, int id) async {
    try {
      final response = await ApiService.instance.delete('/accounting/categories/$id', queryParameters: {'type': type});
      if (response.statusCode == 200 || response.statusCode == 202) {
        final list = type == 'income' ? _incomeCategories : _expenseCategories;
        list.removeWhere((c) => c.id == id);
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Error deleting category: $e');
    }
    return false;
  }
}
