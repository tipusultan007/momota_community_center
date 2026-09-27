import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Momota Community Center Brand Palette (Teal & Gold from Logo)
  static const Color primaryTeal = Color(0xFF004D40);
  static const Color primaryTealDark = Color(0xFF00332C);
  static const Color primaryTealLight = Color(0xFF006D5B);

  static const Color accentGold = Color(0xFFF5A623);
  static const Color accentGoldDark = Color(0xFFD48806);
  static const Color accentGoldLight = Color(0xFFFFF4DF);

  // Backwards-compatible aliases
  static const Color primaryEmerald = primaryTeal;
  static const Color secondaryEmerald = accentGold;
  static const Color backgroundSlate = Color(0xFFF7FAF9);
  static const Color surfaceWhite = Colors.white;
  static const Color textNavy = Color(0xFF0E1E25);
  static const Color onSecondaryContainer = primaryTeal;

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: backgroundSlate,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryTeal,
        primary: primaryTeal,
        secondary: accentGold,
        surface: surfaceWhite,
        onSurface: textNavy,
        secondaryContainer: accentGoldLight,
        onSecondaryContainer: accentGoldDark,
      ),
      textTheme: GoogleFonts.interTextTheme().copyWith(
        headlineLarge: GoogleFonts.manrope(
          color: textNavy,
          fontWeight: FontWeight.w800,
          fontSize: 32,
          letterSpacing: -1.0,
        ),
        headlineMedium: GoogleFonts.manrope(
          color: textNavy,
          fontWeight: FontWeight.bold,
          fontSize: 24,
        ),
        titleLarge: GoogleFonts.manrope(
          color: textNavy,
          fontWeight: FontWeight.bold,
          fontSize: 18,
        ),
        bodyLarge: GoogleFonts.inter(
          color: textNavy,
          fontSize: 16,
        ),
        labelSmall: GoogleFonts.inter(
          color: Colors.grey,
          fontSize: 10,
          fontWeight: FontWeight.bold,
          letterSpacing: 1.2,
        ),
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: surfaceWhite,
        elevation: 0,
        centerTitle: true,
        titleTextStyle: TextStyle(
          color: textNavy,
          fontWeight: FontWeight.bold,
          fontSize: 18,
        ),
        iconTheme: IconThemeData(color: textNavy),
      ),
      cardTheme: CardThemeData(
        elevation: 0.0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(color: Colors.grey.withAlpha(25)),
        ),
        color: surfaceWhite,
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryEmerald,
          foregroundColor: Colors.white,
          minimumSize: const Size(double.infinity, 54),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: const TextStyle(
            fontWeight: FontWeight.bold,
            fontSize: 16,
          ),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.grey.withAlpha(10),
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: Colors.grey.withAlpha(20)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: primaryEmerald, width: 2),
        ),
        labelStyle: GoogleFonts.manrope(
          color: textNavy.withAlpha(150),
          fontSize: 14,
          fontWeight: FontWeight.w600,
        ),
        floatingLabelStyle: GoogleFonts.manrope(
          color: primaryEmerald,
          fontSize: 12,
          fontWeight: FontWeight.bold,
        ),
        prefixIconColor: primaryEmerald,
      ),
    );
  }
}
