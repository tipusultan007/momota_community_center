<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="landingApp()">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title x-text="t[lang].meta_title">{{ config('app.name', 'PranganHQ') }} | প্রিমিয়াম কনভেনশন হল ম্যানেজমেন্ট সিস্টেম</title>
    <meta name="description" :content="t[lang].meta_desc" content="Smart SaaS platform for Convention Hall, Banquet & Community Center Management." />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    
    <!-- Material Symbols Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background selection:bg-secondary selection:text-white font-sans antialiased overflow-x-hidden">

    <!-- Top Navigation Bar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-md py-3 border-b border-slate-200/80' : 'bg-white/80 backdrop-blur-sm py-4 border-b border-slate-100'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 group focus:outline-none">
                <div class="w-10 h-10 primary-gradient rounded-xl flex items-center justify-center shadow-md shadow-primary/20 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-secondary text-2xl">domain</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl sm:text-2xl font-black text-primary tracking-tight group-hover:text-secondary transition-colors">
                        Prangan<span class="text-secondary">HQ</span>
                    </span>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest -mt-1" x-text="t[lang].brand_tagline">
                        Convention Hall ERP
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold text-slate-700">
                <a href="#features" class="hover:text-secondary transition-colors py-1 relative group" x-text="t[lang].nav_features">
                    বৈশিষ্ট্যসমূহ
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary rounded-full group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#solutions" class="hover:text-secondary transition-colors py-1 relative group" x-text="t[lang].nav_solutions">
                    সমাধান
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary rounded-full group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#comparison" class="hover:text-secondary transition-colors py-1 relative group" x-text="t[lang].nav_comparison">
                    কেন সেরা
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary rounded-full group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#mobile-app" class="hover:text-secondary transition-colors py-1 relative group" x-text="t[lang].nav_mobile_app">
                    মোবাইল অ্যাপ
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary rounded-full group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#pricing" class="hover:text-secondary transition-colors py-1 relative group" x-text="t[lang].nav_pricing">
                    মূল্যতালিকা
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary rounded-full group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#faq" class="hover:text-secondary transition-colors py-1 relative group" x-text="t[lang].nav_faq">
                    প্রশ্নোত্তর
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary rounded-full group-hover:w-full transition-all duration-300"></span>
                </a>
            </nav>

            <!-- Language Switcher & Auth Buttons -->
            <div class="flex items-center gap-3">
                
                <!-- Language Switcher Pill -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 shadow-inner">
                    <button @click="setLang('bn')" 
                            :class="lang === 'bn' ? 'bg-white text-primary font-bold shadow-sm' : 'text-slate-500 hover:text-primary font-medium'"
                            class="px-2.5 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5 focus:outline-none"
                            title="বাংলা">
                        <span class="text-sm">🇧🇩</span>
                        <span>বাং</span>
                    </button>
                    <button @click="setLang('en')" 
                            :class="lang === 'en' ? 'bg-white text-primary font-bold shadow-sm' : 'text-slate-500 hover:text-primary font-medium'"
                            class="px-2.5 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5 focus:outline-none"
                            title="English">
                        <span class="text-sm">🇬🇧</span>
                        <span>EN</span>
                    </button>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="primary-gradient text-white px-5 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-primary/20 hover:shadow-lg transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-secondary">dashboard</span>
                            <span x-text="t[lang].nav_dashboard">ড্যাশবোর্ড</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex text-xs sm:text-sm font-bold text-slate-700 hover:text-primary px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors" x-text="t[lang].nav_login">
                            লগইন
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="tertiary-gradient text-slate-900 px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-amber-400/20 hover:shadow-lg transition-all hover:scale-105 active:scale-95 flex items-center gap-1">
                                <span x-text="t[lang].nav_get_started">শুরু করুন</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        @endif
                    @endauth
                @endif

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="lg:hidden w-9 h-9 flex items-center justify-center text-primary rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors focus:outline-none"
                        aria-label="Toggle navigation menu">
                    <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             @click.outside="mobileMenuOpen = false"
             class="lg:hidden bg-white border-b border-slate-200 shadow-xl px-6 py-6 space-y-4"
             style="display: none;">
            <div class="flex flex-col gap-3 text-base font-bold text-slate-800">
                <a @click="mobileMenuOpen = false" href="#features" class="py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-secondary flex items-center justify-between" x-text="t[lang].nav_features">বৈশিষ্ট্যসমূহ</a>
                <a @click="mobileMenuOpen = false" href="#solutions" class="py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-secondary flex items-center justify-between" x-text="t[lang].nav_solutions">সমাধানসমূহ</a>
                <a @click="mobileMenuOpen = false" href="#comparison" class="py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-secondary flex items-center justify-between" x-text="t[lang].nav_comparison">কেন সেরা</a>
                <a @click="mobileMenuOpen = false" href="#mobile-app" class="py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-secondary flex items-center justify-between" x-text="t[lang].nav_mobile_app">মোবাইল অ্যাপ</a>
                <a @click="mobileMenuOpen = false" href="#pricing" class="py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-secondary flex items-center justify-between" x-text="t[lang].nav_pricing">মূল্যতালিকা</a>
                <a @click="mobileMenuOpen = false" href="#faq" class="py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-secondary flex items-center justify-between" x-text="t[lang].nav_faq">প্রশ্নোত্তর</a>
            </div>
            
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="primary-gradient text-white text-center py-2.5 rounded-xl font-bold text-sm shadow-md" x-text="t[lang].nav_dashboard">ড্যাশবোর্ড</a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-800" x-text="t[lang].nav_login">লগইন</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="tertiary-gradient text-slate-900 text-center py-2.5 rounded-xl font-bold text-sm shadow-md" x-text="t[lang].nav_get_started">শুরু করুন</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden mesh-gradient-bg">
        <!-- Subtle Animated Ambient Glows -->
        <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-secondary/15 rounded-full blur-[120px] pointer-events-none -z-10"></div>
        <div class="absolute top-48 right-10 w-[350px] h-[350px] bg-amber-400/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto space-y-6">
                
                <!-- Hero Badge -->
                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200/80 px-4 py-1.5 rounded-full shadow-sm text-emerald-800 text-xs sm:text-sm font-bold animate-float">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span x-text="t[lang].hero_badge">✨ বাংলাদেশের #১ স্মার্ট কনভেনশন হল সফটওয়্যার</span>
                </div>

                <!-- Hero Title -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                    <span x-text="t[lang].hero_title_1">কনভেনশন হল বুকিং ও ব্যবসা পরিচালনা করুন</span>
                    <span class="block mt-1 bg-gradient-to-r from-emerald-600 via-teal-600 to-amber-600 bg-clip-text text-transparent" x-text="t[lang].hero_title_2">
                        সম্পূর্ণ স্মার্ট অটোমেশনে
                    </span>
                </h1>

                <!-- Hero Subtitle -->
                <p class="text-base sm:text-lg md:text-xl text-slate-600 font-medium leading-relaxed max-w-2xl mx-auto" x-text="t[lang].hero_subtitle">
                    রিয়েল-টাইম বুকিং ক্যালেন্ডার, স্বয়ংক্রিয় ইনভয়েস, ভেন্ডর কমিশন, ইনভেন্টরি স্টক ও স্টাফ পে-রোল—সবকিছু এক প্ল্যাটফর্মে। যেকোনো ডিভাইস বা মোবাইল অ্যাপ থেকে দ্রুত পরিচালনা করুন।
                </p>

                <!-- Hero CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto tertiary-gradient text-slate-950 px-8 py-4 rounded-2xl font-extrabold text-base shadow-xl shadow-amber-400/20 hover:shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span x-text="t[lang].hero_cta_primary">১৪ দিনের ফ্রি ট্রায়াল শুরু করুন</span>
                        <span class="material-symbols-outlined text-lg">rocket_launch</span>
                    </a>
                    
                    <a href="#interactive-demo" class="w-full sm:w-auto bg-white text-slate-800 border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 px-7 py-4 rounded-2xl font-bold text-base transition-all flex items-center justify-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-secondary text-xl">play_circle</span>
                        <span x-text="t[lang].hero_cta_secondary">সরাসরি লাইভ প্রিভিউ দেখুন</span>
                    </a>
                </div>

                <!-- Trust Badges & Social Proof -->
                <div class="pt-8 flex flex-wrap items-center justify-center gap-6 sm:gap-10 text-xs sm:text-sm font-semibold text-slate-500">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                        <span x-text="t[lang].hero_trust_1">কোনো ক্রেডিট কার্ড লাগে না</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                        <span x-text="t[lang].hero_trust_2">১ মিনিটে ইনস্ট্যান্ট সেটআপ</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                        <span x-text="t[lang].hero_trust_3">২৪/৭ কাস্টমার সাপোর্ট</span>
                    </div>
                </div>
            </div>

            <!-- Interactive Live SaaS Preview Mockup -->
            <div id="interactive-demo" class="mt-14 max-w-5xl mx-auto relative" data-aos="fade-up">
                <!-- Glowing Backdrop -->
                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500/20 via-teal-500/20 to-amber-500/20 rounded-[2.5rem] blur-xl opacity-70"></div>
                
                <div class="relative bg-white rounded-3xl sm:rounded-[2rem] border border-slate-200/80 shadow-2xl overflow-hidden">
                    
                    <!-- Dashboard Mockup Header -->
                    <div class="bg-slate-900 px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-4 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            </div>
                            <span class="text-xs font-mono text-slate-400 hidden sm:inline">app.pranganhq.com/dashboard</span>
                        </div>
                        
                        <!-- Interactive Tabs Switcher -->
                        <div class="flex items-center bg-slate-800 p-1 rounded-xl">
                            <button @click="demoTab = 'overview'" 
                                    :class="demoTab === 'overview' ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1 text-xs rounded-lg transition-all focus:outline-none" x-text="t[lang].demo_tab_overview">
                                ওভারভিউ
                            </button>
                            <button @click="demoTab = 'bookings'" 
                                    :class="demoTab === 'bookings' ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1 text-xs rounded-lg transition-all focus:outline-none" x-text="t[lang].demo_tab_bookings">
                                বুকিং শিডিউল
                            </button>
                            <button @click="demoTab = 'financials'" 
                                    :class="demoTab === 'financials' ? 'bg-secondary text-white font-bold' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1 text-xs rounded-lg transition-all focus:outline-none" x-text="t[lang].demo_tab_financials">
                                আয় ও লাভ-ক্ষতি
                            </button>
                        </div>
                    </div>

                    <!-- Mockup Tab 1: Overview -->
                    <div x-show="demoTab === 'overview'" class="p-6 sm:p-8 space-y-6 bg-slate-50/50">
                        <!-- 4 Quick Stats -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-500" x-text="t[lang].demo_card_bookings">চলতি মাসের বুকিং</span>
                                    <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold">+১৮%</span>
                                </div>
                                <div class="text-2xl font-black text-slate-900" x-text="t[lang].demo_val_bookings">২৪ টি</div>
                                <div class="text-[11px] text-slate-400 mt-1" x-text="t[lang].demo_sub_bookings">৩টি হল মিলিয়ে</div>
                            </div>
                            
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-500" x-text="t[lang].demo_card_revenue">মোট রাজস্ব আদায়</span>
                                    <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold">+১৪%</span>
                                </div>
                                <div class="text-2xl font-black text-emerald-600" x-text="t[lang].demo_val_revenue">৳ ১২,৫০,০০০</div>
                                <div class="text-[11px] text-slate-400 mt-1" x-text="t[lang].demo_sub_revenue">অগ্রিম সহ সর্বমোট</div>
                            </div>

                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-500" x-text="t[lang].demo_card_profit">নিট মুনাফা</span>
                                    <span class="p-1.5 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold">+২২%</span>
                                </div>
                                <div class="text-2xl font-black text-slate-900" x-text="t[lang].demo_val_profit">৳ ৮,২০,০০০</div>
                                <div class="text-[11px] text-slate-400 mt-1" x-text="t[lang].demo_sub_profit">সকল খরচ বাদে</div>
                            </div>

                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-500" x-text="t[lang].demo_card_occupancy">হল বুকিং হার</span>
                                    <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">৮৮%</span>
                                </div>
                                <div class="text-2xl font-black text-blue-600" x-text="t[lang].demo_val_occupancy">৮৮% বুকড</div>
                                <div class="text-[11px] text-slate-400 mt-1" x-text="t[lang].demo_sub_occupancy">উইকএন্ড ফুল স্লট</div>
                            </div>
                        </div>

                        <!-- Live Upcoming Events Schedule in Mockup -->
                        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-bold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                                    <span class="material-symbols-outlined text-secondary">event_available</span>
                                    <span x-text="t[lang].demo_upcoming_title">আসন্ন ইভেন্ট ও শিডিউল</span>
                                </h4>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full" x-text="t[lang].demo_live_sync">● লাইভ সিঙ্ক</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition-colors gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                            💍
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900" x-text="t[lang].demo_evt_1_name">বিবাহের রিসেপশন - গ্র্যান্ড হল</p>
                                            <p class="text-xs text-slate-500" x-text="t[lang].demo_evt_1_time">আজ সন্ধ্যা ৭:০০ - রাত ১১:৩০ | ৭০০ মেহমান</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 sm:justify-end">
                                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-md" x-text="t[lang].demo_status_paid">পরিশোধিত</span>
                                        <span class="font-black text-sm text-slate-800">৳ ১,৮০,০০০</span>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition-colors gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                            🏢
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900" x-text="t[lang].demo_evt_2_name">বার্ষিক করপোরেট কনফারেন্স - হল ২</p>
                                            <p class="text-xs text-slate-500" x-text="t[lang].demo_evt_2_time">আগামীকাল সকাল ৯:০০ - বিকেল ৫:০০ | ৩০০ মেহমান</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 sm:justify-end">
                                        <span class="text-xs font-bold text-amber-700 bg-amber-100/80 px-2.5 py-1 rounded-md" x-text="t[lang].demo_status_token">টোকেন জমা</span>
                                        <span class="font-black text-sm text-slate-800">৳ ৯০,০০০</span>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition-colors gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
                                            🎂
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900" x-text="t[lang].demo_evt_3_name">জন্মদিন গালা পার্টি - রুফটপ ভেন্যু</p>
                                            <p class="text-xs text-slate-500" x-text="t[lang].demo_evt_3_time">শুক্রবার বিকেল ৪:০০ - রাত ৯:০০ | ১৫০ মেহমান</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 sm:justify-end">
                                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-md" x-text="t[lang].demo_status_confirmed">কনফার্মড</span>
                                        <span class="font-black text-sm text-slate-800">৳ ৬০,০০০</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mockup Tab 2: Bookings Calendar -->
                    <div x-show="demoTab === 'bookings'" class="p-6 sm:p-8 space-y-6 bg-slate-50/50" style="display: none;">
                        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base" x-text="t[lang].demo_cal_title">হল অনুযায়ী শিফট ও স্লট ক্যালেন্ডার</h4>
                                    <p class="text-xs text-slate-500" x-text="t[lang].demo_cal_sub">ডে শিফট / নাইট শিফট ডাবল বুকিং প্রটেকশন</p>
                                </div>
                                <span class="bg-secondary/10 text-secondary text-xs font-bold px-3 py-1.5 rounded-xl" x-text="t[lang].demo_cal_filter">গ্র্যান্ড হল এ + হল বি</span>
                            </div>

                            <div class="grid grid-cols-7 gap-2 text-center text-xs">
                                <div class="font-bold text-slate-400 py-1">রবি</div>
                                <div class="font-bold text-slate-400 py-1">সোম</div>
                                <div class="font-bold text-slate-400 py-1">মঙ্গল</div>
                                <div class="font-bold text-slate-400 py-1">বুধ</div>
                                <div class="font-bold text-slate-400 py-1">বৃহঃ</div>
                                <div class="font-bold text-slate-400 py-1 text-secondary">শুক্র</div>
                                <div class="font-bold text-slate-400 py-1 text-secondary">শনি</div>

                                <div class="p-2 rounded-xl bg-slate-50 text-slate-600 text-xs font-semibold">১</div>
                                <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">২ (বুকড)</div>
                                <div class="p-2 rounded-xl bg-slate-50 text-slate-600 text-xs font-semibold">৩</div>
                                <div class="p-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">৪ (টোকেন)</div>
                                <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">৫ (বুকড)</div>
                                <div class="p-2 rounded-xl bg-emerald-600 text-white text-xs font-black shadow-sm">৬ (ফুল ডে)</div>
                                <div class="p-2 rounded-xl bg-emerald-600 text-white text-xs font-black shadow-sm">৭ (ফুল ডে)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mockup Tab 3: Financials -->
                    <div x-show="demoTab === 'financials'" class="p-6 sm:p-8 space-y-6 bg-slate-50/50" style="display: none;">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider" x-text="t[lang].demo_fin_income">আয়ের খাতসমূহ</span>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>হল রেন্টাল ফি</span>
                                        <span class="font-bold text-slate-900">৳ ৯,২০,০০০</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-full w-[75%] rounded-full"></div>
                                    </div>
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>ক্যাটারিং ও ফুড বিল</span>
                                        <span class="font-bold text-slate-900">৳ ২,২০,০০০</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-teal-500 h-full w-[45%] rounded-full"></div>
                                    </div>
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>ভেন্ডর কমিশন কালেকশন</span>
                                        <span class="font-bold text-slate-900">৳ ১,১০,০০০</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full w-[25%] rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider" x-text="t[lang].demo_fin_expense">ব্যয়ের বিবরণী</span>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>স্টাফ বেতন ও বোনাস</span>
                                        <span class="font-bold text-slate-900">৳ ২,১০,০০০</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-rose-500 h-full w-[50%] rounded-full"></div>
                                    </div>
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>বিদ্যুৎ ও জেনারেটর ডিজেল</span>
                                        <span class="font-bold text-slate-900">৳ ১,২০,০০০</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-rose-400 h-full w-[35%] rounded-full"></div>
                                    </div>
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>রক্ষণাবেক্ষণ ও লন্ড্রি</span>
                                        <span class="font-bold text-slate-900">৳ ১,০০,০০০</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-rose-300 h-full w-[25%] rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Notification Pill (Micro UI) -->
                    <div class="absolute bottom-4 right-4 bg-slate-900/90 backdrop-blur-md text-white text-xs px-3.5 py-2 rounded-xl shadow-lg border border-slate-700 flex items-center gap-2 animate-float">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span x-text="t[lang].demo_notification">🔔 নতুন বুকিং টোকেন জমা: ৳ ৫০,০০০</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Highlight Stats Bar -->
    <section class="py-12 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
                <div class="flex flex-col items-center text-center p-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-2xl">domain_add</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-extrabold text-slate-900" x-text="t[lang].stat_halls">১০০+</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-500 mt-1" x-text="t[lang].stat_halls_label">কনভেনশন হল ও ভেন্যু</span>
                </div>

                <div class="flex flex-col items-center text-center p-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-2xl">celebration</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-extrabold text-slate-900" x-text="t[lang].stat_events">১৫,০০০+</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-500 mt-1" x-text="t[lang].stat_events_label">সফল ইভেন্ট সম্পন্ন</span>
                </div>

                <div class="flex flex-col items-center text-center p-4">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-2xl">shield_check</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-extrabold text-slate-900" x-text="t[lang].stat_accuracy">০%</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-500 mt-1" x-text="t[lang].stat_accuracy_label">ডাবল বুকিং ভুল</span>
                </div>

                <div class="flex flex-col items-center text-center p-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-2xl">support_agent</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-extrabold text-slate-900" x-text="t[lang].stat_support">২৪/৭</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-500 mt-1" x-text="t[lang].stat_support_label">ডেডিকেটেড সহায়তা</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Features Grid -->
    <section id="features" class="py-24 bg-slate-50/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-emerald-700 font-extrabold text-xs uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200" x-text="t[lang].feat_badge">
                    শক্তিশালী ফিচারসমূহ
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight" x-text="t[lang].feat_title">
                    একটি অ্যাপেই হলের সম্পূর্ণ নিয়ন্ত্রণ
                </h2>
                <p class="text-slate-600 text-sm sm:text-base font-medium max-w-2xl mx-auto" x-text="t[lang].feat_subtitle">
                    কনভেনশন হল, কমিউনিটি সেন্টার ও ব্যাঙ্কুয়েটের প্রতিটি বিভাগের জন্য তৈরি বিশেষায়িত ডিজিটাল অটোমেশন।
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-3xl">calendar_month</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-emerald-600 transition-colors" x-text="t[lang].feat_1_title">
                            স্মার্ট ক্যালেন্ডার ও বুকিং
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed" x-text="t[lang].feat_1_desc">
                            ডে/নাইট শিফট বা আওয়ারলি স্লট বুকিং। অটোমেটিক ডেট লকিং ও রিয়েল-টাইম প্রাপ্যতা যাচাই থাকায় কোনো ডাবল বুকিংয়ের সুযোগ নেই।
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-emerald-600">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span x-text="t[lang].feat_1_tag">অগ্রিম টোকেন ও স্লট লক</span>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-3xl">receipt_long</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-amber-600 transition-colors" x-text="t[lang].feat_2_title">
                            স্বয়ংক্রিয় ইনভয়েস ও বিলিং
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed" x-text="t[lang].feat_2_desc">
                            হলের লোগো সহ প্রফেশনাল পিডিএফ ইনভয়েস জেনারেশন। কিস্তিতে পেমেন্ট আদায়, বাকি টাকার হিসাব এবং তাৎক্ষণিক এসএমএস রসিদ পাঠানো।
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-amber-600">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span x-text="t[lang].feat_2_tag">পিডিএফ ও ইনস্ট্যান্ট এসএমএস</span>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-3xl">account_balance</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-teal-600 transition-colors" x-text="t[lang].feat_3_title">
                            পূর্ণাঙ্গ আয়-ব্যয় অ্যাকাউন্টিং
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed" x-text="t[lang].feat_3_desc">
                            দৈনিক বিদ্যুৎ, ডিজেল, লন্ড্রি, ক্যাটারিং ও মেরামতের খরচ লিপিবদ্ধকরণ। দিনশেষে নিট লাভ-ক্ষতি ও মাসিক ক্যাশফ্লো রিপোর্ট।
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-teal-600">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span x-text="t[lang].feat_3_tag">লাভ-ক্ষতি ও ক্যাশফ্লো</span>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-3xl">inventory</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors" x-text="t[lang].feat_4_title">
                            ইনভেন্টরি ও সম্পদ ট্র্যাকিং
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed" x-text="t[lang].feat_4_desc">
                            চেয়ার, টেবিল, স্টেজ ডেকোরেশন, লাইটিং, সাউন্ড ইকুইপমেন্ট ও ক্রোকারিজের সঠিক স্টক এবং ইভেন্টে আইটেম রিটার্ন/ড্যামেজ ট্র্যাকিং।
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-indigo-600">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span x-text="t[lang].feat_4_tag">ড্যামেজ ও রিটার্ন ট্র্যাকার</span>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-6 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-3xl">handshake</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-rose-600 transition-colors" x-text="t[lang].feat_5_title">
                            ভেন্ডর কমিশন ও কোঅর্ডিনেশন
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed" x-text="t[lang].feat_5_desc">
                            ফটোগ্রাফার, ডেকোরেটর, ক্যাটারার ও ডিজে ভেন্ডরদের কাজের অর্ডার তৈরি এবং তাদের সাথে কমিশনের স্বচ্ছ লেজার ম্যানেজমেন্ট।
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-rose-600">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span x-text="t[lang].feat_5_tag">স্বচ্ছ কমিশন লেজার</span>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-6 group-hover:bg-cyan-600 group-hover:text-white transition-colors duration-300">
                            <span class="material-symbols-outlined text-3xl">badge</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-cyan-600 transition-colors" x-text="t[lang].feat_6_title">
                            স্টাফ পে-রোল ও হাজিরা
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed" x-text="t[lang].feat_6_desc">
                            স্টাফদের দৈনিক হাজিরা, দায়িত্ব বণ্টন, অ্যাডভান্স লোন ট্র্যাকিং এবং মাস শেষে এক ক্লিকে নির্ভুল স্যালারি শিট ও ভাউচার তৈরি।
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-cyan-600">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span x-text="t[lang].feat_6_tag">অটো স্যালারি ভাউচার</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Before vs After Comparison -->
    <section id="comparison" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-amber-700 font-extrabold text-xs uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full border border-amber-200" x-text="t[lang].comp_badge">
                    পার্থক্যটা স্পষ্ট
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight" x-text="t[lang].comp_title">
                    হাতে-কলমে হিসাব বনাম PranganHQ
                </h2>
                <p class="text-slate-600 text-sm sm:text-base font-medium" x-text="t[lang].comp_subtitle">
                    দেখুন কেন ঐতিহ্যবাহী ডায়েরি বা এক্সেল শিট বাদ দিয়ে আধুনিক হল মালিকরা PranganHQ ব্যবহার করছেন।
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Old / Manual Way -->
                <div class="bg-rose-50/40 rounded-3xl p-8 border border-rose-200/80 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-rose-200/60">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">close</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-lg text-slate-900" x-text="t[lang].comp_old_title">হাতে-কলমে বা এক্সেলে পুরোনো পদ্ধতি</h3>
                            <p class="text-xs text-rose-600 font-bold" x-text="t[lang].comp_old_sub">ঝামেলা ও ভুলভ্রান্তির ঝুঁকি</p>
                        </div>
                    </div>

                    <ul class="space-y-4 text-sm text-slate-700">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-rose-500 text-lg flex-shrink-0 mt-0.5">cancel</span>
                            <span x-text="t[lang].comp_old_1">ডায়েরিতে তারিখ বুকিং রাখায় ডাবল বুকিং ও শিডিউল কনফ্লিক্টের ভয়।</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-rose-500 text-lg flex-shrink-0 mt-0.5">cancel</span>
                            <span x-text="t[lang].comp_old_2">কাগজের রসিদ হারিয়ে যাওয়া বা বাকি টাকার হিসেবে গরমিল হওয়া।</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-rose-500 text-lg flex-shrink-0 mt-0.5">cancel</span>
                            <span x-text="t[lang].comp_old_3">ভেন্ডর কমিশন ও ডেকোরেশন মালামাল ড্যামেজের সঠিক হিসাব না থাকা।</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-rose-500 text-lg flex-shrink-0 mt-0.5">cancel</span>
                            <span x-text="t[lang].comp_old_4">মালিক হলের বাইরে থাকলে ব্যবসার আপডেট পেতে স্টাফদের উপর নির্ভরশীলতা।</span>
                        </li>
                    </ul>
                </div>

                <!-- New / PranganHQ Smart Way -->
                <div class="bg-emerald-50/40 rounded-3xl p-8 border-2 border-emerald-500/30 space-y-6 shadow-xl relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
                    
                    <div class="flex items-center gap-3 pb-4 border-b border-emerald-200/60 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                            <span class="material-symbols-outlined">check</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-lg text-slate-900" x-text="t[lang].comp_new_title">PranganHQ স্মার্ট প্ল্যাটফর্ম</h3>
                            <p class="text-xs text-emerald-700 font-bold" x-text="t[lang].comp_new_sub">১০০% স্বচ্ছ ও স্বয়ংক্রিয়</p>
                        </div>
                    </div>

                    <ul class="space-y-4 text-sm text-slate-700 relative z-10">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-600 text-lg flex-shrink-0 mt-0.5">check_circle</span>
                            <span x-text="t[lang].comp_new_1">এক ক্লিকে স্লট বুকিং ও অটো লক; ডাবল বুকিং ০% অসম্ভব।</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-600 text-lg flex-shrink-0 mt-0.5">check_circle</span>
                            <span x-text="t[lang].comp_new_2">গ্রাহকের মোবাইলে তাৎক্ষণিক এসএমএস ও হোয়াটসঅ্যাপ ডিজিটাল পিডিএফ বিল।</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-600 text-lg flex-shrink-0 mt-0.5">check_circle</span>
                            <span x-text="t[lang].comp_new_3">প্রতিটি ভেন্ডর কমিশন ও ক্রোকারিজ মালামাল রিটার্ন লাইভ ট্র্যাক।</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-600 text-lg flex-shrink-0 mt-0.5">check_circle</span>
                            <span x-text="t[lang].comp_new_4">স্মার্টফোন থেকেই হলের রিয়েল-টাইম আয়, ব্যয় ও লাভ পর্যবেক্ষণ।</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile App Showcase -->
    <section id="mobile-app" class="py-24 mesh-dark-bg text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                
                <!-- Left Details -->
                <div class="space-y-8 text-center lg:text-left" data-aos="fade-right">
                    <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-400/20 px-4 py-1.5 rounded-full text-emerald-400 text-xs font-bold">
                        <span class="material-symbols-outlined text-sm">smartphone</span>
                        <span x-text="t[lang].app_badge">অ্যান্ড্রয়েড ও আইওএস সাপোর্টেড</span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-6xl font-black tracking-tight leading-[1.15]" x-text="t[lang].app_title">
                        মোবাইল অ্যাপ দিয়ে ব্যবসা পরিচালনা করুন যেকোনো স্থান থেকে
                    </h2>

                    <p class="text-slate-300 text-base sm:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0" x-text="t[lang].app_subtitle">
                        আপনি যেখানেই থাকুন না কেন, আপনার কনভেনশন হলের বুকিং অ্যালার্ট, পেমেন্ট ও প্রতিদিনের খরচের হিসাব সব সময় আপনার হাতের মুঠোয়।
                    </p>

                    <div class="grid sm:grid-cols-2 gap-4 text-left max-w-lg mx-auto lg:mx-0">
                        <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-3.5 rounded-2xl">
                            <span class="material-symbols-outlined text-emerald-400 text-2xl">notifications_active</span>
                            <span class="text-xs sm:text-sm font-bold" x-text="t[lang].app_f1">লাইভ পুশ নোটিফিকেশন</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-3.5 rounded-2xl">
                            <span class="material-symbols-outlined text-amber-400 text-2xl">qr_code_scanner</span>
                            <span class="text-xs sm:text-sm font-bold" x-text="t[lang].app_f2">কিউআর কোড স্ক্যানার</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-3.5 rounded-2xl">
                            <span class="material-symbols-outlined text-teal-400 text-2xl">share</span>
                            <span class="text-xs sm:text-sm font-bold" x-text="t[lang].app_f3">ইনস্ট্যান্ট ইনভয়েস শেয়ার</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-3.5 rounded-2xl">
                            <span class="material-symbols-outlined text-cyan-400 text-2xl">offline_pin</span>
                            <span class="text-xs sm:text-sm font-bold" x-text="t[lang].app_f4">অফলাইন ড্রাফট সেভ</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                        <a href="#" class="inline-flex items-center gap-3 bg-white text-slate-900 px-5 py-3 rounded-2xl font-bold text-sm shadow-xl hover:scale-105 transition-all">
                            <span class="material-symbols-outlined text-2xl text-emerald-600">android</span>
                            <div class="text-left">
                                <span class="block text-[10px] text-slate-500 uppercase tracking-wider">ডাউনলোড করুন</span>
                                <span class="font-extrabold text-sm">Google Play</span>
                            </div>
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 border-2 border-white/20 hover:border-white/40 text-white px-5 py-3 rounded-2xl font-bold text-sm hover:bg-white/5 transition-all">
                            <span class="material-symbols-outlined text-amber-400">web</span>
                            <span x-text="t[lang].app_web_login">ওয়েব ভার্সনে লগইন</span>
                        </a>
                    </div>
                </div>

                <!-- Right Smartphone Mockup -->
                <div class="relative flex justify-center" data-aos="fade-left">
                    <div class="relative w-[300px] sm:w-[320px] h-[600px] bg-slate-950 rounded-[3rem] border-[10px] border-slate-800 shadow-2xl overflow-hidden">
                        <!-- Speaker & Camera Notch -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-5 bg-slate-800 rounded-b-2xl z-30"></div>
                        
                        <!-- Mobile App Screen Content -->
                        <div class="h-full bg-slate-900 text-white p-5 pt-8 flex flex-col justify-between overflow-y-auto">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between pt-2">
                                    <div>
                                        <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">PranganHQ App</p>
                                        <h4 class="font-extrabold text-base">গ্র্যান্ড প্যালেস হল</h4>
                                    </div>
                                    <span class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs">👑</span>
                                </div>

                                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-4 rounded-2xl shadow-lg">
                                    <p class="text-xs opacity-80">আজকের সংগৃহীত পেমেন্ট</p>
                                    <p class="text-2xl font-black mt-1">৳ ২,৫০,০০০</p>
                                    <div class="flex items-center gap-2 mt-2 text-[10px] bg-black/20 px-2 py-1 rounded-lg w-fit">
                                        <span>● ৩টি বুকিং নিশ্চিত</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-xs font-bold text-slate-400">আজকের শিডিউল</p>
                                    <div class="bg-slate-800/80 p-3 rounded-xl border border-slate-700/60 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-xs">বিবাহ - মেহমান ৫০০</p>
                                            <p class="text-[10px] text-slate-400">হল ১ • সন্ধ্যা ৭:০০</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-md">Paid</span>
                                    </div>
                                    <div class="bg-slate-800/80 p-3 rounded-xl border border-slate-700/60 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-xs">জন্মদিন - মেহমান ১০০</p>
                                            <p class="text-[10px] text-slate-400">হল ২ • বিকেল ৪:০০</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-1 rounded-md">Token</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile App Bottom Nav -->
                            <div class="bg-slate-950/80 backdrop-blur-md p-3 rounded-2xl border border-slate-800 flex justify-around text-slate-400">
                                <span class="material-symbols-outlined text-emerald-400 text-xl">home</span>
                                <span class="material-symbols-outlined text-xl">calendar_today</span>
                                <span class="material-symbols-outlined text-xl">receipt</span>
                                <span class="material-symbols-outlined text-xl">settings</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Pricing Section with Billing Toggle -->
    <section id="pricing" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-emerald-700 font-extrabold text-xs uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200" x-text="t[lang].pricing_badge">
                    স্বচ্ছ ও সাশ্রয়ী প্যাকেজ
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight" x-text="t[lang].pricing_title">
                    আপনার হলের উপযোগী প্ল্যান বেছে নিন
                </h2>
                <p class="text-slate-600 text-sm sm:text-base font-medium max-w-xl mx-auto" x-text="t[lang].pricing_subtitle">
                    কোনো লুকানো ফি নেই। সকল প্যাকেজেই পাচ্ছেন ১৪ দিনের বিনামূল্যে ট্রায়াল ও ফুল সাপোর্ট।
                </p>

                <!-- Billing Cycle Switcher -->
                <div class="flex items-center justify-center gap-3 pt-4">
                    <span :class="billingCycle === 'monthly' ? 'text-slate-900 font-bold' : 'text-slate-500 font-medium'" class="text-sm" x-text="t[lang].price_monthly">
                        মাসিক বিলিং
                    </span>
                    
                    <button @click="billingCycle = (billingCycle === 'monthly' ? 'annual' : 'monthly')" 
                            type="button" 
                            class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none bg-slate-800"
                            :class="billingCycle === 'annual' ? 'bg-emerald-600' : 'bg-slate-300'">
                        <span aria-hidden="true" 
                              class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="billingCycle === 'annual' ? 'translate-x-7' : 'translate-x-0'"></span>
                    </button>
                    
                    <div class="flex items-center gap-1.5">
                        <span :class="billingCycle === 'annual' ? 'text-slate-900 font-bold' : 'text-slate-500 font-medium'" class="text-sm" x-text="t[lang].price_annual">
                            বাৎসরিক বিলিং
                        </span>
                        <span class="bg-amber-100 text-amber-800 text-[11px] font-black px-2 py-0.5 rounded-full" x-text="t[lang].price_discount_tag">
                            ২০% ছাড়
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3 Pricing Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto items-stretch">
                
                <!-- Plan 1: Starter -->
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="mb-4">
                            <h3 class="text-xl font-extrabold text-slate-900" x-text="t[lang].plan_starter_title">স্টার্টআপ</h3>
                            <p class="text-slate-500 text-xs mt-1" x-text="t[lang].plan_starter_desc">ছোট বা একক কনভেনশন হলের জন্য উপযুক্ত</p>
                        </div>

                        <div class="flex items-baseline gap-1 my-6">
                            <span class="text-4xl font-black text-slate-900" x-text="billingCycle === 'annual' ? t[lang].plan_starter_price_yr : t[lang].plan_starter_price_mo">৳ ৩,১৯৯</span>
                            <span class="text-slate-500 text-xs font-semibold" x-text="t[lang].price_per_month">/ মাস</span>
                        </div>

                        <ul class="space-y-3.5 text-sm text-slate-700 my-6">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_starter_f1">১টি কনভেনশন হল ব্যবস্থাপনা</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_starter_f2">আনলিমিটেড বুকিং ও ক্যালেন্ডার</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_starter_f3">পিডিএফ ইনভয়েস ও রসিদ তৈরি</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_starter_f4">বেসিক আয়-ব্যয় হিসাব</span>
                            </li>
                            <li class="flex items-center gap-2.5 text-slate-400">
                                <span class="material-symbols-outlined text-slate-300 text-lg">cancel</span>
                                <span x-text="t[lang].plan_starter_f5">ইনভেন্টরি ও স্টাফ পে-রোল</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="w-full text-center py-3.5 rounded-xl font-extrabold text-sm border-2 border-slate-300 text-slate-800 hover:bg-slate-200/60 transition-colors mt-4" x-text="t[lang].plan_btn_trial">
                        ফ্রি শুরু করুন
                    </a>
                </div>

                <!-- Plan 2: Professional (Most Popular) -->
                <div class="bg-slate-900 text-white rounded-3xl p-8 border-2 border-emerald-500 shadow-2xl flex flex-col justify-between relative transform lg:-translate-y-2">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-[11px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-md" x-text="t[lang].plan_popular_badge">
                        সর্বাধিক জনপ্রিয়
                    </div>

                    <div>
                        <div class="mb-4">
                            <h3 class="text-xl font-extrabold text-white" x-text="t[lang].plan_pro_title">প্রফেশনাল</h3>
                            <p class="text-slate-400 text-xs mt-1" x-text="t[lang].plan_pro_desc">মাঝারি ও একাধিক ভেন্যু বিশিষ্ট কনভেনশন সেন্টারের জন্য</p>
                        </div>

                        <div class="flex items-baseline gap-1 my-6">
                            <span class="text-4xl font-black text-emerald-400" x-text="billingCycle === 'annual' ? t[lang].plan_pro_price_yr : t[lang].plan_pro_price_mo">৳ ৬,৩৯৯</span>
                            <span class="text-slate-400 text-xs font-semibold" x-text="t[lang].price_per_month">/ মাস</span>
                        </div>

                        <ul class="space-y-3.5 text-sm text-slate-200 my-6">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_pro_f1">৩টি পর্যন্ত হল / ভেন্যু ম্যানেজমেন্ট</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_pro_f2">ইনভেন্টরি ও সম্পদ ট্র্যাকিং</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_pro_f3">স্টাফ হাজিরা ও স্যালারি পে-রোল</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_pro_f4">ভেন্ডর কমিশন ও কোঅর্ডিনেশন</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_pro_f5">মোবাইল অ্যাপ ফুল এক্সেস</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="w-full tertiary-gradient text-slate-950 text-center py-3.5 rounded-xl font-extrabold text-sm shadow-lg hover:scale-105 active:scale-95 transition-all mt-4" x-text="t[lang].plan_btn_pro">
                        প্রফেশনাল শুরু করুন
                    </a>
                </div>

                <!-- Plan 3: Enterprise -->
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="mb-4">
                            <h3 class="text-xl font-extrabold text-slate-900" x-text="t[lang].plan_ent_title">এন্টারপ্রাইজ</h3>
                            <p class="text-slate-500 text-xs mt-1" x-text="t[lang].plan_ent_desc">বড় কনভেনশন চেইন ও মাল্টি-ব্রাঞ্চ সেন্টারের জন্য</p>
                        </div>

                        <div class="flex items-baseline gap-1 my-6">
                            <span class="text-4xl font-black text-slate-900" x-text="billingCycle === 'annual' ? t[lang].plan_ent_price_yr : t[lang].plan_ent_price_mo">৳ ১১,১৯৯</span>
                            <span class="text-slate-500 text-xs font-semibold" x-text="t[lang].price_per_month">/ মাস</span>
                        </div>

                        <ul class="space-y-3.5 text-sm text-slate-700 my-6">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_ent_f1">আনলিমিটেড হল ও মাল্টি-ব্রাঞ্চ</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_ent_f2">কাস্টম এসএমএস ও ডোমেইন ব্যান্ডিং</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_ent_f3">ডেডিকেটেড অ্যাকাউন্ট ম্যানেজার</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_ent_f4">অন-সাইট ট্রেইনিং ও ডাটা মাইগ্রেশন</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                <span x-text="t[lang].plan_ent_f5">২৪/৭ ভিআইপি সাপোর্ট</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="w-full text-center py-3.5 rounded-xl font-extrabold text-sm border-2 border-slate-800 bg-slate-900 text-white hover:bg-slate-800 transition-colors mt-4" x-text="t[lang].plan_btn_ent">
                        এন্টারপ্রাইজ নিন
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Client Testimonials -->
    <section class="py-24 bg-slate-50/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-emerald-700 font-extrabold text-xs uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200" x-text="t[lang].test_badge">
                    গ্রাহকদের সন্তুষ্টি
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight" x-text="t[lang].test_title">
                    হল মালিক ও ম্যানেজাররা যা বলছেন
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-amber-400 text-sm">
                            ★★★★★
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed" x-text="t[lang].test_1_quote">
                            "আগে ডাবল বুকিং নিয়ে সবসময় আতঙ্কে থাকতাম। PranganHQ ব্যবহার শুরু করার পর থেকে বুকিং শিডিউল ১০০% নির্ভুল এবং ইনভয়েস এক ক্লিকেই তৈরি হয়ে যায়।"
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center">
                            র
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm" x-text="t[lang].test_1_name">রফিকুল ইসলাম</h4>
                            <p class="text-xs text-slate-500" x-text="t[lang].test_1_role">ম্যানেজিং ডিরেক্টর, গ্র্যান্ড রয়েল কনভেনশন (ঢাকা)</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-amber-400 text-sm">
                            ★★★★★
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed" x-text="t[lang].test_2_quote">
                            "মোবাইল অ্যাপটি অসাধারণ! আমি বাইরে থাকলেও হলের প্রতিদিনের কালেকশন ও বুকিং দেখতে পারি। আমাদের স্টাফদের স্যালারি শিটও এখন এই অ্যাপ দিয়েই তৈরি হয়।"
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center">
                            আ
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm" x-text="t[lang].test_2_name">আশরাফুল হক</h4>
                            <p class="text-xs text-slate-500" x-text="t[lang].test_2_role">স্বত্বাধিকারী, উৎসব কমিউনিটি সেন্টার (চট্টগ্রাম)</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-amber-400 text-sm">
                            ★★★★★
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed" x-text="t[lang].test_3_quote">
                            "ভেন্ডর কমিশন ও ইনভেন্টরির হিসাব মেলাতে আগে অনেক সময় নষ্ট হতো। এখন ক্যাটারিং ও ডেকোরেশনের প্রতিটি হিসাব নির্ভুলভাবে ট্র্যাক করতে পারি।"
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-800 font-bold flex items-center justify-center">
                            ত
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm" x-text="t[lang].test_3_name">তানভীর আহমেদ</h4>
                            <p class="text-xs text-slate-500" x-text="t[lang].test_3_role">জেনারেল ম্যানেজার, প্যারাডাইস ব্যাঙ্কুয়েট (সিলেট)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive FAQ Accordion -->
    <section id="faq" class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-3">
                <span class="text-emerald-700 font-extrabold text-xs uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200" x-text="t[lang].faq_badge">
                    সাধারণ জিজ্ঞাসা
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight" x-text="t[lang].faq_title">
                    প্রায়শই জিজ্ঞাসিত প্রশ্নাবলি
                </h2>
            </div>

            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-colors" :class="faqOpen === 1 ? 'border-emerald-500 bg-emerald-50/20' : 'bg-white'">
                    <button @click="faqOpen = (faqOpen === 1 ? null : 1)" class="w-full px-6 py-5 text-left font-bold text-slate-900 flex justify-between items-center focus:outline-none">
                        <span class="text-base sm:text-lg" x-text="t[lang].faq_q1">সফটওয়্যারটি ব্যবহার শুরু করতে কী কী প্রয়োজন?</span>
                        <span class="material-symbols-outlined transition-transform duration-200" :class="faqOpen === 1 ? 'rotate-180 text-emerald-600' : 'text-slate-400'">expand_more</span>
                    </button>
                    <div x-show="faqOpen === 1" x-collapse class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100/80 pt-3">
                        <p x-text="t[lang].faq_a1">কোনো বিশেষ হার্ডওয়্যার বা ইন্সটলেশনের প্রয়োজন নেই। ইন্টারনেট সংযোগযুক্ত যেকোনো কম্পিউটার, ল্যাপটপ বা স্মার্টফোন থেকেই সরাসরি ব্রাউজার বা অ্যাপের মাধ্যমে আপনি PranganHQ ব্যবহার করতে পারবেন।</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-colors" :class="faqOpen === 2 ? 'border-emerald-500 bg-emerald-50/20' : 'bg-white'">
                    <button @click="faqOpen = (faqOpen === 2 ? null : 2)" class="w-full px-6 py-5 text-left font-bold text-slate-900 flex justify-between items-center focus:outline-none">
                        <span class="text-base sm:text-lg" x-text="t[lang].faq_q2">আমার একাধিক হল বা ভেন্যু থাকলে কীভাবে ম্যানেজ করব?</span>
                        <span class="material-symbols-outlined transition-transform duration-200" :class="faqOpen === 2 ? 'rotate-180 text-emerald-600' : 'text-slate-400'">expand_more</span>
                    </button>
                    <div x-show="faqOpen === 2" x-collapse class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100/80 pt-3">
                        <p x-text="t[lang].faq_a2">আমাদের প্রফেশনাল ও এন্টারপ্রাইজ প্যাকেজে মাল্টি-হল সাপোর্ট রয়েছে। আপনি একই অ্যাকাউন্ট থেকে আলাদা আলাদা হলের বুকিং ক্যালেন্ডার, শিফট ও আয়-ব্যয়ের হিসাব একক ড্যাশবোর্ডেই দেখতে পাবেন।</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-colors" :class="faqOpen === 3 ? 'border-emerald-500 bg-emerald-50/20' : 'bg-white'">
                    <button @click="faqOpen = (faqOpen === 3 ? null : 3)" class="w-full px-6 py-5 text-left font-bold text-slate-900 flex justify-between items-center focus:outline-none">
                        <span class="text-base sm:text-lg" x-text="t[lang].faq_q3">আমাদের হলের তথ্য ও ডাটা কতটা নিরাপদ?</span>
                        <span class="material-symbols-outlined transition-transform duration-200" :class="faqOpen === 3 ? 'rotate-180 text-emerald-600' : 'text-slate-400'">expand_more</span>
                    </button>
                    <div x-show="faqOpen === 3" x-collapse class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100/80 pt-3">
                        <p x-text="t[lang].faq_a3">আপনার ডাটা সুরক্ষিত ক্লাউড সার্ভারে ২৫৬-বিট এসএসএল এনক্রিপশনের মাধ্যমে সংরক্ষিত থাকে। প্রতিদিন স্বয়ংক্রিয় ক্লাউড ব্যাকআপ নেওয়া হয়, যাতে যেকোনো পরিস্থিতিতে আপনার মূল্যবান তথ্য সম্পূর্ণ নিরাপদ থাকে।</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-colors" :class="faqOpen === 4 ? 'border-emerald-500 bg-emerald-50/20' : 'bg-white'">
                    <button @click="faqOpen = (faqOpen === 4 ? null : 4)" class="w-full px-6 py-5 text-left font-bold text-slate-900 flex justify-between items-center focus:outline-none">
                        <span class="text-base sm:text-lg" x-text="t[lang].faq_q4">বিকাশ, নগদ বা ব্যাংকের মাধ্যমে পেমেন্ট করা যাবে কি?</span>
                        <span class="material-symbols-outlined transition-transform duration-200" :class="faqOpen === 4 ? 'rotate-180 text-emerald-600' : 'text-slate-400'">expand_more</span>
                    </button>
                    <div x-show="faqOpen === 4" x-collapse class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100/80 pt-3">
                        <p x-text="t[lang].faq_a4">হ্যাঁ, সাবস্ক্রিপশন ফি বিকাশ, নগদ, রকেট, ডেবিট/ক্রেডিট কার্ড অথবা সরাসরি ব্যাংক ট্রান্সফারের মাধ্যমে সহজেই পরিশোধ করা যায়।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- High-Impact Bottom Call to Action Banner -->
    <section class="py-20 relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/80 via-slate-950 to-amber-950/60 pointer-events-none"></div>
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-emerald-500/20 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight" x-text="t[lang].cta_banner_title">
                আপনার কনভেনশন হলকে আজই ডিজিটাল ও স্মার্ট করুন
            </h2>
            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto font-medium" x-text="t[lang].cta_banner_sub">
                কোনো ইনস্টলেশন চার্জ নেই। ১৪ দিনের ফ্রি ট্রায়ালে শুরু করে দেখুন কীভাবে আপনার ব্যবসার সময় ও খরচ বাঁচে।
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto tertiary-gradient text-slate-950 px-9 py-4 rounded-2xl font-black text-base shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span x-text="t[lang].cta_banner_btn">ফ্রি ট্রায়াল শুরু করুন</span>
                    <span class="material-symbols-outlined text-lg">rocket_launch</span>
                </a>
                <a href="tel:+8801700000000" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 border border-white/20 px-7 py-4 rounded-2xl font-bold text-base transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-emerald-400">call</span>
                    <span x-text="t[lang].cta_banner_call">ফোন করুন: +৮৮০ ১৭০০০০০০০০</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Comprehensive Modern Footer -->
    <footer class="bg-slate-950 border-t border-slate-800 text-slate-400 py-16 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
                
                <!-- Brand Info (2 cols) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 primary-gradient rounded-xl flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-secondary text-xl">domain</span>
                        </div>
                        <span class="text-2xl font-black text-white tracking-tight">
                            Prangan<span class="text-secondary">HQ</span>
                        </span>
                    </div>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed max-w-sm" x-text="t[lang].footer_desc">
                        বাংলাদেশের প্রথম পূর্ণাঙ্গ ক্লাউড-ভিত্তিক কনভেনশন হল ও ব্যাঙ্কুয়েট ম্যানেজমেন্ট সফটওয়্যার। বুকিং, ইনভয়েসিং, পে-রোল ও ইনভেন্টরি অটোমেশনের নির্ভরযোগ্য প্ল্যাটফর্ম।
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:border-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-lg">public</span>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:border-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-lg">mail</span>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:border-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-lg">call</span>
                        </a>
                    </div>
                </div>

                <!-- Column 1: Products -->
                <div class="space-y-3">
                    <h5 class="font-bold text-white text-sm" x-text="t[lang].footer_col1_title">মডিউলসমূহ</h5>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors" x-text="t[lang].footer_f1">বুকিং ক্যালেন্ডার</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors" x-text="t[lang].footer_f2">স্বয়ংক্রিয় ইনভয়েস</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors" x-text="t[lang].footer_f3">ইনভেন্টরি ট্র্যাকিং</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors" x-text="t[lang].footer_f4">স্টাফ ও পে-রোল</a></li>
                        <li><a href="#mobile-app" class="hover:text-white transition-colors" x-text="t[lang].footer_f5">মোবাইল অ্যাপ</a></li>
                    </ul>
                </div>

                <!-- Column 2: Company -->
                <div class="space-y-3">
                    <h5 class="font-bold text-white text-sm" x-text="t[lang].footer_col2_title">কোম্পানি</h5>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="#comparison" class="hover:text-white transition-colors" x-text="t[lang].footer_about">আমাদের সম্পর্কে</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors" x-text="t[lang].footer_pricing">মূল্যতালিকা</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors" x-text="t[lang].footer_faq">প্রশ্নোত্তর</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors" x-text="t[lang].nav_login">লগইন</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors" x-text="t[lang].nav_get_started">রেজিস্ট্রেশন</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact Details -->
                <div class="space-y-3">
                    <h5 class="font-bold text-white text-sm" x-text="t[lang].footer_col3_title">যোগাযোগ</h5>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-400 text-sm">location_on</span>
                            <span>ধানমন্ডি, ঢাকা, বাংলাদেশ</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-400 text-sm">mail</span>
                            <span>support@pranganhq.com</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-400 text-sm">call</span>
                            <span>+৮৮০ ১৭০০০০০০০০</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright & Legal -->
            <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p x-text="t[lang].footer_copyright">© ২০২৬ PranganHQ। সর্বস্বত্ব সংরক্ষিত।</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-slate-400 transition-colors" x-text="t[lang].footer_terms">ব্যবহারের শর্তাবলি</a>
                    <a href="#" class="hover:text-slate-400 transition-colors" x-text="t[lang].footer_privacy">গোপনীয়তা নীতি</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Application Logic with Bilingual Dictionary -->
    <script>
        function landingApp() {
            return {
                lang: localStorage.getItem('prangan_lang') || 'bn',
                scrolled: false,
                mobileMenuOpen: false,
                demoTab: 'overview',
                billingCycle: 'annual',
                faqOpen: 1,

                setLang(newLang) {
                    this.lang = newLang;
                    localStorage.setItem('prangan_lang', newLang);
                },

                init() {
                    window.addEventListener('scroll', () => {
                        this.scrolled = window.scrollY > 20;
                    });
                },

                t: {
                    bn: {
                        meta_title: 'PranganHQ | প্রিমিয়াম কনভেনশন হল ম্যানেজমেন্ট সিস্টেম',
                        meta_desc: 'বাংলাদেশের #১ কনভেনশন হল, কমিউনিটি সেন্টার ও ব্যাঙ্কুয়েট ম্যানেজমেন্ট সফটওয়্যার। বুকিং, ইনভয়েস, স্টক ও অ্যাকাউন্টিং।',
                        brand_tagline: 'কনভেনশন হল ইআরপি',
                        
                        // Nav
                        nav_features: 'বৈশিষ্ট্যসমূহ',
                        nav_solutions: 'সমাধান',
                        nav_comparison: 'কেন সেরা',
                        nav_mobile_app: 'মোবাইল অ্যাপ',
                        nav_pricing: 'মূল্যতালিকা',
                        nav_faq: 'প্রশ্নোত্তর',
                        nav_login: 'লগইন',
                        nav_get_started: 'শুরু করুন',
                        nav_dashboard: 'ড্যাশবোর্ড',

                        // Hero
                        hero_badge: '✨ বাংলাদেশের #১ স্মার্ট কনভেনশন হল সফটওয়্যার',
                        hero_title_1: 'কনভেনশন হল বুকিং ও ব্যবসা পরিচালনা করুন',
                        hero_title_2: 'সম্পূর্ণ স্মার্ট অটোমেশনে',
                        hero_subtitle: 'রিয়েল-টাইম বুকিং ক্যালেন্ডার, স্বয়ংক্রিয় ইনভয়েস, ভেন্ডর কমিশন, ইনভেন্টরি স্টক ও স্টাফ পে-রোল—সবকিছু এক প্ল্যাটফর্মে। যেকোনো ডিভাইস বা মোবাইল অ্যাপ থেকে দ্রুত পরিচালনা করুন।',
                        hero_cta_primary: '১৪ দিনের ফ্রি ট্রায়াল শুরু করুন',
                        hero_cta_secondary: 'সরাসরি লাইভ প্রিভিউ দেখুন',
                        hero_trust_1: 'কোনো ক্রেডিট কার্ড লাগে না',
                        hero_trust_2: '১ মিনিটে ইনস্ট্যান্ট সেটআপ',
                        hero_trust_3: '২৪/৭ কাস্টমার সাপোর্ট',

                        // Demo Mockup
                        demo_tab_overview: 'ওভারভিউ',
                        demo_tab_bookings: 'বুকিং শিডিউল',
                        demo_tab_financials: 'আয় ও লাভ-ক্ষতি',
                        demo_card_bookings: 'চলতি মাসের বুকিং',
                        demo_val_bookings: '২৪ টি',
                        demo_sub_bookings: '৩টি হল মিলিয়ে',
                        demo_card_revenue: 'মোট রাজস্ব আদায়',
                        demo_val_revenue: '৳ ১২,৫০,০০০',
                        demo_sub_revenue: 'অগ্রিম সহ সর্বমোট',
                        demo_card_profit: 'নিট মুনাফা',
                        demo_val_profit: '৳ ৮,২০,০০০',
                        demo_sub_profit: 'সকল খরচ বাদে',
                        demo_card_occupancy: 'হল বুকিং হার',
                        demo_val_occupancy: '৮৮% বুকড',
                        demo_sub_occupancy: 'উইকএন্ড ফুল স্লট',
                        demo_upcoming_title: 'আসন্ন ইভেন্ট ও শিডিউল',
                        demo_live_sync: '● লাইভ সিঙ্ক',
                        demo_evt_1_name: 'বিবাহের রিসেপশন - গ্র্যান্ড হল',
                        demo_evt_1_time: 'আজ সন্ধ্যা ৭:০০ - রাত ১১:৩০ | ৭০০ মেহমান',
                        demo_status_paid: 'পরিশোধিত',
                        demo_evt_2_name: 'বার্ষিক করপোরেট কনফারেন্স - হল ২',
                        demo_evt_2_time: 'আগামীকাল সকাল ৯:০০ - বিকেল ৫:০০ | ৩০০ মেহমান',
                        demo_status_token: 'টোকেন জমা',
                        demo_evt_3_name: 'জন্মদিন গালা পার্টি - রুফটপ ভেন্যু',
                        demo_evt_3_time: 'শুক্রবার বিকেল ৪:০০ - রাত ৯:০০ | ১৫০ মেহমান',
                        demo_status_confirmed: 'কনফার্মড',
                        demo_cal_title: 'হল অনুযায়ী শিফট ও স্লট ক্যালেন্ডার',
                        demo_cal_sub: 'ডে শিফট / নাইট শিফট ডাবল বুকিং প্রটেকশন',
                        demo_cal_filter: 'গ্র্যান্ড হল এ + হল বি',
                        demo_fin_income: 'আয়ের খাতসমূহ',
                        demo_fin_expense: 'ব্যয়ের বিবরণী',
                        demo_notification: '🔔 নতুন বুকিং টোকেন জমা: ৳ ৫০,০০০',

                        // Stats Bar
                        stat_halls: '১০০+',
                        stat_halls_label: 'কনভেনশন হল ও ভেন্যু',
                        stat_events: '১৫,০০০+',
                        stat_events_label: 'সফল ইভেন্ট সম্পন্ন',
                        stat_accuracy: '০%',
                        stat_accuracy_label: 'ডাবল বুকিং ভুল',
                        stat_support: '২৪/৭',
                        stat_support_label: 'ডেডিকেটেড সহায়তা',

                        // Features
                        feat_badge: 'শক্তিশালী ফিচারসমূহ',
                        feat_title: 'একটি অ্যাপেই হলের সম্পূর্ণ নিয়ন্ত্রণ',
                        feat_subtitle: 'কনভেনশন হল, কমিউনিটি সেন্টার ও ব্যাঙ্কুয়েটের প্রতিটি বিভাগের জন্য তৈরি বিশেষায়িত ডিজিটাল অটোমেশন।',
                        feat_1_title: 'স্মার্ট ক্যালেন্ডার ও বুকিং',
                        feat_1_desc: 'ডে/নাইট শিফট বা আওয়ারলি স্লট বুকিং। অটোমেটিক ডেট লকিং ও রিয়েল-টাইম প্রাপ্যতা যাচাই থাকায় কোনো ডাবল বুকিংয়ের সুযোগ নেই।',
                        feat_1_tag: 'অগ্রিম টোকেন ও স্লট লক',
                        feat_2_title: 'স্বয়ংক্রিয় ইনভয়েস ও বিলিং',
                        feat_2_desc: 'হলের লোগো সহ প্রফেশনাল পিডিএফ ইনভয়েস জেনারেশন। কিস্তিতে পেমেন্ট আদায়, বাকি টাকার হিসাব এবং তাৎক্ষণিক এসএমএস রসিদ পাঠানো।',
                        feat_2_tag: 'পিডিএফ ও ইনস্ট্যান্ট এসএমএস',
                        feat_3_title: 'পূর্ণাঙ্গ আয়-ব্যয় অ্যাকাউন্টিং',
                        feat_3_desc: 'দৈনিক বিদ্যুৎ, ডিজেল, লন্ড্রি, ক্যাটারিং ও মেরামতের খরচ লিপিবদ্ধকরণ। দিনশেষে নিট লাভ-ক্ষতি ও মাসিক ক্যাশফ্লো রিপোর্ট।',
                        feat_3_tag: 'লাভ-ক্ষতি ও ক্যাশফ্লো',
                        feat_4_title: 'ইনভেন্টরি ও সম্পদ ট্র্যাকিং',
                        feat_4_desc: 'চেয়ার, টেবিল, স্টেজ ডেকোরেশন, লাইটিং, সাউন্ড ইকুইপমেন্ট ও ক্রোকারিজের সঠিক স্টক এবং ইভেন্টে আইটেম রিটার্ন/ড্যামেজ ট্র্যাকিং।',
                        feat_4_tag: 'ড্যামেজ ও রিটার্ন ট্র্যাকার',
                        feat_5_title: 'ভেন্ডর কমিশন ও কোঅর্ডিনেশন',
                        feat_5_desc: 'ফটোগ্রাফার, ডেকোরেটর, ক্যাটারার ও ডিজে ভেন্ডরদের কাজের অর্ডার তৈরি এবং তাদের সাথে কমিশনের স্বচ্ছ লেজার ম্যানেজমেন্ট।',
                        feat_5_tag: 'স্বচ্ছ কমিশন লেজার',
                        feat_6_title: 'স্টাফ পে-রোল ও হাজিরা',
                        feat_6_desc: 'স্টাফদের দৈনিক হাজিরা, দায়িত্ব বণ্টন, অ্যাডভান্স লোন ট্র্যাকিং এবং মাস শেষে এক ক্লিকে নির্ভুল স্যালারি শিট ও ভাউচার তৈরি।',
                        feat_6_tag: 'অটো স্যালারি ভাউচার',

                        // Comparison
                        comp_badge: 'পার্থক্যটা স্পষ্ট',
                        comp_title: 'হাতে-কলমে হিসাব বনাম PranganHQ',
                        comp_subtitle: 'দেখুন কেন ঐতিহ্যবাহী ডায়েরি বা এক্সেল শিট বাদ দিয়ে আধুনিক হল মালিকরা PranganHQ ব্যবহার করছেন।',
                        comp_old_title: 'হাতে-কলমে বা এক্সেলে পুরোনো পদ্ধতি',
                        comp_old_sub: 'ঝামেলা ও ভুলভ্রান্তির ঝুঁকি',
                        comp_old_1: 'ডায়েরিতে তারিখ বুকিং রাখায় ডাবল বুকিং ও শিডিউল কনফ্লিক্টের ভয়।',
                        comp_old_2: 'কাগজের রসিদ হারিয়ে যাওয়া বা বাকি টাকার হিসেবে গরমিল হওয়া।',
                        comp_old_3: 'ভেন্ডর কমিশন ও ডেকোরেশন মালামাল ড্যামেজের সঠিক হিসাব না থাকা।',
                        comp_old_4: 'মালিক হলের বাইরে থাকলে ব্যবসার আপডেট পেতে স্টাফদের উপর নির্ভরশীলতা।',
                        comp_new_title: 'PranganHQ স্মার্ট প্ল্যাটফর্ম',
                        comp_new_sub: '১০০% স্বচ্ছ ও স্বয়ংক্রিয়',
                        comp_new_1: 'এক ক্লিকে স্লট বুকিং ও অটো লক; ডাবল বুকিং ০% অসম্ভব।',
                        comp_new_2: 'গ্রাহকের মোবাইলে তাৎক্ষণিক এসএমএস ও হোয়াটসঅ্যাপ ডিজিটাল পিডিএফ বিল।',
                        comp_new_3: 'প্রতিটি ভেন্ডর কমিশন ও ক্রোকারিজ মালামাল রিটার্ন লাইভ ট্র্যাক।',
                        comp_new_4: 'স্মার্টফোন থেকেই হলের রিয়েল-টাইম আয়, ব্যয় ও লাভ পর্যবেক্ষণ।',

                        // Mobile App
                        app_badge: 'অ্যান্ড্রয়েড ও আইওএস সাপোর্টেড',
                        app_title: 'মোবাইল অ্যাপ দিয়ে ব্যবসা পরিচালনা করুন যেকোনো স্থান থেকে',
                        app_subtitle: 'আপনি যেখানেই থাকুন না কেন, আপনার কনভেনশন হলের বুকিং অ্যালার্ট, পেমেন্ট ও প্রতিদিনের খরচের হিসাব সব সময় আপনার হাতের মুঠোয়।',
                        app_f1: 'লাইভ পুশ নোটিফিকেশন',
                        app_f2: 'কিউআর কোড স্ক্যানার',
                        app_f3: 'ইনস্ট্যান্ট ইনভয়েস শেয়ার',
                        app_f4: 'অফলাইন ড্রাফট সেভ',
                        app_web_login: 'ওয়েব ভার্সনে লগইন',

                        // Pricing
                        pricing_badge: 'স্বচ্ছ ও সাশ্রয়ী প্যাকেজ',
                        pricing_title: 'আপনার হলের উপযোগী প্ল্যান বেছে নিন',
                        pricing_subtitle: 'কোনো লুকানো ফি নেই। সকল প্যাকেজেই পাচ্ছেন ১৪ দিনের বিনামূল্যে ট্রায়াল ও ফুল সাপোর্ট।',
                        price_monthly: 'মাসিক বিলিং',
                        price_annual: 'বাৎসরিক বিলিং',
                        price_discount_tag: '২০% ছাড়',
                        price_per_month: '/ মাস',
                        plan_starter_title: 'স্টার্টআপ',
                        plan_starter_desc: 'ছোট বা একক কনভেনশন হলের জন্য উপযুক্ত',
                        plan_starter_price_mo: '৳ ৩,৯৯৯',
                        plan_starter_price_yr: '৳ ৩,১৯৯',
                        plan_starter_f1: '১টি কনভেনশন হল ব্যবস্থাপনা',
                        plan_starter_f2: 'আনলিমিটেড বুকিং ও ক্যালেন্ডার',
                        plan_starter_f3: 'পিডিএফ ইনভয়েস ও রসিদ তৈরি',
                        plan_starter_f4: 'বেসিক আয়-ব্যয় হিসাব',
                        plan_starter_f5: 'ইনভেন্টরি ও স্টাফ পে-রোল',
                        plan_btn_trial: 'ফ্রি শুরু করুন',

                        plan_popular_badge: 'সর্বাধিক জনপ্রিয়',
                        plan_pro_title: 'প্রফেশনাল',
                        plan_pro_desc: 'মাঝারি ও একাধিক ভেন্যু বিশিষ্ট কনভেনশন সেন্টারের জন্য',
                        plan_pro_price_mo: '৳ ৭,৯৯৯',
                        plan_pro_price_yr: '৳ ৬,৩৯৯',
                        plan_pro_f1: '৩টি পর্যন্ত হল / ভেন্যু ম্যানেজমেন্ট',
                        plan_pro_f2: 'ইনভেন্টরি ও সম্পদ ট্র্যাকিং',
                        plan_pro_f3: 'স্টাফ হাজিরা ও স্যালারি পে-রোল',
                        plan_pro_f4: 'ভেন্ডর কমিশন ও কোঅর্ডিনেশন',
                        plan_pro_f5: 'মোবাইল অ্যাপ ফুল এক্সেস',
                        plan_btn_pro: 'প্রফেশনাল শুরু করুন',

                        plan_ent_title: 'এন্টারপ্রাইজ',
                        plan_ent_desc: 'বড় কনভেনশন চেইন ও মাল্টি-ব্রাঞ্চ সেন্টারের জন্য',
                        plan_ent_price_mo: '৳ ১৩,৯৯৯',
                        plan_ent_price_yr: '৳ ১১,১৯৯',
                        plan_ent_f1: 'আনলিমিটেড হল ও মাল্টি-ব্রাঞ্চ',
                        plan_ent_f2: 'কাস্টম এসএমএস ও ডোমেইন ব্যান্ডিং',
                        plan_ent_f3: 'ডেডিকেটেড অ্যাকাউন্ট ম্যানেজার',
                        plan_ent_f4: 'অন-সাইট ট্রেইনিং ও ডাটা মাইগ্রেশন',
                        plan_ent_f5: '২৪/৭ ভিআইপি সাপোর্ট',
                        plan_btn_ent: 'এন্টারপ্রাইজ নিন',

                        // Testimonials
                        test_badge: 'গ্রাহকদের সন্তুষ্টি',
                        test_title: 'হল মালিক ও ম্যানেজাররা যা বলছেন',
                        test_1_quote: '"আগে ডাবল বুকিং নিয়ে সবসময় আতঙ্কে থাকতাম। PranganHQ ব্যবহার শুরু করার পর থেকে বুকিং শিডিউল ১০০% নির্ভুল এবং ইনভয়েস এক ক্লিকেই তৈরি হয়ে যায়।"',
                        test_1_name: 'রফিকুল ইসলাম',
                        test_1_role: 'ম্যানেজিং ডিরেক্টর, গ্র্যান্ড রয়েল কনভেনশন (ঢাকা)',
                        test_2_quote: '"মোবাইল অ্যাপটি অসাধারণ! আমি বাইরে থাকলেও হলের প্রতিদিনের কালেকশন ও বুকিং দেখতে পারি। আমাদের স্টাফদের স্যালারি শিটও এখন এই অ্যাপ দিয়েই তৈরি হয়।"',
                        test_2_name: 'আশরাফুল হক',
                        test_2_role: 'স্বত্বাধিকারী, উৎসব কমিউনিটি সেন্টার (চট্টগ্রাম)',
                        test_3_quote: '"ভেন্ডর কমিশন ও ইনভেন্টরির হিসাব মেলাতে আগে অনেক সময় নষ্ট হতো। এখন ক্যাটারিং ও ডেকোরেশনের প্রতিটি হিসাব নির্ভুলভাবে ট্র্যাক করতে পারি।"',
                        test_3_name: 'তানভীর আহমেদ',
                        test_3_role: 'জেনারেল ম্যানেজার, প্যারাডাইস ব্যাঙ্কুয়েট (সিলেট)',

                        // FAQ
                        faq_badge: 'সাধারণ জিজ্ঞাসা',
                        faq_title: 'প্রায়শই জিজ্ঞাসিত প্রশ্নাবলি',
                        faq_q1: 'সফটওয়্যারটি ব্যবহার শুরু করতে কী কী প্রয়োজন?',
                        faq_a1: 'কোনো বিশেষ হার্ডওয়্যার বা ইন্সটলেশনের প্রয়োজন নেই। ইন্টারনেট সংযোগযুক্ত যেকোনো কম্পিউটার, ল্যাপটপ বা স্মার্টফোন থেকেই সরাসরি ব্রাউজার বা অ্যাপের মাধ্যমে আপনি PranganHQ ব্যবহার করতে পারবেন।',
                        faq_q2: 'আমার একাধিক হল বা ভেন্যু থাকলে কীভাবে ম্যানেজ করব?',
                        faq_a2: 'আমাদের প্রফেশনাল ও এন্টারপ্রাইজ প্যাকেজে মাল্টি-হল সাপোর্ট রয়েছে। আপনি একই অ্যাকাউন্ট থেকে আলাদা আলাদা হলের বুকিং ক্যালেন্ডার, শিফট ও আয়-ব্যয়ের হিসাব একক ড্যাশবোর্ডেই দেখতে পাবেন।',
                        faq_q3: 'আমাদের হলের তথ্য ও ডাটা কতটা নিরাপদ?',
                        faq_a3: 'আপনার ডাটা সুরক্ষিত ক্লাউড সার্ভারে ২৫৬-বিট এসএসএল এনক্রিপশনের মাধ্যমে সংরক্ষিত থাকে। প্রতিদিন স্বয়ংক্রিয় ক্লাউড ব্যাকআপ নেওয়া হয়, যাতে যেকোনো পরিস্থিতিতে আপনার মূল্যবান তথ্য সম্পূর্ণ নিরাপদ থাকে।',
                        faq_q4: 'বিকাশ, নগদ বা ব্যাংকের মাধ্যমে পেমেন্ট করা যাবে কি?',
                        faq_a4: 'হ্যাঁ, সাবস্ক্রিপশন ফি বিকাশ, নগদ, রকেট, ডেবিট/ক্রেডিট কার্ড অথবা সরাসরি ব্যাংক ট্রান্সফারের মাধ্যমে সহজেই পরিশোধ করা যায়।',

                        // Bottom CTA & Footer
                        cta_banner_title: 'আপনার কনভেনশন হলকে আজই ডিজিটাল ও স্মার্ট করুন',
                        cta_banner_sub: 'কোনো ইনস্টলেশন চার্জ নেই। ১৪ দিনের ফ্রি ট্রায়ালে শুরু করে দেখুন কীভাবে আপনার ব্যবসার সময় ও খরচ বাঁচে।',
                        cta_banner_btn: 'ফ্রি ট্রায়াল শুরু করুন',
                        cta_banner_call: 'ফোন করুন: +৮৮০ ১৭০০০০০০০০',
                        footer_desc: 'বাংলাদেশের প্রথম পূর্ণাঙ্গ ক্লাউড-ভিত্তিক কনভেনশন হল ও ব্যাঙ্কুয়েট ম্যানেজমেন্ট সফটওয়্যার। বুকিং, ইনভয়েসিং, পে-রোল ও ইনভেন্টরি অটোমেশনের নির্ভরযোগ্য প্ল্যাটফর্ম।',
                        footer_col1_title: 'মডিউলসমূহ',
                        footer_f1: 'বুকিং ক্যালেন্ডার',
                        footer_f2: 'স্বয়ংক্রিয় ইনভয়েস',
                        footer_f3: 'ইনভেন্টরি ট্র্যাকিং',
                        footer_f4: 'স্টাফ ও পে-রোল',
                        footer_f5: 'মোবাইল অ্যাপ',
                        footer_col2_title: 'কোম্পানি',
                        footer_about: 'আমাদের সম্পর্কে',
                        footer_pricing: 'মূল্যতালিকা',
                        footer_faq: 'প্রশ্নোত্তর',
                        footer_col3_title: 'যোগাযোগ',
                        footer_copyright: '© ২০২৬ PranganHQ। সর্বস্বত্ব সংরক্ষিত।',
                        footer_terms: 'ব্যবহারের শর্তাবলি',
                        footer_privacy: 'গোপনীয়তা নীতি',
                    },

                    en: {
                        meta_title: 'PranganHQ | Premium Convention Hall Management ERP',
                        meta_desc: "Bangladesh's #1 Convention Hall, Banquet & Community Center Management Platform. Bookings, Invoicing, Staff & Accounting.",
                        brand_tagline: 'Convention Hall ERP',

                        // Nav
                        nav_features: 'Features',
                        nav_solutions: 'Solutions',
                        nav_comparison: 'Why Us',
                        nav_mobile_app: 'Mobile App',
                        nav_pricing: 'Pricing',
                        nav_faq: 'FAQ',
                        nav_login: 'Log In',
                        nav_get_started: 'Get Started',
                        nav_dashboard: 'Dashboard',

                        // Hero
                        hero_badge: "✨ Bangladesh's #1 Smart Convention Hall ERP",
                        hero_title_1: 'Run Your Convention Hall Bookings & Business with',
                        hero_title_2: 'Flawless Smart Automation',
                        hero_subtitle: 'Real-time booking calendar, automated branded invoices, vendor commissions, inventory tracking, and staff payroll—all in one unified platform.',
                        hero_cta_primary: 'Start 14-Day Free Trial',
                        hero_cta_secondary: 'Watch Live Preview',
                        hero_trust_1: 'No credit card required',
                        hero_trust_2: 'Instant 1-minute setup',
                        hero_trust_3: '24/7 Priority support',

                        // Demo Mockup
                        demo_tab_overview: 'Overview',
                        demo_tab_bookings: 'Booking Schedule',
                        demo_tab_financials: 'Income & P&L',
                        demo_card_bookings: 'Monthly Bookings',
                        demo_val_bookings: '24 Events',
                        demo_sub_bookings: 'Across 3 Halls',
                        demo_card_revenue: 'Total Revenue',
                        demo_val_revenue: '৳ 12,50,000',
                        demo_sub_revenue: 'Including Advances',
                        demo_card_profit: 'Net Profit',
                        demo_val_profit: '৳ 8,20,000',
                        demo_sub_profit: 'After All Expenses',
                        demo_card_occupancy: 'Occupancy Rate',
                        demo_val_occupancy: '88% Occupied',
                        demo_sub_occupancy: 'Weekend Peak Slots',
                        demo_upcoming_title: 'Upcoming Events & Schedule',
                        demo_live_sync: '● Live Sync',
                        demo_evt_1_name: 'Grand Wedding Reception - Hall A',
                        demo_evt_1_time: 'Today 7:00 PM - 11:30 PM | 700 Guests',
                        demo_status_paid: 'Paid in Full',
                        demo_evt_2_name: 'Annual Corporate Summit - Hall B',
                        demo_evt_2_time: 'Tomorrow 9:00 AM - 5:00 PM | 300 Guests',
                        demo_status_token: 'Token Paid',
                        demo_evt_3_name: 'Birthday Gala Party - Rooftop Venue',
                        demo_evt_3_time: 'Friday 4:00 PM - 9:00 PM | 150 Guests',
                        demo_status_confirmed: 'Confirmed',
                        demo_cal_title: 'Multi-Hall Shift & Slot Calendar',
                        demo_cal_sub: 'Day/Night Shift Double-Booking Protection',
                        demo_cal_filter: 'Grand Hall A + Hall B',
                        demo_fin_income: 'Revenue Sources',
                        demo_fin_expense: 'Expense Breakdown',
                        demo_notification: '🔔 Advance Token Received: ৳ 50,000',

                        // Stats Bar
                        stat_halls: '100+',
                        stat_halls_label: 'Convention Halls & Venues',
                        stat_events: '15,000+',
                        stat_events_label: 'Successful Events Hosted',
                        stat_accuracy: '0%',
                        stat_accuracy_label: 'Double-Booking Conflicts',
                        stat_support: '24/7',
                        stat_support_label: 'Dedicated Support',

                        // Features
                        feat_badge: 'Powerful Modules',
                        feat_title: 'Complete Hall Control in One Single Hub',
                        feat_subtitle: 'Tailored digital workflows engineered specifically for convention halls, banquets, and community centers.',
                        feat_1_title: 'Smart Calendar & Booking',
                        feat_1_desc: 'Day/Night shift and hourly slot booking. Automatic date locking and real-time availability prevent costly double-booking errors.',
                        feat_1_tag: 'Advance Tokens & Slot Locking',
                        feat_2_title: 'Automated Invoicing & Billing',
                        feat_2_desc: 'Generate branded PDF invoices in seconds. Track installments, remaining balances, and send instant SMS receipt notifications.',
                        feat_2_tag: 'PDF & Instant SMS Receipts',
                        feat_3_title: 'Full P&L Accounting',
                        feat_3_desc: 'Log electricity, generator fuel, laundry, catering, and maintenance costs. Get instant daily P&L and monthly cashflow reports.',
                        feat_3_tag: 'Profit & Loss & Cashflow',
                        feat_4_title: 'Inventory & Asset Tracking',
                        feat_4_desc: 'Keep accurate counts of chairs, stage decor, sound systems, lights, and crockery with event damage & return logs.',
                        feat_4_tag: 'Damage & Return Tracking',
                        feat_5_title: 'Vendor Commission & Coordination',
                        feat_5_desc: 'Issue work orders to photographers, decorators, and caterers while maintaining a crystal-clear commission ledger.',
                        feat_5_tag: 'Transparent Commission Ledger',
                        feat_6_title: 'Staff Payroll & Attendance',
                        feat_6_desc: 'Track daily attendance, shift duties, salary advances, and generate accurate monthly pay slips with one click.',
                        feat_6_tag: 'Auto Salary Vouchers',

                        // Comparison
                        comp_badge: 'Clear Difference',
                        comp_title: 'Manual Methods vs PranganHQ ERP',
                        comp_subtitle: 'See why modern venue owners are abandoning paper diaries and messy spreadsheets for PranganHQ.',
                        comp_old_title: 'Traditional Paper Diary / Spreadsheets',
                        comp_old_sub: 'Prone to Costly Errors & Confusion',
                        comp_old_1: 'Handwritten entries cause double-booking risks and schedule clashes.',
                        comp_old_2: 'Paper receipts get lost and due payments go uncollected.',
                        comp_old_3: 'No record of damaged decor inventory or vendor commissions.',
                        comp_old_4: 'Owners cannot check business performance when away from the hall.',
                        comp_new_title: 'PranganHQ Smart Platform',
                        comp_new_sub: '100% Transparent & Automated',
                        comp_new_1: 'One-click slot locking; double bookings are completely impossible.',
                        comp_new_2: 'Instant digital PDF invoices sent via SMS and WhatsApp.',
                        comp_new_3: 'Live ledger for every vendor commission and crockery asset.',
                        comp_new_4: 'Real-time revenue, booking, and profit metrics on your smartphone.',

                        // Mobile App
                        app_badge: 'Android & iOS Supported',
                        app_title: 'Run Your Convention Business On-The-Go',
                        app_subtitle: 'Whether traveling or at home, stay connected to your hall bookings, payment collections, and expenses in real-time.',
                        app_f1: 'Instant Push Alerts',
                        app_f2: 'QR Code Guest Check-in',
                        app_f3: 'Share Invoices via WhatsApp',
                        app_f4: 'Offline Draft Sync',
                        app_web_login: 'Open Web Portal',

                        // Pricing
                        pricing_badge: 'Transparent & Affordable',
                        pricing_title: 'Choose the Right Plan for Your Hall',
                        pricing_subtitle: 'No hidden fees. Every package comes with a 14-day risk-free trial and priority support.',
                        price_monthly: 'Monthly Billing',
                        price_annual: 'Annual Billing',
                        price_discount_tag: 'Save 20%',
                        price_per_month: '/ mo',
                        plan_starter_title: 'Starter',
                        plan_starter_desc: 'Perfect for single hall or boutique venues',
                        plan_starter_price_mo: '৳ 3,999',
                        plan_starter_price_yr: '৳ 3,199',
                        plan_starter_f1: '1 Convention Hall Management',
                        plan_starter_f2: 'Unlimited Bookings & Calendar',
                        plan_starter_f3: 'Branded PDF Invoices & Receipts',
                        plan_starter_f4: 'Basic Income & Expense Tracking',
                        plan_starter_f5: 'Inventory & Staff Payroll',
                        plan_btn_trial: 'Start Free Trial',

                        plan_popular_badge: 'Most Popular',
                        plan_pro_title: 'Professional',
                        plan_pro_desc: 'Ideal for medium & multi-venue convention centers',
                        plan_pro_price_mo: '৳ 7,999',
                        plan_pro_price_yr: '৳ 6,399',
                        plan_pro_f1: 'Up to 3 Halls / Venues',
                        plan_pro_f2: 'Asset & Inventory Damage Tracker',
                        plan_pro_f3: 'Staff Attendance & Payroll',
                        plan_pro_f4: 'Vendor Commission Ledger',
                        plan_pro_f5: 'Full Mobile App Access',
                        plan_btn_pro: 'Get Professional',

                        plan_ent_title: 'Enterprise',
                        plan_ent_desc: 'Engineered for large chains & multi-branch centers',
                        plan_ent_price_mo: '৳ 13,999',
                        plan_ent_price_yr: '৳ 11,199',
                        plan_ent_f1: 'Unlimited Halls & Multi-Branch',
                        plan_ent_f2: 'Custom SMS Gateway & Branding',
                        plan_ent_f3: 'Dedicated Account Manager',
                        plan_ent_f4: 'On-site Training & Data Migration',
                        plan_ent_f5: '24/7 VIP Phone Support',
                        plan_btn_ent: 'Get Enterprise',

                        // Testimonials
                        test_badge: 'Client Reviews',
                        test_title: 'Loved by Hall Owners & Event Managers',
                        test_1_quote: '"We used to dread double-booking conflicts during wedding seasons. Since switching to PranganHQ, our booking schedule is 100% synchronized and invoices take 10 seconds."',
                        test_1_name: 'Rafiqul Islam',
                        test_1_role: 'Managing Director, Grand Royal Convention (Dhaka)',
                        test_2_quote: '"The mobile app is a game-changer! I can monitor cash collections and approved bookings from anywhere. Staff payroll is also handled seamlessly."',
                        test_2_name: 'Ashraful Haque',
                        test_2_role: 'Owner, Utshob Community Center (Chittagong)',
                        test_3_quote: '"Tracking vendor commissions and stage decor inventory used to take hours of manual paperwork. PranganHQ streamlined our whole banquet operations."',
                        test_3_name: 'Tanvir Ahmed',
                        test_3_role: 'General Manager, Paradise Banquet (Sylhet)',

                        // FAQ
                        faq_badge: 'Help & FAQ',
                        faq_title: 'Frequently Asked Questions',
                        faq_q1: 'What do I need to get started with PranganHQ?',
                        faq_a1: 'No specialized hardware or installation required. You can access PranganHQ from any browser or smartphone app on PC, laptop, tablet, or mobile phone.',
                        faq_q2: 'Can I manage multiple halls under one account?',
                        faq_a2: 'Yes, our Professional and Enterprise plans support multi-hall operations. You can monitor bookings, shifts, and accounting for all venues in a single master dashboard.',
                        faq_q3: 'How secure is our venue data?',
                        faq_a3: 'Your data is hosted on enterprise-grade cloud servers with 256-bit SSL encryption and automated daily backups to guarantee maximum reliability and security.',
                        faq_q4: 'Which payment methods are accepted for subscriptions?',
                        faq_a4: 'We accept bKash, Nagad, Rocket, Credit/Debit cards, and direct bank transfers.',

                        // Bottom CTA & Footer
                        cta_banner_title: 'Transform Your Convention Hall Today',
                        cta_banner_sub: 'Zero setup fees. Start your 14-day free trial and experience how much time and money you save.',
                        cta_banner_btn: 'Start Free Trial',
                        cta_banner_call: 'Call Us: +880 1700000000',
                        footer_desc: "Bangladesh's premier cloud ERP for convention halls, banquets, and community centers. Reliable booking, billing, payroll, and inventory automation.",
                        footer_col1_title: 'Modules',
                        footer_f1: 'Booking Calendar',
                        footer_f2: 'Automated Invoicing',
                        footer_f3: 'Inventory Tracker',
                        footer_f4: 'Staff & Payroll',
                        footer_f5: 'Mobile App',
                        footer_col2_title: 'Company',
                        footer_about: 'About Us',
                        footer_pricing: 'Pricing Plans',
                        footer_faq: 'FAQ',
                        footer_col3_title: 'Contact',
                        footer_copyright: '© 2026 PranganHQ. All rights reserved.',
                        footer_terms: 'Terms of Service',
                        footer_privacy: 'Privacy Policy',
                    }
                }
            }
        }
    </script>
</body>
</html>
