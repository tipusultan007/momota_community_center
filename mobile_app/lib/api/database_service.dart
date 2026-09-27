import 'dart:convert';
import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class DatabaseService {
  static final DatabaseService instance = DatabaseService._init();
  static Database? _database;

  DatabaseService._init();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDB('app_database.db');
    return _database!;
  }

  Future<Database> _initDB(String filePath) async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, filePath);

    return await openDatabase(
      path,
      version: 2,
      onCreate: _createDB,
      onUpgrade: _onUpgradeDB,
    );
  }

  Future _createDB(Database db, int version) async {
    // Cache table for GET requests
    await db.execute('''
      CREATE TABLE cache (
        key TEXT PRIMARY KEY,
        value TEXT,
        updated_at INTEGER
      )
    ''');

    // Sync queue for POST/PUT/DELETE requests
    await db.execute('''
      CREATE TABLE sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        method TEXT,
        path TEXT,
        body TEXT,
        hall_id TEXT,
        created_at INTEGER
      )
    ''');
  }

  Future _onUpgradeDB(Database db, int oldVersion, int newVersion) async {
    if (oldVersion < 2) {
      try {
        await db.execute('ALTER TABLE sync_queue ADD COLUMN hall_id TEXT');
      } catch (_) {}
    }
  }

  // Cache Operations
  Future<void> saveCache(String key, Map<String, dynamic> data) async {
    final db = await instance.database;
    await db.insert(
      'cache',
      {
        'key': key,
        'value': jsonEncode(data),
        'updated_at': DateTime.now().millisecondsSinceEpoch,
      },
      conflictAlgorithm: ConflictAlgorithm.replace,
    );
  }

  Future<Map<String, dynamic>?> getCache(String key) async {
    final db = await instance.database;
    final maps = await db.query(
      'cache',
      where: 'key = ?',
      whereArgs: [key],
    );

    if (maps.isNotEmpty) {
      return jsonDecode(maps.first['value'] as String);
    }
    return null;
  }

  Future<void> deleteCache([String? pattern]) async {
    final db = await instance.database;
    if (pattern == null) {
      await db.delete('cache');
    } else {
      await db.delete('cache', where: 'key LIKE ?', whereArgs: ['%$pattern%']);
    }
  }

  // Sync Queue Operations
  Future<void> addToQueue(String method, String path, dynamic body, {String? hallId}) async {
    final db = await instance.database;
    await db.insert('sync_queue', {
      'method': method,
      'path': path,
      'body': body != null ? jsonEncode(body) : null,
      'hall_id': hallId,
      'created_at': DateTime.now().millisecondsSinceEpoch,
    });
  }

  Future<List<Map<String, dynamic>>> getQueue() async {
    final db = await instance.database;
    return await db.query('sync_queue', orderBy: 'created_at ASC');
  }

  Future<int> getQueueCount() async {
    final db = await instance.database;
    final res = await db.rawQuery('SELECT COUNT(*) as count FROM sync_queue');
    return Sqflite.firstIntValue(res) ?? 0;
  }

  Future<void> removeFromQueue(int id) async {
    final db = await instance.database;
    await db.delete('sync_queue', where: 'id = ?', whereArgs: [id]);
  }

  Future<void> clearDatabase() async {
    final db = await instance.database;
    await db.delete('cache');
    await db.delete('sync_queue');
  }

  Future<void> close() async {
    final db = await instance.database;
    db.close();
  }
}
