<!doctype html>
<html lang="bn" dir="ltr">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ $title ?? 'ড্যাশবোর্ড' }} | {{ config('app.name', 'SaaS Hall') }}</title>
    <!-- CSS files -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-vendors.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    @stack('css')
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap');
      :root {
        --tblr-font-sans-serif: 'Anek Bangla', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --tblr-primary: #000000;
        --tblr-secondary: #006c49;
        --tblr-bg-surface: #f7f9fb;
        --tblr-bg-page: #f2f4f6;
      }
      body, .page, .navbar, .nav-link, .nav-link-title, .page-title, .table, .card, input, select, textarea, button {
        font-family: 'Anek Bangla', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
      }
      body {
        font-feature-settings: "cv03", "cv04", "cv11";
        background-color: var(--tblr-bg-page);
      }
      .container-fluid, .container-lg, .container-md {
        max-width: 100% !important;
      }
      [x-cloak] {
        display: none !important;
      }
      .navbar-vertical {
        background: linear-gradient(180deg, #002d26 0%, #004035 100%) !important;
        border-right: 1px solid rgba(245, 166, 35, 0.15) !important;
      }
      .navbar-vertical .navbar-brand {
        color: #ffffff !important;
        padding-top: 1.25rem !important;
        padding-bottom: 1.25rem !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      }
      .navbar-vertical .navbar-collapse {
        display: block;
        visibility: visible;
      }
      .navbar-vertical .nav-link,
      .navbar-vertical .nav-link-title,
      .navbar-vertical .nav-link-icon {
        color: rgba(255, 255, 255, 0.85) !important;
        transition: all 0.2s ease;
      }
      .navbar-vertical .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
        color: #ffffff !important;
      }
      .navbar-vertical .nav-item.active > .nav-link,
      .navbar-vertical .nav-link.active {
        background-color: rgba(245, 166, 35, 0.14) !important;
        border-left: 3.5px solid #F5A623 !important;
        padding-left: calc(1rem - 3.5px) !important;
      }
      .navbar-vertical .nav-item.active > .nav-link .nav-link-title,
      .navbar-vertical .nav-item.active > .nav-link .nav-link-icon,
      .navbar-vertical .nav-link.active .nav-link-title,
      .navbar-vertical .nav-link.active .nav-link-icon {
        color: #ffffff !important;
      }
      .navbar-vertical .nav-item.active > .nav-link .nav-link-icon,
      .navbar-vertical .nav-link.active .nav-link-icon {
        color: #F5A623 !important;
      }
      .navbar-vertical .nav-item.dropdown > .dropdown-menu {
        display: none;
        position: static;
        float: none;
        transform: none !important;
        margin-top: 0;
        margin-left: 1rem;
        padding-top: .35rem;
        background: transparent;
        border: 0;
        box-shadow: none;
      }
      .navbar-vertical .nav-item.dropdown.active > .dropdown-menu,
      .navbar-vertical .nav-item.dropdown > .dropdown-menu.show {
        display: block;
      }
      .navbar-vertical .nav-item.dropdown > .nav-link[aria-expanded="true"] {
        background-color: rgba(245, 166, 35, 0.1) !important;
        border-left: 3.5px solid #F5A623 !important;
        padding-left: calc(1rem - 3.5px) !important;
      }
      .navbar-vertical .dropdown-item {
        color: rgba(255, 255, 255, 0.75) !important;
      }
      .navbar-vertical .dropdown-item:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.06) !important;
      }
      .navbar-vertical .dropdown-item.active {
        background-color: rgba(245, 166, 35, 0.12) !important;
        border-left: 3px solid #F5A623 !important;
        color: #F5A623 !important;
        padding-left: calc(1rem - 3px) !important;
      }
      @media (max-width: 991.98px) {
        .navbar-vertical .navbar-collapse {
          display: none;
        }

        .navbar-vertical .navbar-collapse.show {
          display: block;
        }
      }
    </style>
  </head>
  <body>
    <div class="page">
      <!-- Sidebar -->
      <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
            <span class="navbar-toggler-icon"></span>
          </button>
          <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none py-1">
              <div class="rounded-circle p-1 me-2 bg-white d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; border: 2.2px solid #F5A623; flex-shrink: 0;">
                <img src="{{ asset('logo.png') }}" alt="Momota Logo" style="width: 32px; height: 32px; object-fit: contain;">
              </div>
              <div class="text-start">
                <div class="fw-bold text-white fs-3 lh-1" style="font-family: 'Manrope', sans-serif;">মমতা</div>
                <div class="fw-bold" style="color: #F5A623; font-size: 9.5px; letter-spacing: 0.8px;">COMMUNITY CENTER</div>
              </div>
            </a>
          </h1>
          <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
              <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                  </span>
                  <span class="nav-link-title">হোম (ড্যাশবোর্ড)</span>
                </a>
              </li>
              
              @can('manage-bookings')
              <li class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('calendar.index') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                  </span>
                  <span class="nav-link-title">বুকিং ক্যালেন্ডার</span>
                </a>
              </li>
              <li class="nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('bookings.index') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                  </span>
                  <span class="nav-link-title">বুকিংসমূহ</span>
                </a>
              </li>
              <li class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('customers.index') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                  </span>
                  <span class="nav-link-title">গ্রাহক তালিকা (CRM)</span>
                </a>
              </li>
              @endcan

              @can('manage-financials')
              <li
                x-data="{ open: {{ request()->routeIs('transactions.*') || request()->routeIs('accounting.*') || request()->routeIs('income-categories.*') || request()->routeIs('expense-categories.*') || request()->routeIs('incomes.*') || request()->routeIs('expenses.*') || request()->routeIs('vendors.*') || request()->routeIs('commissions.*') || request()->routeIs('assets.*') ? 'true' : 'false' }} }"
                class="nav-item dropdown {{ request()->routeIs('transactions.*') || request()->routeIs('accounting.*') || request()->routeIs('income-categories.*') || request()->routeIs('expense-categories.*') || request()->routeIs('incomes.*') || request()->routeIs('expenses.*') || request()->routeIs('vendors.*') || request()->routeIs('commissions.*') || request()->routeIs('assets.*') ? 'active' : '' }}"
              >
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  @click.prevent="open = !open"
                  :aria-expanded="open ? 'true' : 'false'"
                >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7l0 10" /><path d="M7 12l10 0" /><path d="M20 12l-16 0" /></svg>
                  </span>
                  <span class="nav-link-title">হিসাব নিকাশ</span>
                </a>
                <div
                  class="dropdown-menu"
                  :class="{ 'show': open }"
                >
                  <a class="dropdown-item {{ request()->routeIs('accounting.*') ? 'active' : '' }}" href="{{ route('accounting.index') }}">মূল খাতা (লেজার)</a>
                  <a class="dropdown-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">সকল লেনদেন</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item fw-bold text-success {{ request()->routeIs('incomes.*') ? 'active' : '' }}" href="{{ route('incomes.index') }}">আয় ব্যবস্থাপনা</a>
                  <a class="dropdown-item fw-bold text-danger {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">ব্যয় ব্যবস্থাপনা</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item {{ request()->routeIs('income-categories.*') ? 'active' : '' }}" href="{{ route('income-categories.index') }}">আয় ক্যাটাগরি</a>
                  <a class="dropdown-item {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}">ব্যয় ক্যাটাগরি</a>
                  <a class="dropdown-item {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">ভেন্ডর তালিকা</a>
                  <a class="dropdown-item {{ request()->routeIs('commissions.*') ? 'active' : '' }}" href="{{ route('commissions.index') }}">কমিশন ও ভেন্ডর পাওনা</a>
                  <a class="dropdown-item {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">মালামাল ও ইনভেন্টরি</a>
                </div>
              </li>
              
              <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.index') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20l14 0" /></svg>
                  </span>
                  <span class="nav-link-title">রিপোর্ট ও অ্যানালিটিক্স</span>
                </a>
              </li>
              @endcan

              @can('manage-employees')
              <li class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('employees.index') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                  </span>
                  <span class="nav-link-title">স্টাফ ম্যানেজমেন্ট</span>
                </a>
              </li>
              <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                  </span>
                  <span class="nav-link-title">ইউজার ও ভূমিকা</span>
                </a>
              </li>
              @endcan


              @can('manage-settings')
              <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.index') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37a1.724 1.724 0 0 0 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                  </span>
                  <span class="nav-link-title">সিস্টেম সেটিংস</span>
                </a>
              </li>
              @endcan
            </ul>
          </div>
        </div>
      </aside>
      <div class="page-wrapper">
        <!-- Header -->
        <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
          <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-nav flex-row order-md-first">
            </div>

            <div class="navbar-nav flex-row order-md-last">
              @php
                $currentUser = auth()->user() ?? auth('admin')->user();
              @endphp
              <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                  <span class="avatar avatar-sm">{{ $currentUser ? mb_substr($currentUser->name, 0, 2) : 'AD' }}</span>
                  <div class="d-none d-xl-block ps-2">
                    <div>{{ $currentUser?->name ?? 'এডমিন' }}</div>
                    <div class="mt-1 small text-secondary">{{ $currentUser?->roles?->first()?->name ?? 'এডমিনিস্ট্রেটর' }}</div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                      লগআউট
                    </button>
                  </form>
                </div>
              </div>
            </div>
            <div class="collapse navbar-collapse" id="navbar-menu">
            </div>
          </div>
        </header>

        <div class="page-body">
          <div class="container-fluid">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
              <div class="d-flex">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                </div>
                <div>{{ session('success') }}</div>
              </div>
              <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
              <div class="d-flex">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>
                </div>
                <div>{{ session('error') }}</div>
              </div>
              <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            @isset($header)
                {{ $header }}
            @endisset
            {{ $slot }}
          </div>
        </div>

        <footer class="footer footer-transparent d-print-none">
          <div class="container-fluid">
            <div class="row text-center align-items-center flex-row-reverse">
              <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                  <li class="list-inline-item">
                    কপিরাইট &copy; {{ date('Y') }}
                    <a href="." class="link-secondary">{{ config('app.name') }}</a>.
                    সর্বস্বত্ব সংরক্ষিত।
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
    <!-- Libs JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.deleteConfirm = function(formId, counts = null) {
                let text = "এই অ্যাকশনটি ফেরানো সম্ভব নয়!";
                
                if (counts) {
                    let details = [];
                    if (counts.bookings > 0) details.push(`${counts.bookings} টি বুকিং`);
                    if (counts.incomes > 0) details.push(`${counts.incomes} টি আয় রেকর্ড`);
                    if (counts.expenses > 0) details.push(`${counts.expenses} টি ব্যয় রেকর্ড`);
                    if (counts.commissions > 0) details.push(`${counts.commissions} টি কমিশন`);
                    if (counts.assets > 0) details.push(`${counts.assets} টি মালামাল অ্যাসাইনমেন্ট`);
                    
                    if (details.length > 0) {
                        text = "এই হলটি ডিলিট করলে নিচের ডাটাগুলোও চিরতরে মুছে যাবে:\n\n" + details.join(', ') + "।\n\nআপনি কি নিশ্চিত?";
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
                })
            };

            // Ensure Bootstrap dropdowns work seamlessly in dynamic tables
            if (window.bootstrap && window.bootstrap.Dropdown) {
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
                    new window.bootstrap.Dropdown(el);
                });
            }
        });
    </script>
    @stack('js')
  </body>
</html>
