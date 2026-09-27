<!DOCTYPE html>
<!--
 * CoreUI - Free Bootstrap Admin Template (integrated into Laravel)
 * @version v5.6.0 | https://coreui.io
 * Sidebar / header adapted for Momota Community Center (Bangla nav preserved).
-->
<html lang="bn" data-coreui-theme="light">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name', 'Momota Community Center') }} - Admin">
    <title>{{ $title ?? 'ড্যাশবোর্ড' }} | {{ config('app.name', 'Momota') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vendors styles -->
    <link rel="stylesheet" href="{{ asset('coreui/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('coreui/css/vendors/simplebar.css') }}">
    <!-- Main CoreUI styles (copied from coreui/dist) -->
    <link href="{{ asset('coreui/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('coreui/vendors/@coreui/chartjs/css/coreui-chartjs.css') }}" rel="stylesheet">
    <!-- Tabler -> CoreUI compatibility shim (keeps existing inner pages working) -->
    <link href="{{ asset('coreui/custom-compat.css') }}" rel="stylesheet">

    <script src="{{ asset('coreui/js/config.js') }}"></script>
    <script>
        // Force light mode: clear any stored dark/auto preference from CoreUI template.
        try { localStorage.setItem('coreui-free-bootstrap-admin-template-theme', 'light'); } catch (e) {}
        document.documentElement.setAttribute('data-coreui-theme', 'light');
    </script>

    <style>
        body, .sidebar-nav, .header, .card, .btn, .table, .form-control, .form-select {
            font-family: 'Anek Bangla', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .sidebar-brand-full { color: #fff; }
        .momota-brand {
            display: flex; align-items: center; gap: .6rem;
            text-decoration: none; color: #fff;
            padding: .9rem 1rem;
        }
        .momota-brand .logo-circle {
            width: 42px; height: 42px; border-radius: 50%;
            background: #fff; display: flex; align-items: center; justify-content: center;
            border: 2.2px solid #F5A623; flex-shrink: 0; overflow: hidden;
        }
        .momota-brand .logo-circle img { width: 30px; height: 30px; object-fit: contain; }
        .momota-brand .bn { font-weight: 800; font-size: 1.35rem; line-height: 1; color: #fff; }
        .momota-brand .en { font-weight: 700; font-size: 9px; letter-spacing: .8px; color: #F5A623; }
        [x-cloak] { display: none !important; }
    </style>

    @stack('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
@php
    $currentUser = auth()->user() ?? (auth('admin')->check() ? auth('admin')->user() : null);
    $initials = $currentUser ? mb_substr($currentUser->name, 0, 2) : 'AD';
    $roleName = $currentUser?->roles?->first()?->name ?? ($currentUser ? 'এডমিনিস্ট্রেটর' : 'অতিথি');
    $finActive = request()->routeIs('transactions.*') || request()->routeIs('accounting.*') || request()->routeIs('income-categories.*') || request()->routeIs('expense-categories.*') || request()->routeIs('incomes.*') || request()->routeIs('expenses.*') || request()->routeIs('vendors.*') || request()->routeIs('commissions.*') || request()->routeIs('assets.*');
@endphp

<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <a href="{{ route('dashboard') }}" class="momota-brand sidebar-brand-full">
            <span class="logo-circle"><img src="{{ asset('logo.png') }}" alt="Momota Logo"></span>
            <span class="text-start">
                <span class="bn d-block">মমতা</span>
                <span class="en d-block">COMMUNITY CENTER</span>
            </span>
        </a>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>

    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 48 16 224h64v208h128V320h96v112h128V224h64z"/></svg>
                হোম (ড্যাশবোর্ড)
            </a>
        </li>

        @can('manage-bookings')
        <li class="nav-title">বুকিং ব্যবস্থাপনা</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}" href="{{ route('calendar.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M144 16v48H80a48 48 0 0 0-48 48v320a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V112a48 48 0 0 0-48-48h-64V16h-32v48H176V16zm-64 112h352v32H80zm0 64h352v240H80z"/></svg>
                বুকিং ক্যালেন্ডার
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M112 64h288v32H112zm0 96h288v32H112zm0 96h176v32H112zM480 64H32v384h448zM64 416V96h384v320z"/></svg>
                বুকিংসমূহ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 256a112 112 0 1 0-112-112 112 112 0 0 0 112 112zm-192 192v-32a96 96 0 0 1 96-96h192a96 96 0 0 1 96 96v32z"/></svg>
                গ্রাহক তালিকা (CRM)
            </a>
        </li>
        @endcan

        @can('manage-financials')
        <li class="nav-title">হিসাব নিকাশ</li>
        <li class="nav-group {{ $finActive ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 32C132.3 32 32 132.3 32 256s100.3 224 224 224 224-100.3 224-224S379.7 32 256 32zm-32 320h-32v-64h-64v-32h64v-64h32v64h64v32h-64z"/></svg>
                হিসাব নিকাশ
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('accounting.*') ? 'active' : '' }}" href="{{ route('accounting.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> মূল খাতা (লেজার)</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> সকল লেনদেন</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('incomes.*') ? 'active' : '' }}" href="{{ route('incomes.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> আয় ব্যবস্থাপনা</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> ব্যয় ব্যবস্থাপনা</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('income-categories.*') ? 'active' : '' }}" href="{{ route('income-categories.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> আয় ক্যাটাগরি</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> ব্যয় ক্যাটাগরি</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> ভেন্ডর তালিকা</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('commissions.*') ? 'active' : '' }}" href="{{ route('commissions.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> কমিশন ও ভেন্ডর পাওনা</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> মালামাল ও ইনভেন্টরি</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M32 480h448v32H32zM128 320h64v128h-64zm96-96h64v224h-64zm96-128h64v352h-64zM416 32h64v416h-64z"/></svg>
                রিপোর্ট ও অ্যানালিটিক্স
            </a>
        </li>
        @endcan

        @can('manage-employees')
        <li class="nav-title">প্রশাসন</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M160 144a96 96 0 1 0 96 96 96 96 0 0 0-96-96zm-160 320v-16a128 128 0 0 1 128-128h64a128 128 0 0 1 128 128v16zm384-304a80 80 0 1 0 80 80zm48 240v-16a111 111 0 0 0-43-87 160 160 0 0 1 43 103z"/></svg>
                স্টাফ ম্যানেজমেন্ট
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 48a96 96 0 1 0 96 96 96 96 0 0 0-96-96zm-208 368v-24a144 144 0 0 1 144-144h128a144 144 0 0 1 144 144v24z"/></svg>
                ইউজার ও ভূমিকা
            </a>
        </li>
        @endcan

        @can('manage-settings')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 176a80 80 0 1 0 80 80 80 80 0 0 0-80-80zm224 112a127 127 0 0 1-2 24l44 35-48 83-52-21a127 127 0 0 1-41 24l-8 55h-96l-8-55a127 127 0 0 1-41-24l-52 21-48-83 44-35a127 127 0 0 1 0-48l-44-35 48-83 52 21a127 127 0 0 1 41-24l8-55h96l8 55a127 127 0 0 1 41 24l52-21 48 83z"/></svg>
                সিস্টেম সেটিংস
            </a>
        </li>
        @endcan
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>

<div class="wrapper d-flex flex-column min-vh-100">
    <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
            <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px" aria-label="Toggle sidebar">
                <svg class="icon icon-lg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M80 96h352v32H80zm0 144h352v32H80zm0 144h352v32H80z"/></svg>
            </button>
            <div class="header-nav ms-auto">
                <span class="text-body-secondary small d-none d-md-inline">
                    {{ $businessSetting?->business_name ?? config('app.name') }}
                    @isset($activeHall) — {{ $activeHall->name ?? '' }} @endisset
                </span>
            </div>
            <ul class="header-nav">
                <li class="nav-item py-1"><div class="vr h-100 mx-2 text-body text-opacity-75"></div></li>
                <li class="nav-item dropdown">
                    <a class="nav-link py-0 pe-0 d-flex align-items-center gap-2" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center fw-bold">{{ $initials }}</div>
                        <div class="d-none d-xl-block text-start">
                            <div class="fw-semibold small">{{ $currentUser?->name ?? 'এডমিন' }}</div>
                            <div class="text-body-secondary" style="font-size:.72rem">{{ $roleName }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pt-0">
                        <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Account</div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">লগআউট</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </header>

    <div class="body flex-grow-1">
        <div class="container-fluid px-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @isset($header)
                <div class="mb-3">{{ $header }}</div>
            @endisset

            {{ $slot }}
        </div>
    </div>

    <footer class="footer px-4">
        <div>কপিরাইট &copy; {{ date('Y') }} <a href=".">{{ config('app.name') }}</a>. সর্বস্বত্ব সংরক্ষিত।</div>
        <div class="ms-auto">Powered by <a href="https://coreui.io">CoreUI</a></div>
    </footer>
</div>

<!-- CoreUI + vendors (from local coreui/ folder) -->
<script src="{{ asset('coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script>
    if (window.coreui) {
        window.bootstrap = window.coreui;
    }
    // Auto-shim any Bootstrap data-bs-* attributes so CoreUI handles them
    (function() {
        function shimBootstrapDataAttrs() {
            document.querySelectorAll('[data-bs-toggle]').forEach(function(el) {
                if (!el.hasAttribute('data-coreui-toggle')) {
                    el.setAttribute('data-coreui-toggle', el.getAttribute('data-bs-toggle'));
                }
            });
            document.querySelectorAll('[data-bs-target]').forEach(function(el) {
                if (!el.hasAttribute('data-coreui-target')) {
                    el.setAttribute('data-coreui-target', el.getAttribute('data-bs-target'));
                }
            });
            document.querySelectorAll('[data-bs-dismiss]').forEach(function(el) {
                if (!el.hasAttribute('data-coreui-dismiss')) {
                    el.setAttribute('data-coreui-dismiss', el.getAttribute('data-bs-dismiss'));
                }
            });
            document.querySelectorAll('[data-bs-auto-close]').forEach(function(el) {
                if (!el.hasAttribute('data-coreui-auto-close')) {
                    el.setAttribute('data-coreui-auto-close', el.getAttribute('data-bs-auto-close'));
                }
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', shimBootstrapDataAttrs);
        } else {
            shimBootstrapDataAttrs();
        }
    })();
</script>
<script src="{{ asset('coreui/vendors/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('coreui/vendors/chart.js/js/chart.umd.js') }}"></script>
<script src="{{ asset('coreui/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>
<script src="{{ asset('coreui/vendors/@coreui/utils/js/index.js') }}"></script>
<script src="{{ asset('coreui/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.deleteConfirm = function(formId, counts = null) {
            let text = "এই অ্যাকশনটি ফেরানো সম্ভব নয়!";
            if (counts && typeof counts === 'object' && !(counts instanceof String)) {
                let details = [];
                if (counts.bookings > 0) details.push(`${counts.bookings} টি বুকিং`);
                if (counts.incomes > 0) details.push(`${counts.incomes} টি আয় রেকর্ড`);
                if (counts.expenses > 0) details.push(`${counts.expenses} টি ব্যয় রেকর্ড`);
                if (counts.commissions > 0) details.push(`${counts.commissions} টি কমিশন`);
                if (counts.assets > 0) details.push(`${counts.assets} টি মালামাল অ্যাসাইনমেন্ট`);
                if (details.length > 0) {
                    text = "ডিলিট করলে নিচের ডাটাগুলোও চিরতরে মুছে যাবে:\n\n" + details.join(', ') + "।\n\nআপনি কি নিশ্চিত?";
                }
            }
            Swal.fire({
                title: 'আপনি কি নিশ্চিত?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'হ্যাঁ, ডিলিট করুন!',
                cancelButtonText: 'বাতিল'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        };
    });
</script>
@stack('js')
</body>
</html>
