<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $brandName = 'মমতা কমিউনিটি সেন্টার';
        $brandNameEn = 'MOMOTA COMMUNITY CENTER';
        $phone = $venue->phone ?: '01700000000';
        $phoneHref = preg_replace('/[^0-9+]/', '', $phone);
        $address = $venue->address ?: 'ঢাকা, বাংলাদেশ';
        $capacity = max(1, (int) ($venue->capacity ?? 500));
        $price = max(0, (float) ($venue->price_per_slot ?? 30000));
        $bookingUrl = auth()->check() ? route('bookings.create') : route('login');
        $logo = $venue->logo_path && file_exists(public_path('storage/' . $venue->logo_path))
            ? asset('storage/' . $venue->logo_path)
            : asset('logo.png');
        $toBn = function ($value) {
            return strtr((string) $value, [
                '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪',
                '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯',
            ]);
        };
        $priceBn = $toBn(number_format($price));
        $capacityBn = $toBn(number_format($capacity));
        $gallery = [
            ['src' => asset('images/landing/hero.webp'), 'alt' => 'মমতা কমিউনিটি সেন্টারের মূল হল', 'caption' => 'মূল হল', 'class' => 'col-span-2 row-span-2'],
            ['src' => asset('images/landing/venue-table.webp'), 'alt' => 'টেবিল সাজসজ্জা ও ফুলের ব্যবস্থা', 'caption' => 'টেবিল সাজসজ্জা', 'class' => 'col-span-2'],
            ['src' => asset('images/landing/gallery-ceremony.webp'), 'alt' => 'বিয়ে ও গায়ে হলুদের আয়োজন', 'caption' => 'বিয়ে ও গায়ে হলুদ', 'class' => ''],
            ['src' => asset('images/landing/gallery-corporate.webp'), 'alt' => 'কর্পোরেট সেমিনার ও কনফারেন্স', 'caption' => 'কর্পোরেট আয়োজন', 'class' => ''],
            ['src' => asset('images/landing/gallery-dining.webp'), 'alt' => 'ক্যাটারিং ও ডাইনিং ব্যবস্থা', 'caption' => 'ক্যাটারিং ও ডাইনিং', 'class' => 'col-span-2'],
            ['src' => asset('images/landing/gallery-hall.webp'), 'alt' => 'বড় আয়োজনের জন্য প্রশস্ত হল', 'caption' => 'প্রশস্ত হল', 'class' => 'col-span-2'],
        ];
    @endphp

    <title>{{ $brandName }} | বিয়ে, গায়ে হলুদ ও কর্পোরেট আয়োজনের ভেন্যু</title>
    <meta name="description" content="মমতা কমিউনিটি সেন্টার—বিয়ে, গায়ে হলুদ, মেহেদি, আকদ, জন্মদিন ও কর্পোরেট আয়োজনের জন্য প্রশস্ত, শীতাতপ নিয়ন্ত্রিত ভেন্যু। বুকিং ও ভেন্যু পরিদর্শনের জন্য যোগাযোগ করুন।">
    <meta name="theme-color" content="#004D40">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $brandName }} | আপনার বিশেষ দিনের ভেন্যু">
    <meta property="og:description" content="বিয়ে, গায়ে হলুদ, আকদ, জন্মদিন ও কর্পোরেট আয়োজনের জন্য একটি উষ্ণ ও প্রশস্ত ভেন্যু।">
    <meta property="og:image" content="{{ asset('images/landing/hero.webp') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/png" href="{{ $logo }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --teal: #004D40;
            --teal-deep: #003028;
            --teal-soft: #006D5B;
            --gold: #F5A623;
            --gold-deep: #B07A12;
            --ink: #122B27;
            --muted: #5A7175;
            --cream: #FBF8F1;
            --cream-deep: #F4EEE2;
            --line: rgba(0, 77, 64, .12);
        }

        html { scroll-behavior: smooth; }
        section[id] { scroll-margin-top: 92px; }
        #top { scroll-margin-top: 0; }

        body {
            background: var(--cream);
            color: var(--ink);
            font-family: 'Anek Bangla', 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .font-display {
            font-family: 'Anek Bangla', 'Inter', sans-serif;
            font-weight: 800;
            letter-spacing: -.025em;
        }

        .eyebrow {
            font-family: 'Manrope', 'Inter', sans-serif;
            font-weight: 800;
            letter-spacing: .2em;
            text-transform: uppercase;
        }

        .hero-mesh {
            background:
                radial-gradient(circle at 15% 8%, rgba(245, 166, 35, .13), transparent 30%),
                radial-gradient(circle at 88% 18%, rgba(0, 109, 91, .10), transparent 36%),
                radial-gradient(circle at 50% 100%, rgba(0, 77, 64, .05), transparent 42%),
                var(--cream);
        }

        .dot-grid {
            background-image: radial-gradient(rgba(0, 77, 64, .16) 1px, transparent 1px);
            background-size: 22px 22px;
            mask-image: linear-gradient(to bottom, black, transparent 80%);
            -webkit-mask-image: linear-gradient(to bottom, black, transparent 80%);
        }

        .gold-rule {
            height: 3px;
            width: 52px;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--gold), rgba(245, 166, 35, .25));
        }

        .venue-shadow {
            box-shadow: 0 34px 90px -34px rgba(0, 48, 40, .55);
        }

        .card-lift {
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        }
        .card-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 48px -26px rgba(0, 48, 40, .38);
        }

        .gallery-item img {
            transition: transform .7s cubic-bezier(.2, .7, .2, 1);
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .reveal {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .7s ease, transform .7s cubic-bezier(.2, .7, .2, 1);
            will-change: opacity, transform;
        }
        .reveal.is-visible {
            opacity: 1;
            transform: none;
        }

        @keyframes floaty {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-9px); }
        }
        .animate-floaty { animation: floaty 7s ease-in-out infinite; }
        .animate-floaty-slow { animation: floaty 9s ease-in-out infinite; }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .reveal { opacity: 1; transform: none; transition: none; }
            .animate-floaty, .animate-floaty-slow { animation: none; }
            .gallery-item img { transition: none; }
        }

        [x-cloak] { display: none !important; }
    </style>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'EventVenue',
            'name' => 'মমতা কমিউনিটি সেন্টার',
            'description' => 'বিয়ে, গায়ে হলুদ, আকদ, জন্মদিন ও কর্পোরেট আয়োজনের জন্য কমিউনিটি সেন্টার।',
            'image' => asset('images/landing/hero.webp'),
            'telephone' => $phone,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressCountry' => 'BD',
            ],
            'url' => url()->current(),
            'maximumAttendeeCapacity' => $capacity,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>

<body
    class="antialiased overflow-x-hidden"
    x-data="{ mobileMenuOpen: false, scrolled: false, activeFaq: 1, lightbox: null }"
    @scroll.window="scrolled = window.scrollY > 24"
    :class="mobileMenuOpen ? 'overflow-hidden' : ''"
