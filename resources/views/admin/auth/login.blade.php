<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>সুপার এডমিন লগইন | {{ config('app.name', 'PranganHQ') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Anek Bangla"', '"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Anek Bangla', 'Plus Jakarta Sans', sans-serif;
            background-color: #070a12;
            background-image: 
                radial-gradient(rgba(16, 185, 129, 0.08) 1px, transparent 1px),
                radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px, 16px 16px;
            background-position: 0 0, 16px 16px;
        }

        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(15, 23, 42, 0.65) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glow-emerald {
            box-shadow: 0 0 50px -10px rgba(16, 185, 129, 0.35);
        }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 sm:p-6 lg:p-10 relative overflow-x-hidden selection:bg-brand-500 selection:text-slate-950">

    <!-- Ambient Glowing Light Orbs -->
    <div class="fixed top-[-10%] left-[-10%] w-[550px] h-[550px] bg-brand-500/10 rounded-full blur-[140px] pointer-events-none -z-10 animate-pulse-slow"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-teal-500/10 rounded-full blur-[150px] pointer-events-none -z-10"></div>
    <div class="fixed top-[40%] right-[20%] w-[350px] h-[350px] bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none -z-10"></div>

    <div class="w-full max-w-5xl mx-auto">
        <!-- Main Card Container -->
        <div class="glass-panel rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 relative border border-slate-800/80">
            
            <!-- Left Column: Executive Platform Overview (Visible on LG) -->
            <div class="lg:col-span-5 p-8 sm:p-10 flex flex-col justify-between relative bg-gradient-to-b from-slate-900/90 via-slate-900/50 to-slate-950/80 border-b lg:border-b-0 lg:border-r border-slate-800/80 overflow-hidden">
                <!-- Background Accent Accent Lines -->
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-brand-950/30 via-transparent to-transparent opacity-80 pointer-events-none"></div>

                <!-- Top: Brand & Badge -->
                <div class="relative z-10">
                    <a href="/" class="inline-flex items-center gap-3 group transition-transform duration-200 hover:scale-[1.02]">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-500 to-teal-400 p-[1px] shadow-lg shadow-brand-500/25">
                            <div class="w-full h-full bg-slate-950 rounded-[15px] flex items-center justify-center">
                                <svg class="w-6 h-6 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight text-white block leading-none">
                                {{ config('app.name', 'PranganHQ') }}
                            </span>
                            <span class="text-[11px] font-bold uppercase tracking-widest text-brand-400 font-mono mt-1 block">
                                Core Administration
                            </span>
                        </div>
                    </a>

                    <!-- Status Pill -->
                    <div class="mt-8 inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/60 shadow-inner">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-slate-300">
                            সার্ভার স্ট্যাটাস: <span class="text-brand-400">সক্রিয় ও সুরক্ষিত</span>
                        </span>
                    </div>

                    <!-- Intro Title -->
                    <div class="mt-6">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-snug">
                            প্ল্যাটফর্ম সেন্ট্রাল কমান্ড ও গভর্ন্যান্স গেটওয়ে
                        </h1>
                        <p class="mt-3 text-sm text-slate-400 leading-relaxed">
                            মাল্টি-টেন্যান্ট ভেন্ডর ম্যানেজমেন্ট, সাবস্ক্রিপশন অনুমোদন, পেমেন্ট গেটওয়ে কনফিগারেশন এবং আর্থিক অডিট পরিচালনার জন্য অনুমোদিত কনসোল।
                        </p>
                    </div>

                    <!-- System Feature Checklist -->
                    <div class="mt-8 space-y-3.5">
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-800/40 border border-slate-800">
                            <div class="p-1.5 rounded-lg bg-brand-500/10 text-brand-400 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">জিরো-ট্রাস্ট এনক্রিপশন</h4>
                                <p class="text-[12px] text-slate-400">সম্পূর্ণ এন্ড-টু-এন্ড এনক্রিপ্টেড ও রেট-লিমিটেড অ্যাডমিন সেশন।</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-800/40 border border-slate-800">
                            <div class="p-1.5 rounded-lg bg-teal-500/10 text-teal-400 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">টেন্যান্ট ক্লাস্টার অর্কেস্ট্রেশন</h4>
                                <p class="text-[12px] text-slate-400">সকল কনভেনশন হল, সাবস্ক্রিপশন ও রাজস্ব এক নজরে নিয়ন্ত্রণ।</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Security Tag -->
                <div class="mt-8 pt-6 border-t border-slate-800/80 relative z-10">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-brand-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            SECURE SSL/TLS 256-BIT
                        </span>
                        <span>IP LOGGED</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Login Form -->
            <div class="lg:col-span-7 p-8 sm:p-12 lg:p-14 flex flex-col justify-center bg-slate-900/60">
                <div class="max-w-md w-full mx-auto">
                    
                    <!-- Form Header -->
                    <div class="mb-8">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-500/10 border border-brand-500/20 text-brand-400 text-xs font-bold uppercase tracking-wider mb-3">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Super Admin Access
                        </div>
                        <h2 class="text-3xl font-extrabold text-white tracking-tight">এডমিন লগইন</h2>
                        <p class="mt-2 text-sm text-slate-400">
                            প্যানেলে প্রবেশ করতে আপনার অনুমোদিত ইমেল ও পাসওয়ার্ড দিন।
                        </p>
                    </div>

                    <!-- Validation / Status Alerts -->
                    @if (session('status'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-bold text-rose-200">লগইন করতে সমস্যা হচ্ছে:</p>
                                <ul class="mt-1 list-disc list-inside space-y-0.5 text-xs text-rose-300">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('admin.login') }}" class="space-y-5" autocomplete="on">
                        @csrf

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                                ইমেল ঠিকানা <span class="text-brand-400 font-mono">(Admin Email)</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    autocomplete="username" 
                                    required 
                                    autofocus
                                    value="{{ old('email') }}"
                                    placeholder="admin@admin.com"
                                    class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/70 border @error('email') border-rose-500/80 focus:border-rose-500 focus:ring-rose-500 @else border-slate-700/80 focus:border-brand-500 focus:ring-brand-500 @enderror rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-1 transition-all duration-200"
                                >
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                    পাসওয়ার্ড <span class="text-brand-400 font-mono">(Password)</span>
                                </label>
                            </div>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    autocomplete="current-password" 
                                    required 
                                    placeholder="••••••••••••"
                                    class="block w-full pl-11 pr-11 py-3.5 bg-slate-950/70 border @error('password') border-rose-500/80 focus:border-rose-500 focus:ring-rose-500 @else border-slate-700/80 focus:border-brand-500 focus:ring-brand-500 @enderror rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-1 transition-all duration-200"
                                >
                                <button 
                                    type="button" 
                                    id="togglePassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 transition-colors focus:outline-none"
                                    title="পাসওয়ার্ড দেখুন বা লুকান"
                                >
                                    <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeSlashIcon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password Note -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center cursor-pointer select-none">
                                <input 
                                    id="remember_me" 
                                    name="remember" 
                                    type="checkbox" 
                                    class="h-4 w-4 rounded bg-slate-950 border-slate-700 text-brand-500 focus:ring-brand-500 focus:ring-offset-slate-900 transition"
                                >
                                <span class="ml-2.5 text-xs font-medium text-slate-300">আমাকে মনে রাখুন</span>
                            </label>
                            
                            <span class="text-xs text-slate-500 font-mono">PORTAL ID: #SA-01</span>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full relative group overflow-hidden rounded-xl p-[1px] font-bold text-slate-950 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-slate-950 transition-all duration-300"
                            >
                                <span class="absolute inset-0 bg-gradient-to-r from-brand-400 via-teal-400 to-brand-500 rounded-xl transition-all duration-300 group-hover:opacity-90"></span>
                                <span class="relative flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-gradient-to-r from-brand-400 via-teal-400 to-brand-500 font-extrabold text-slate-950 text-sm tracking-wide shadow-lg shadow-brand-500/25 group-hover:shadow-brand-500/40 transition-all duration-200 transform group-hover:scale-[0.99] group-active:scale-[0.97]">
                                    <span>এডমিন প্যানেলে প্রবেশ করুন</span>
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Divider & Navigation Alternatives -->
                    <div class="mt-8 pt-6 border-t border-slate-800/80 space-y-4">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-brand-400 font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>ভেন্ডর / ক্লায়েন্ট লগইন পোর্টাল</span>
                            </a>

                            <a href="/" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-slate-200 font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <span>মূল ওয়েবসাইটে ফিরে যান</span>
                            </a>
                        </div>

                        <!-- Security Notice Note -->
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 text-[11px] text-slate-400 flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="leading-relaxed">
                                <strong class="text-slate-300">নিরাপত্তা সতর্কতা:</strong> এটি শুধুমাত্র অনুমোদিত সুপার এডমিনদের জন্য নির্ধারিত। অননুমোদিত অ্যাক্সেস প্রচেষ্টা স্বয়ংক্রিয়ভাবে ট্র্যাকিং ও অডিট সিস্টেমে সংরক্ষিত হয়।
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Copyright Footer -->
        <div class="mt-6 text-center text-xs text-slate-400 flex items-center justify-center gap-4">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'PranganHQ') }}. সর্বস্বত্ব সংরক্ষিত।</span>
            <span class="inline-block w-1 h-1 rounded-full bg-slate-700"></span>
            <span class="font-mono text-slate-400">Enterprise Edition v2.4</span>
        </div>
    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        eyeIcon.classList.add('hidden');
                        eyeSlashIcon.classList.remove('hidden');
                    } else {
                        eyeIcon.classList.remove('hidden');
                        eyeSlashIcon.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