>
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[120] focus:rounded-xl focus:bg-[#004D40] focus:px-4 focus:py-2 focus:text-white">মূল কনটেন্টে যান</a>

    <!-- Header -->
    <header
        class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
        :class="scrolled ? 'bg-[#FBF8F1]/95 backdrop-blur-xl shadow-[0_12px_40px_-24px_rgba(0,48,40,.45)] border-b border-[#004D40]/10' : 'bg-transparent'"
    >
        <div class="mx-auto flex h-[76px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="#top" class="flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5A623] rounded-2xl">
                <img src="{{ $logo }}" alt="{{ $brandName }} লোগো" class="h-12 w-12 rounded-2xl bg-white object-contain p-1 shadow-sm ring-1 ring-[#004D40]/10">
                <span class="flex flex-col leading-none">
                    <span class="text-[19px] font-extrabold tracking-tight text-[#004D40]">মমতা</span>
                    <span class="mt-1 text-[9px] font-extrabold tracking-[0.2em] text-[#B07A12]">কমিউনিটি সেন্টার</span>
                </span>
            </a>

            <nav class="hidden items-center gap-7 lg:flex" aria-label="প্রধান মেনু">
                <a href="#venue" class="text-[13px] font-bold text-[#2F4A46] transition-colors hover:text-[#004D40]">ভেন্যু</a>
                <a href="#events" class="text-[13px] font-bold text-[#2F4A46] transition-colors hover:text-[#004D40]">আয়োজন</a>
                <a href="#amenities" class="text-[13px] font-bold text-[#2F4A46] transition-colors hover:text-[#004D40]">সুবিধা</a>
                <a href="#gallery" class="text-[13px] font-bold text-[#2F4A46] transition-colors hover:text-[#004D40]">গ্যালারি</a>
                <a href="#faq" class="text-[13px] font-bold text-[#2F4A46] transition-colors hover:text-[#004D40]">প্রশ্নোত্তর</a>
                <a href="#contact" class="text-[13px] font-bold text-[#2F4A46] transition-colors hover:text-[#004D40]">যোগাযোগ</a>
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="tel:{{ $phoneHref }}" class="hidden h-10 w-10 items-center justify-center rounded-full border border-[#004D40]/[0.15] text-[#004D40] transition-colors hover:bg-white sm:flex" aria-label="ফোন করুন">
                    <span class="material-symbols-outlined text-[20px]">call</span>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="hidden items-center gap-2 rounded-full bg-[#004D40] px-5 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-[#004D40]/20 transition-all hover:bg-[#006D5B] sm:inline-flex">
                        <span class="material-symbols-outlined text-[17px] text-[#F5A623]">dashboard</span>
                        ড্যাশবোর্ড
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-full px-3 py-2 text-xs font-extrabold text-[#2F4A46] transition-colors hover:bg-white hover:text-[#004D40] sm:inline-flex">লগইন</a>
                    <a href="{{ $bookingUrl }}" class="hidden items-center gap-1.5 rounded-full bg-[#004D40] px-5 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-[#004D40]/20 transition-all hover:bg-[#006D5B] sm:inline-flex">
                        বুকিং করুন
                        <span class="material-symbols-outlined text-[16px] text-[#F5A623]">arrow_outward</span>
                    </a>
                @endauth

                <a href="{{ $bookingUrl }}" class="inline-flex items-center gap-1 rounded-full bg-[#F5A623] px-4 py-2.5 text-xs font-extrabold text-[#003028] shadow-md shadow-[#F5A623]/25 sm:hidden">
                    বুকিং
                    <span class="material-symbols-outlined text-[15px]">arrow_outward</span>
                </a>

                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-[#004D40]/[0.15] bg-white/70 text-[#004D40] lg:hidden"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen.toString()"
                    aria-controls="mobile-menu"
                    aria-label="মেনু খুলুন"
                >
                    <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div
            id="mobile-menu"
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-3"
            class="border-t border-[#004D40]/10 bg-[#FBF8F1] px-5 pb-6 pt-3 shadow-xl lg:hidden"
        >
            <nav class="flex flex-col" aria-label="মোবাইল মেনু">
                <a href="#venue" @click="mobileMenuOpen = false" class="border-b border-[#004D40]/[0.08] py-3.5 text-sm font-bold text-[#2F4A46]">ভেন্যু</a>
                <a href="#events" @click="mobileMenuOpen = false" class="border-b border-[#004D40]/[0.08] py-3.5 text-sm font-bold text-[#2F4A46]">আয়োজন</a>
                <a href="#amenities" @click="mobileMenuOpen = false" class="border-b border-[#004D40]/[0.08] py-3.5 text-sm font-bold text-[#2F4A46]">সুবিধা</a>
                <a href="#gallery" @click="mobileMenuOpen = false" class="border-b border-[#004D40]/[0.08] py-3.5 text-sm font-bold text-[#2F4A46]">গ্যালারি</a>
                <a href="#faq" @click="mobileMenuOpen = false" class="border-b border-[#004D40]/[0.08] py-3.5 text-sm font-bold text-[#2F4A46]">প্রশ্নোত্তর</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="py-3.5 text-sm font-bold text-[#2F4A46]">যোগাযোগ</a>
            </nav>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <a href="tel:{{ $phoneHref }}" class="flex items-center justify-center gap-2 rounded-xl border border-[#004D40]/[0.15] bg-white px-4 py-3 text-xs font-extrabold text-[#004D40]">
                    <span class="material-symbols-outlined text-[18px]">call</span>
                    ফোন করুন
                </a>
                <a href="{{ $bookingUrl }}" class="flex items-center justify-center gap-2 rounded-xl bg-[#004D40] px-4 py-3 text-xs font-extrabold text-white">
                    বুকিং করুন
                    <span class="material-symbols-outlined text-[17px] text-[#F5A623]">arrow_outward</span>
                </a>
            </div>
            @guest
                <a href="{{ route('login') }}" class="mt-3 flex items-center justify-center rounded-xl py-2.5 text-xs font-extrabold text-[#004D40]">অ্যাকাউন্টে লগইন করুন</a>
            @endguest
        </div>
    </header>

    <main id="main">
        <!-- Hero -->
        <section id="top" class="hero-mesh relative overflow-hidden pb-16 pt-28 sm:pt-32 lg:pb-24 lg:pt-36">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-[520px] dot-grid opacity-60"></div>
            <div class="pointer-events-none absolute -right-24 top-24 h-72 w-72 rounded-full bg-[#F5A623]/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-[#004D40]/10 blur-3xl"></div>

            <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1.02fr_0.98fr] lg:gap-16 lg:px-8">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#004D40]/[0.12] bg-white/70 px-3.5 py-1.5 text-[11px] font-extrabold text-[#004D40] shadow-sm backdrop-blur">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#F5A623] opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#F5A623]"></span>
                        </span>
                        {{ $address }} · দিন ও রাতের স্লট
                    </div>

                    <h1 class="font-display mt-5 text-[2.7rem] leading-[1.08] text-[#122B27] sm:text-6xl lg:text-[3.4rem] xl:text-[4.35rem]">
                        আপনার বিশেষ দিন,<br>
                        <span class="relative inline-block text-[#004D40]">
                            আমাদের প্রিয় ভেন্যু
                            <svg class="absolute -bottom-2 left-0 h-3 w-full text-[#F5A623]" viewBox="0 0 300 14" fill="none" preserveAspectRatio="none" aria-hidden="true">
                                <path d="M2 10C52 3 101 3 151 7C199 10 248 11 298 4" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </h1>

                    <p class="mt-7 max-w-xl text-[15px] leading-8 text-[#5A7175] sm:text-base">
                        বিয়ে, গায়ে হলুদ, মেহেদি, আকদ থেকে জন্মদিন ও কর্পোরেট আয়োজন—সব কিছু এক ছাদের নিচে, আপনার পরিকল্পনায়। প্রশস্ত হল, উষ্ণ আতিথেয়তা আর একটু যত্ন—এই তিনেই আমরা বিশ্বাস করি।
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center lg:flex-col xl:flex-row">
                        <a href="{{ $bookingUrl }}" class="group inline-flex w-full items-center justify-center gap-2 whitespace-nowrap rounded-full bg-[#004D40] px-7 py-3.5 text-sm font-extrabold text-white shadow-xl shadow-[#004D40]/20 transition-all hover:-translate-y-0.5 hover:bg-[#006D5B] sm:w-auto lg:w-full xl:w-auto">
                            বুকিং শুরু করুন
                            <span class="material-symbols-outlined text-[19px] text-[#F5A623] transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </a>
                        <a href="#venue" class="inline-flex w-full items-center justify-center gap-2 whitespace-nowrap rounded-full border border-[#004D40]/[0.18] bg-white/[0.55] px-6 py-3.5 text-sm font-extrabold text-[#004D40] backdrop-blur transition-all hover:bg-white sm:w-auto lg:w-full xl:w-auto">
                            <span class="material-symbols-outlined text-[19px]">play_circle</span>
                            ভেন্যু ঘুরে দেখুন
                        </a>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3 text-[12px] font-bold text-[#44605B]">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#B07A12]">groups</span>
                            {{ $capacityBn }}+ অতিথি
                        </span>
                        <span class="h-4 w-px bg-[#004D40]/[0.15]"></span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#B07A12]">wb_sunny</span>
                            দিন ও রাতের স্লট
                        </span>
                        <span class="h-4 w-px bg-[#004D40]/[0.15]"></span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#B07A12]">ac_unit</span>
                            এসি · সাউন্ড · জেনারেটর
                        </span>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-[560px] lg:max-w-none">
                    <div class="absolute -inset-3 rounded-[2.8rem] border border-[#F5A623]/30"></div>
                    <div class="absolute -inset-8 rounded-[3.2rem] bg-[#004D40]/5 blur-2xl"></div>

                    <div class="venue-shadow relative overflow-hidden rounded-[2.4rem] border-[6px] border-white bg-[#003028]">
                        <img
                            src="{{ asset('images/landing/hero.webp') }}"
                            alt="মমতা কমিউনিটি সেন্টারের সজ্জিত মূল হল"
                            class="h-[360px] w-full object-cover sm:h-[470px] lg:h-[570px]"
                            fetchpriority="high"
                            decoding="async"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#003028] via-[#003028]/10 to-transparent"></div>

                        <div class="absolute inset-x-5 bottom-5 flex items-end justify-between gap-4 text-white sm:inset-x-7 sm:bottom-7">
                            <div>
                                <p class="eyebrow text-[9px] text-[#F5A623]">{{ $brandNameEn }}</p>
                                <p class="mt-1.5 text-lg font-extrabold leading-tight sm:text-xl">প্রতিটি আয়োজনে<br>আন্তরিকতার ছোঁয়া</p>
                            </div>
                            <div class="hidden shrink-0 rounded-2xl border border-white/20 bg-white/10 px-3.5 py-2.5 text-right backdrop-blur-md sm:block">
                                <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/60">শুরু</p>
                                <p class="text-base font-extrabold text-[#F5A623]">৳{{ $priceBn }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="animate-floaty absolute -left-3 top-8 hidden items-center gap-3 rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-xl shadow-[#003028]/10 backdrop-blur xl:flex lg:-left-7">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#004D40]/[0.08] text-[#004D40]">
                            <span class="material-symbols-outlined text-[21px]">groups</span>
                        </span>
                        <div>
                            <p class="text-lg font-extrabold leading-none text-[#004D40]">{{ $capacityBn }}+</p>
                            <p class="mt-1 text-[10px] font-bold text-[#5A7175]">অতিথির ধারণক্ষমতা</p>
                        </div>
                    </div>

                    <div class="animate-floaty-slow absolute -right-3 bottom-24 hidden rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-xl shadow-[#003028]/10 backdrop-blur xl:block lg:-right-6">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[19px] text-[#B07A12]">calendar_month</span>
                            <p class="text-[11px] font-extrabold text-[#004D40]">২টি স্লট প্রতিদিন</p>
                        </div>
                        <p class="mt-1 text-[10px] font-semibold text-[#5A7175]">দিন ও রাত—আপনার পছন্দে</p>
                    </div>

                    <div class="mt-5 flex items-center justify-between rounded-2xl border border-[#004D40]/10 bg-white/70 px-4 py-3 backdrop-blur sm:hidden">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-[#B07A12]">groups</span>
                            <span class="text-[11px] font-extrabold text-[#004D40]">{{ $capacityBn }}+ অতিথি</span>
                        </div>
                        <span class="h-4 w-px bg-[#004D40]/[0.15]"></span>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-[#B07A12]">calendar_month</span>
                            <span class="text-[11px] font-extrabold text-[#004D40]">২টি স্লট</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats strip -->
        <section class="border-y border-[#004D40]/10 bg-white/80">
            <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-[#004D40]/10 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
                <div class="px-3 py-6 text-center sm:py-7">
                    <p class="font-display text-2xl text-[#004D40] sm:text-3xl">{{ $capacityBn }}+</p>
                    <p class="mt-1 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#5A7175] sm:text-[11px]">অতিথি ধারণক্ষমতা</p>
                </div>
                <div class="px-3 py-6 text-center sm:py-7">
                    <p class="font-display text-2xl text-[#004D40] sm:text-3xl">২টি</p>
                    <p class="mt-1 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#5A7175] sm:text-[11px]">স্লট প্রতিদিন</p>
                </div>
                <div class="border-t border-[#004D40]/10 px-3 py-6 text-center lg:border-t-0 sm:py-7">
                    <p class="font-display text-2xl text-[#004D40] sm:text-3xl">১০+</p>
                    <p class="mt-1 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#5A7175] sm:text-[11px]">ধরনের আয়োজন</p>
                </div>
                <div class="border-t border-[#004D40]/10 px-3 py-6 text-center lg:border-t-0 sm:py-7">
                    <p class="font-display text-2xl text-[#004D40] sm:text-3xl">১টি</p>
                    <p class="mt-1 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#5A7175] sm:text-[11px]">নিজস্ব ভেন্যু</p>
                </div>
            </div>
        </section>

        <!-- Venue -->
        <section id="venue" class="relative overflow-hidden py-20 lg:py-28">
            <div class="pointer-events-none absolute left-0 top-24 h-64 w-64 rounded-full bg-[#F5A623]/[0.08] blur-3xl"></div>
            <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
                <div class="reveal relative order-2 lg:order-1">
                    <div class="overflow-hidden rounded-[2rem] border-[6px] border-white venue-shadow">
                        <img src="{{ asset('images/landing/venue-table.webp') }}" alt="মমতা কমিউনিটি সেন্টারে টেবিল ও ফুলের সাজসজ্জা" class="h-[330px] w-full object-cover sm:h-[430px]" loading="lazy" decoding="async">
                    </div>
                    <div class="absolute -bottom-7 -right-3 w-44 rounded-2xl bg-[#004D40] p-5 text-white shadow-xl shadow-[#004D40]/25 sm:-right-6 sm:w-52">
                        <p class="font-display text-3xl leading-none text-[#F5A623]">১</p>
                        <p class="mt-2 text-xs font-bold leading-5 text-white/[0.85]">ভেন্যু,<br>অগণিত স্মৃতি</p>
                    </div>
                    <div class="absolute -left-4 top-6 hidden rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-lg backdrop-blur sm:block">
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.15em] text-[#B07A12]">In the heart of</p>
                        <p class="mt-1 text-xs font-extrabold text-[#004D40]">{{ $address }}</p>
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <div class="reveal">
                        <p class="eyebrow text-[10px] text-[#B07A12]">The Venue</p>
                        <h2 class="font-display mt-3 text-3xl leading-tight text-[#122B27] sm:text-4xl lg:text-[2.85rem]">
                            প্রশস্ত হল,<br><span class="text-[#004D40]">উষ্ণ আতিথেয়তা</span>
                        </h2>
                        <div class="gold-rule mt-5"></div>
                        <p class="mt-6 text-[15px] leading-8 text-[#5A7175]">
                            মমতা কমিউনিটি সেন্টার একটাই—তাই আমরা প্রতিটি আয়োজনকে ভাবি নিজেদের আয়োজন হিসেবে। প্রশস্ত মূল হল, নমনীয় টেবিল বিন্যাস আর পরিচ্ছন্ন পরিবেশে আপনার অতিথিরা পাবেন আরামদায়ক জায়গা; আপনি পাবেন নিশ্চিন্ততা।
                        </p>
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            ['ac_unit', 'শীতাতপ নিয়ন্ত্রিত', 'গরমের দিনেও আরামদায়ক, পরিচ্ছন্ন পরিবেশ।'],
                            ['table_restaurant', 'নমনীয় সিটিং', 'অতিথি সংখ্যা অনুযায়ী টেবিল ও চেয়ারের বিন্যাস।'],
                            ['wb_sunny', 'দিন ও রাতের স্লট', 'আপনার অনুষ্ঠানের সময় অনুযায়ী দুটি আলাদা স্লট।'],
                            ['door_front', 'প্রশস্ত প্রবেশপথ', 'অতিথিদের জন্য সহজ ও সুবিধাজনক প্রবেশ।'],
                        ] as [$icon, $title, $desc])
                            <div class="reveal card-lift flex gap-3 rounded-2xl border border-[#004D40]/10 bg-white/75 p-4">
                                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#004D40]/[0.08] text-[#004D40]">
                                    <span class="material-symbols-outlined text-[19px]">{{ $icon }}</span>
                                </span>
                                <div>
                                    <p class="text-[13px] font-extrabold text-[#122B27]">{{ $title }}</p>
                                    <p class="mt-1 text-[11px] leading-5 text-[#5A7175]">{{ $desc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ $bookingUrl }}" class="reveal mt-8 inline-flex items-center gap-2 text-sm font-extrabold text-[#004D40] transition-colors hover:text-[#B07A12]">
                        তারিখ ও স্লট দেখে নিন
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Events -->
        <section id="events" class="border-y border-[#004D40]/[0.08] bg-[#F5F0E6]/70 py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="reveal mx-auto max-w-2xl text-center">
                    <p class="eyebrow text-[10px] text-[#B07A12]">Celebrations &amp; Gatherings</p>
                    <h2 class="font-display mt-3 text-3xl leading-tight text-[#122B27] sm:text-4xl lg:text-[2.85rem]">যে আয়োজনে আমরা পাশে আছি</h2>
                    <p class="mt-5 text-[14px] leading-7 text-[#5A7175] sm:text-[15px]">
                        ছোট পারিবারিক আয়োজন থেকে বড় কর্পোরেট অনুষ্ঠান—আপনার ধরন অনুযায়ী ভেন্যু সাজিয়ে নিতে পারবেন।
                    </p>
                </div>

                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['favorite', 'বিয়ে ও ওয়ালিমা', 'প্রথম দেখার গল্প থেকে নতুন জীবনের শুরু—পারিবারিক আয়োজনে প্রশস্ত হল ও উষ্ণ পরিবেশ।', 'বিয়ে · ওয়ালিমা · বৌভাত'],
                        ['spa', 'গায়ে হলুদ ও মেহেদি', 'রঙ, ফুল আর হাসির আয়োজনে আলাদা সাজে ভেন্যু। ছোট-বড় দুই ধরনের গায়ে হলুদের জন্যই উপযুক্ত।', 'গায়ে হলুদ · মেহেদি · হলুদ'],
                        ['diamond', 'আকদ ও এনগেজমেন্ট', 'নিকটজনের উপস্থিতিতে পবিত্র আকদ বা এনগেজমেন্ট—সাদামাটা থেকে রাজকীয়, আপনার পছন্দে।', 'আকদ · এনগেজমেন্ট · বাগদান'],
                        ['cake', 'জন্মদিন ও আকিকা', 'শিশুর প্রথম জন্মদিন, আকিকা কিংবা পারিবারিক মিলন—আনন্দের দিনটি হোক সহজ ও সুন্দর।', 'জন্মদিন · আকিকা · পারিবারিক আয়োজন'],
                        ['business_center', 'কর্পোরেট আয়োজন', 'এজিএম, প্রোডাক্ট লঞ্চ, কর্পোরেট ডিনার—পেশাদার পরিবেশে গুছিয়ে করা আয়োজন।', 'এজিএম · লঞ্চ · কর্পোরেট ডিনার'],
                        ['mic_external_on', 'সেমিনার ও সাংস্কৃতিক', 'কনফারেন্স, সেমিনার, সাংস্কৃতিক অনুষ্ঠান বা ইফতার মাহফিল—প্রয়োজনীয় সেটআপসহ।', 'সেমিনার · কনসার্ট · ইফতার'],
                    ] as [$icon, $title, $desc, $tags])
                        <article class="reveal card-lift group rounded-[1.6rem] border border-[#004D40]/10 bg-white p-6">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#004D40]/[0.08] text-[#004D40] transition-colors group-hover:bg-[#004D40] group-hover:text-white">
                                <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
                            </div>
                            <h3 class="mt-5 text-lg font-extrabold text-[#122B27]">{{ $title }}</h3>
                            <p class="mt-2.5 text-[12.5px] leading-6 text-[#5A7175]">{{ $desc }}</p>
                            <p class="mt-5 border-t border-[#004D40]/[0.08] pt-4 text-[10px] font-extrabold uppercase tracking-[0.09em] text-[#B07A12]">{{ $tags }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Amenities -->
        <section id="amenities" class="relative overflow-hidden bg-[#003028] py-20 text-white lg:py-28">
            <div class="pointer-events-none absolute -right-24 top-0 h-96 w-96 rounded-full bg-[#004D40] blur-3xl"></div>
            <div class="pointer-events-none absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-[#F5A623]/10 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start lg:gap-16">
                    <div class="reveal lg:sticky lg:top-28">
                        <p class="eyebrow text-[10px] text-[#F5A623]">What's Included</p>
                        <h2 class="font-display mt-3 text-3xl leading-tight sm:text-4xl lg:text-[2.85rem]">ভেন্যুর সুবিধা,<br><span class="text-[#F5A623]">আপনার আয়োজনে</span></h2>
                        <div class="gold-rule mt-5"></div>
                        <p class="mt-6 max-w-md text-[14px] leading-8 text-white/[0.65]">
                            মূল ভাড়ার সঙ্গে প্রয়োজনীয় সেটআপ ও সহায়তা—যাতে আপনার মন থাকে শুধু অতিথি আপ্যায়নে। অতিরিক্ত সুবিধা আপনার প্যাকেজ অনুযায়ী যোগ করা যায়।
                        </p>
                        <a href="tel:{{ $phoneHref }}" class="mt-8 inline-flex items-center gap-2 rounded-full border border-white/[0.15] bg-white/5 px-5 py-3 text-xs font-extrabold text-white transition-colors hover:bg-white/10">
                            <span class="material-symbols-outlined text-[18px] text-[#F5A623]">call</span>
                            {{ $phone }}
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            ['ac_unit', 'শীতাতপ নিয়ন্ত্রণ', 'প্রশস্ত হলজুড়ে আরামদায়ক তাপমাত্রা।'],
                            ['graphic_eq', 'সাউন্ড সিস্টেম', 'আয়োজনের জন্য প্রয়োজনীয় সাউন্ড ও লাইটিং সুবিধা।'],
                            ['bolt', 'জেনারেটর ব্যাকআপ', 'বিদ্যুৎ বিভ্রাটেও অনুষ্ঠান চলবে নিরবচ্ছিন্নভাবে।'],
                            ['auto_awesome', 'ডেকোরেশন সাপোর্ট', 'মঞ্চ, প্রবেশপথ ও টেবিল সাজানোর সহায়তা।'],
                            ['badge', 'প্রশিক্ষিত কর্মী', 'অতিথি আপ্যায়নে অভিজ্ঞ ও সহযোগী টিম।'],
                            ['table_restaurant', 'টেবিল ও চেয়ার', 'অতিথি সংখ্যা অনুযায়ী নমনীয় সেটআপ।'],
                        ] as [$icon, $title, $desc])
                            <div class="reveal group rounded-[1.4rem] border border-white/10 bg-white/[0.045] p-5 backdrop-blur-sm transition-colors hover:border-[#F5A623]/[0.35] hover:bg-white/[0.07]">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#F5A623]/[0.12] text-[#F5A623]">
                                    <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                                </span>
                                <h3 class="mt-4 text-[15px] font-extrabold text-white">{{ $title }}</h3>
                                <p class="mt-1.5 text-[12px] leading-6 text-white/[0.55]">{{ $desc }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery -->
        <section id="gallery" class="py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <div class="reveal max-w-xl">
                        <p class="eyebrow text-[10px] text-[#B07A12]">A Glimpse Inside</p>
                        <h2 class="font-display mt-3 text-3xl leading-tight text-[#122B27] sm:text-4xl lg:text-[2.85rem]">ভেন্যু এক নজরে</h2>
                        <p class="mt-4 text-[14px] leading-7 text-[#5A7175] sm:text-[15px]">হল, টেবিল সাজসজ্জা, ডাইনিং আর কর্পোরেট সেটআপ—কয়েকটি ছবিতে আমাদের পরিবেশ।</p>
                    </div>
                    <p class="hidden items-center gap-2 text-[11px] font-bold text-[#5A7175] sm:flex">
                        <span class="material-symbols-outlined text-[17px] text-[#B07A12]">zoom_in</span>
                        ছবিতে ক্লিক করে বড় দেখুন
                    </p>
                </div>

                <div class="mt-10 grid auto-rows-[150px] grid-cols-2 gap-3 sm:auto-rows-[190px] sm:gap-4 lg:auto-rows-[210px] lg:grid-cols-4">
                    @foreach ($gallery as $image)
                        <button
                            type="button"
                            class="gallery-item reveal group relative overflow-hidden rounded-[1.35rem] bg-[#003028] text-left focus:outline-none focus-visible:ring-4 focus-visible:ring-[#F5A623]/60 {{ $image['class'] }}"
                            @click="lightbox = { src: $el.dataset.src, alt: $el.dataset.alt }"
                            data-src="{{ $image['src'] }}"
                            data-alt="{{ $image['alt'] }}"
                            aria-label="{{ $image['caption'] }} ছবিটি বড় করে দেখুন"
                        >
                            <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            <span class="absolute inset-0 bg-gradient-to-t from-[#003028]/80 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-100"></span>
                            <span class="absolute bottom-3 left-4 right-3 flex items-center justify-between gap-2">
                                <span class="text-[11px] font-extrabold text-white sm:text-xs">{{ $image['caption'] }}</span>
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/[0.15] text-white opacity-0 backdrop-blur transition-opacity group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-[15px]">open_in_full</span>
                                </span>
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Booking -->
        <section id="booking" class="relative overflow-hidden border-y border-[#004D40]/[0.08] bg-[#F5F0E6]/70 py-20 lg:py-28">
            <div class="pointer-events-none absolute -right-20 top-10 h-72 w-72 rounded-full bg-[#F5A623]/10 blur-3xl"></div>
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:gap-20 lg:px-8">
                <div class="reveal">
                    <p class="eyebrow text-[10px] text-[#B07A12]">Booking</p>
                    <h2 class="font-display mt-3 text-3xl leading-tight text-[#122B27] sm:text-4xl lg:text-[2.85rem]">তারিখ ঠিক করে নিন,<br><span class="text-[#004D40]">বাকিটা আমাদের দায়িত্ব</span></h2>
                    <p class="mt-5 max-w-lg text-[14px] leading-8 text-[#5A7175] sm:text-[15px]">
                        পছন্দের তারিখ ও স্লট আগে যাচাই করে নিন। অগ্রিম পরিশোধের মাধ্যমে বুকিং নিশ্চিত করুন—অনুষ্ঠানের বাকি পরিকল্পনা আমরা একসাথে সাজিয়ে নেব।
                    </p>

                    <div class="mt-9 space-y-5">
                        @foreach ([
                            ['১', 'তারিখ ও স্লট যাচাই', 'অনলাইনে অথবা ফোনে জানুন আপনার পছন্দের দিনটি ফাঁকা আছে কি না।'],
                            ['২', 'অগ্রিম দিয়ে বুকিং নিশ্চিত', 'আয়োজনের ধরন অনুযায়ী অগ্রিম পরিশোধ করে তারিখটি আপনার নামে রাখুন।'],
                            ['৩', 'আয়োজন উপভোগ করুন', 'টেবিল, সাউন্ড, ডেকোরেশন—প্রয়োজনীয় সবকিছু আগেই গুছিয়ে নিন।'],
                        ] as [$step, $title, $desc])
                            <div class="reveal flex gap-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[#B07A12]/25 bg-white text-xs font-extrabold text-[#B07A12]">{{ $step }}</span>
                                <div>
                                    <p class="text-sm font-extrabold text-[#122B27]">{{ $title }}</p>
                                    <p class="mt-1 text-[12px] leading-6 text-[#5A7175]">{{ $desc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="reveal relative">
                    <div class="absolute -inset-3 rounded-[2.4rem] border border-[#F5A623]/25"></div>
                    <div class="relative rounded-[2rem] bg-white p-6 shadow-[0_30px_70px_-34px_rgba(0,48,40,.45)] sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="eyebrow text-[9px] text-[#B07A12]">Venue Hire</p>
                                <p class="font-display mt-2 text-4xl text-[#004D40] sm:text-5xl">৳{{ $priceBn }}<span class="ml-1 text-sm font-bold text-[#5A7175]">থেকে</span></p>
                                <p class="mt-2 text-[11px] font-semibold text-[#5A7175]">প্রতি স্লটের ভিত্তিমূল্য</p>
                            </div>
                            <span class="rounded-full bg-[#004D40]/[0.08] px-3 py-1.5 text-[10px] font-extrabold text-[#004D40]">২টি স্লট</span>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-[#004D40]/10 bg-[#FBF8F1] p-3.5">
                                <span class="material-symbols-outlined text-[19px] text-[#B07A12]">light_mode</span>
                                <p class="mt-2 text-[11px] font-extrabold text-[#004D40]">দিনের স্লট</p>
                                <p class="mt-0.5 text-[10px] text-[#5A7175]">সকাল থেকে বিকেল</p>
                            </div>
                            <div class="rounded-xl border border-[#004D40]/10 bg-[#FBF8F1] p-3.5">
                                <span class="material-symbols-outlined text-[19px] text-[#B07A12]">dark_mode</span>
                                <p class="mt-2 text-[11px] font-extrabold text-[#004D40]">রাতের স্লট</p>
                                <p class="mt-0.5 text-[10px] text-[#5A7175]">সন্ধ্যা থেকে রাত</p>
                            </div>
                        </div>

                        <ul class="mt-6 space-y-2.5 text-[12px] font-semibold text-[#44605B]">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[17px] text-[#004D40]">check_circle</span> {{ $capacityBn }}+ অতিথির ধারণক্ষমতা</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[17px] text-[#004D40]">check_circle</span> এসি, সাউন্ড ও জেনারেটর সুবিধা</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[17px] text-[#004D40]">check_circle</span> প্রয়োজন অনুযায়ী ডেকোরেশন ও সেটআপ</li>
                        </ul>

                        <a href="{{ $bookingUrl }}" class="mt-7 flex w-full items-center justify-center gap-2 rounded-xl bg-[#004D40] px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-[#004D40]/20 transition-colors hover:bg-[#006D5B]">
                            বুকিং শুরু করুন
                            <span class="material-symbols-outlined text-[18px] text-[#F5A623]">arrow_forward</span>
                        </a>
                        <a href="tel:{{ $phoneHref }}" class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-[#004D40]/[0.15] px-5 py-3 text-xs font-extrabold text-[#004D40] transition-colors hover:bg-[#FBF8F1]">
                            <span class="material-symbols-outlined text-[17px]">call</span>
                            {{ $phone }}
                        </a>
                        <p class="mt-4 text-center text-[10px] leading-5 text-[#8A9A97]">চূড়ান্ত মূল্য আয়োজনের ধরন, অতিথি সংখ্যা ও অতিরিক্ত সুবিধার উপর নির্ভর করে।</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="py-20 lg:py-28">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20 lg:px-8">
                <div class="reveal">
                    <p class="eyebrow text-[10px] text-[#B07A12]">Good To Know</p>
                    <h2 class="font-display mt-3 text-3xl leading-tight text-[#122B27] sm:text-4xl lg:text-[2.85rem]">সাধারণ<br><span class="text-[#004D40]">প্রশ্নোত্তর</span></h2>
                    <p class="mt-5 max-w-sm text-[14px] leading-7 text-[#5A7175]">বুকিং, ধারণক্ষমতা বা সুবিধা নিয়ে প্রশ্ন থাকলে ফোনে বা সরাসরি ভেন্যুতে এসে জেনে নিতে পারেন।</p>
                    <a href="tel:{{ $phoneHref }}" class="mt-7 inline-flex items-center gap-2 rounded-full bg-[#004D40] px-5 py-3 text-xs font-extrabold text-white transition-colors hover:bg-[#006D5B]">
                        <span class="material-symbols-outlined text-[17px] text-[#F5A623]">call</span>
                        {{ $phone }}
                    </a>
                </div>

                <div class="reveal divide-y divide-[#004D40]/10 border-y border-[#004D40]/10">
                    @foreach ([
                        ['বুকিং কীভাবে করব?', 'ওয়েবসাইটের “বুকিং করুন” বাটনে ক্লিক করে লগইন করুন, অথবা সরাসরি ফোন করুন। পছন্দের তারিখ ও স্লট ফাঁকা থাকলে অগ্রিম পরিশোধের মাধ্যমে বুকিং নিশ্চিত করা যায়।'],
                        ['কত আগে বুকিং করা উচিত?', 'বিশেষ করে বিয়ের মৌসুমে জনপ্রিয় তারিখগুলো আগে ভরে যায়। তাই ২–৩ মাস আগে যোগাযোগ করা ভালো। তবে ফাঁকা স্লট থাকলে শেষ মুহূর্তেও বুকিং সম্ভব।'],
                        ['ভেন্যুর ধারণক্ষমতা কত?', 'আমাদের মূল হলে আরামদায়কভাবে প্রায় ' . $capacityBn . ' জন অতিথি আপ্যায়ন করা যায়। আপনার আয়োজনের ধরন অনুযায়ী টেবিল ও সিটিং বিন্যাস সাজানো হয়।'],
                        ['অগ্রিম কত দিতে হয়?', 'অগ্রিমের পরিমাণ আয়োজনের ধরন ও প্যাকেজের উপর নির্ভর করে। বুকিংয়ের সময় অগ্রিম পরিশোধ করতে হয় এবং বাকি অর্থ অনুষ্ঠানের আগে পরিশোধের সুযোগ থাকে।'],
                        ['ডেকোরেশন ও ক্যাটারিং ব্যবস্থা আছে?', 'ডেকোরেশন সাপোর্ট রয়েছে। ক্যাটারিংয়ের জন্য আপনার পছন্দের ক্যাটারারের সঙ্গে সমন্বয় করে নেওয়া যায়—বিস্তারিত ফোনে আলোচনা করে জেনে নিন।'],
                        ['ভেন্যু দেখতে কখন আসব?', 'ভেন্যু পরিদর্শনের জন্য আগে ফোন করে সময় নিয়ে আসুন, যাতে আমরা আপনাকে মনোযোগ দিয়ে হল, ডাইনিং ও অন্যান্য সুবিধা দেখাতে পারি।'],
                    ] as $index => [$question, $answer])
                        <div>
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-5 py-5 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5A623]"
                                @click="activeFaq = activeFaq === {{ $index + 1 }} ? null : {{ $index + 1 }}"
                                :aria-expanded="activeFaq === {{ $index + 1 }} ? 'true' : 'false'"
                            >
                                <span class="text-[13.5px] font-extrabold text-[#122B27] sm:text-sm">{{ $question }}</span>
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-[#004D40]/[0.15] text-[#004D40] transition-transform duration-300" :class="activeFaq === {{ $index + 1 }} ? 'rotate-180 bg-[#004D40] text-white border-[#004D40]' : ''">
                                    <span class="material-symbols-outlined text-[17px]">expand_more</span>
                                </span>
                            </button>
                            <div
                                x-cloak
                                x-show="activeFaq === {{ $index + 1 }}"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="pb-5 pr-10 text-[12.5px] leading-7 text-[#5A7175]"
                            >
                                {{ $answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Contact / Location -->
        <section id="contact" class="border-t border-[#004D40]/[0.08] bg-[#F5F0E6]/60 py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="reveal mx-auto max-w-2xl text-center">
                    <p class="eyebrow text-[10px] text-[#B07A12]">Visit Us</p>
                    <h2 class="font-display mt-3 text-3xl leading-tight text-[#122B27] sm:text-4xl lg:text-[2.85rem]">আসুন, ভেন্যুটি নিজে দেখুন</h2>
                    <p class="mt-4 text-[14px] leading-7 text-[#5A7175] sm:text-[15px]">পছন্দের তারিখ নিয়ে আলোচনা করতে বা হল ঘুরে দেখতে আমাদের সাথে যোগাযোগ করুন।</p>
                </div>

                <div class="mt-12 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
                    <div class="reveal space-y-3">
                        <div class="flex gap-4 rounded-2xl border border-[#004D40]/10 bg-white p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#004D40]/[0.08] text-[#004D40]">
                                <span class="material-symbols-outlined text-[21px]">location_on</span>
                            </span>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#B07A12]">ঠিকানা</p>
                                <p class="mt-1 text-[13px] font-extrabold leading-6 text-[#122B27]">{{ $address }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4 rounded-2xl border border-[#004D40]/10 bg-white p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#004D40]/[0.08] text-[#004D40]">
                                <span class="material-symbols-outlined text-[21px]">call</span>
                            </span>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#B07A12]">ফোন</p>
                                <a href="tel:{{ $phoneHref }}" class="mt-1 block text-[13px] font-extrabold text-[#122B27] hover:text-[#004D40]">{{ $phone }}</a>
                                <p class="mt-1 text-[10px] text-[#5A7175]">বুকিং ও ভেন্যু পরিদর্শনের জন্য</p>
                            </div>
                        </div>

                        @if ($venue->email)
                            <div class="flex gap-4 rounded-2xl border border-[#004D40]/10 bg-white p-5">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#004D40]/[0.08] text-[#004D40]">
                                    <span class="material-symbols-outlined text-[21px]">mail</span>
                                </span>
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#B07A12]">ইমেইল</p>
                                    <a href="mailto:{{ $venue->email }}" class="mt-1 block text-[13px] font-extrabold text-[#122B27] hover:text-[#004D40]">{{ $venue->email }}</a>
                                </div>
                            </div>
                        @endif

                        <div class="rounded-2xl bg-[#004D40] p-5 text-white">
                            <p class="text-[11px] font-extrabold text-[#F5A623]">ভেন্যু পরিদর্শন</p>
                            <p class="mt-2 text-[12px] leading-6 text-white/70">আগে ফোন করে সময় নিয়ে আসুন—আমরা হল, ডাইনিং ও অন্যান্য সুবিধা দেখিয়ে দেব।</p>
                            <a href="tel:{{ $phoneHref }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-[11px] font-extrabold text-[#004D40]">
                                <span class="material-symbols-outlined text-[16px]">call</span>
                                কল করুন
                            </a>
                        </div>
                    </div>

                    <div class="reveal relative overflow-hidden rounded-[2rem] border-[5px] border-white bg-[#003028] venue-shadow">
                        <img
                            src="{{ asset('images/landing/gallery-hall.webp') }}"
                            alt="মমতা কমিউনিটি সেন্টারের প্রশস্ত হল"
                            class="h-[330px] w-full object-cover sm:h-[420px] lg:h-full lg:min-h-[460px]"
                            loading="lazy"
                            decoding="async"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#003028] via-[#003028]/35 to-transparent"></div>
                        <div class="absolute inset-x-5 bottom-5 text-white sm:inset-x-7 sm:bottom-7">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-[#F5A623] backdrop-blur">
                                    <span class="material-symbols-outlined text-[23px]">location_on</span>
                                </span>
                                <div>
                                    <p class="eyebrow text-[9px] text-[#F5A623]">Find Us</p>
                                    <p class="mt-1 text-base font-extrabold leading-tight sm:text-lg">{{ $brandName }}</p>
                                </div>
                            </div>
                            <p class="mt-3 text-[12px] leading-6 text-white/70">{{ $address }}</p>
                            <a
                                href="https://www.google.com/maps/search/?api=1&query={{ urlencode($brandName . ', ' . $address) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2.5 text-[11px] font-extrabold text-[#004D40] transition-colors hover:bg-[#F5A623] hover:text-[#003028]"
                            >
                                Google Maps-এ দেখুন
                                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="relative overflow-hidden bg-[#003028] py-16 text-white sm:py-20">
            <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at 20% 20%, rgba(245,166,35,.16), transparent 32%), radial-gradient(circle at 85% 80%, rgba(0,109,91,.5), transparent 42%);"></div>
            <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-[#F5A623]/20"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-20 h-72 w-72 rounded-full border border-white/10"></div>
            <div class="relative mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:px-8">
                <div>
                    <p class="eyebrow text-[10px] text-[#F5A623]">Your Day, Our Care</p>
                    <h2 class="font-display mt-3 max-w-2xl text-3xl leading-tight sm:text-4xl">আপনার আয়োজন শুরু হোক<br><span class="text-[#F5A623]">আমাদের সাথে</span></h2>
                </div>
                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <a href="{{ $bookingUrl }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#F5A623] px-7 py-3.5 text-sm font-extrabold text-[#003028] shadow-lg shadow-black/10 transition-transform hover:-translate-y-0.5">
                        বুকিং শুরু করুন
                        <span class="material-symbols-outlined text-[18px]">arrow_outward</span>
                    </a>
                    <a href="tel:{{ $phoneHref }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 bg-white/5 px-7 py-3.5 text-sm font-extrabold text-white backdrop-blur transition-colors hover:bg-white/10">
                        <span class="material-symbols-outlined text-[18px] text-[#F5A623]">call</span>
                        {{ $phone }}
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[#FBF8F1] pb-8 pt-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 border-b border-[#004D40]/10 pb-10 sm:grid-cols-2 lg:grid-cols-[1.4fr_0.7fr_0.7fr_1fr]">
                <div>
                    <div class="flex items-center gap-3">
                        <img src="{{ $logo }}" alt="{{ $brandName }} লোগো" class="h-12 w-12 rounded-2xl bg-white object-contain p-1 ring-1 ring-[#004D40]/10">
                        <div>
                            <p class="text-lg font-extrabold leading-none text-[#004D40]">মমতা</p>
                            <p class="mt-1 text-[9px] font-extrabold tracking-[0.18em] text-[#B07A12]">কমিউনিটি সেন্টার</p>
                        </div>
                    </div>
                    <p class="mt-5 max-w-xs text-[12px] leading-6 text-[#5A7175]">বিয়ে, গায়ে হলুদ, আকদ থেকে কর্পোরেট আয়োজন—প্রতিটি বিশেষ দিনে পাশে থাকার জন্য একটি উষ্ণ ভেন্যু।</p>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#B07A12]">ভেন্যু</p>
                    <ul class="mt-4 space-y-2.5 text-[12px] font-bold text-[#44605B]">
                        <li><a href="#venue" class="hover:text-[#004D40]">ভেন্যু সম্পর্কে</a></li>
                        <li><a href="#events" class="hover:text-[#004D40]">আয়োজনের ধরন</a></li>
                        <li><a href="#amenities" class="hover:text-[#004D40]">সুবিধা</a></li>
                        <li><a href="#gallery" class="hover:text-[#004D40]">গ্যালারি</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#B07A12]">সহায়তা</p>
                    <ul class="mt-4 space-y-2.5 text-[12px] font-bold text-[#44605B]">
                        <li><a href="#booking" class="hover:text-[#004D40]">বুকিং</a></li>
                        <li><a href="#faq" class="hover:text-[#004D40]">প্রশ্নোত্তর</a></li>
                        <li><a href="#contact" class="hover:text-[#004D40]">যোগাযোগ</a></li>
                        <li><a href="{{ $bookingUrl }}" class="hover:text-[#004D40]">বুকিং শুরু করুন</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#B07A12]">যোগাযোগ</p>
                    <ul class="mt-4 space-y-3 text-[12px] font-bold text-[#44605B]">
                        <li class="flex gap-2.5">
                            <span class="material-symbols-outlined mt-0.5 text-[17px] text-[#004D40]">location_on</span>
                            <span class="leading-5">{{ $address }}</span>
                        </li>
                        <li class="flex gap-2.5">
                            <span class="material-symbols-outlined text-[17px] text-[#004D40]">call</span>
                            <a href="tel:{{ $phoneHref }}" class="hover:text-[#004D40]">{{ $phone }}</a>
                        </li>
                        @if ($venue->email)
                            <li class="flex gap-2.5">
                                <span class="material-symbols-outlined text-[17px] text-[#004D40]">mail</span>
                                <a href="mailto:{{ $venue->email }}" class="break-all hover:text-[#004D40]">{{ $venue->email }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="flex flex-col items-center justify-between gap-3 pt-6 sm:flex-row">
                <p class="text-[10px] font-semibold text-[#7A8C88]">© {{ $toBn(date('Y')) }} {{ $brandName }}। সর্বস্বত্ব সংরক্ষিত।</p>
                <p class="text-[10px] font-semibold text-[#7A8C88]">ভালোবাসা ও যত্নে সাজানো একটি ভেন্যু।</p>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div
        x-cloak
        x-show="lightbox"
        x-transition.opacity
        class="fixed inset-0 z-[100] flex items-center justify-center bg-[#003028]/95 p-4 backdrop-blur-sm"
        @click.self="lightbox = null"
        @keydown.escape.window="lightbox = null"
        role="dialog"
        aria-modal="true"
        aria-label="ছবি বড় করে দেখুন"
    >
        <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20" @click="lightbox = null" aria-label="বন্ধ করুন">
            <span class="material-symbols-outlined">close</span>
        </button>
        <figure class="w-full max-w-5xl">
            <img :src="lightbox ? lightbox.src : ''" :alt="lightbox ? lightbox.alt : ''" class="mx-auto max-h-[82vh] w-auto max-w-full rounded-2xl object-contain shadow-2xl">
            <figcaption class="mt-3 text-center text-xs font-bold text-white/75" x-text="lightbox ? lightbox.alt : ''"></figcaption>
        </figure>
    </div>

    <noscript>
        <style>.reveal { opacity: 1 !important; transform: none !important; }</style>
    </noscript>

    <script>
        (function () {
            const items = document.querySelectorAll('.reveal');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (!('IntersectionObserver' in window) || reduceMotion) {
                items.forEach((el) => el.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            items.forEach((el, index) => {
                el.style.transitionDelay = `${(index % 4) * 70}ms`;
                observer.observe(el);
            });
        })();
    </script>
</body>
</html>
