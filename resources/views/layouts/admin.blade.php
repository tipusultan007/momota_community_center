<!DOCTYPE html>
<!--
 * CoreUI shell for legacy SaaS super-admin area.
 * (Admin routes currently redirect to the main dashboard; kept for BC.)
-->
<html lang="bn" data-coreui-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Saas Convention Hall') }} - এডমিন</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('coreui/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('coreui/css/vendors/simplebar.css') }}">
    <link href="{{ asset('coreui/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('coreui/custom-compat.css') }}" rel="stylesheet">
    <script src="{{ asset('coreui/js/config.js') }}"></script>
    <script>
        // Force light mode: clear any stored dark/auto preference from CoreUI template.
        try { localStorage.setItem('coreui-free-bootstrap-admin-template-theme', 'light'); } catch (e) {}
        document.documentElement.setAttribute('data-coreui-theme', 'light');
    </script>
    <style>
        body, .sidebar-nav, .header, .card, .btn { font-family: 'Anek Bangla','Inter',sans-serif; }
    </style>
    @stack('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand text-white fw-bold fs-5">SaaS Admin</div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 48 16 224h64v208h128V320h96v112h128V224h64z"/></svg>
                ড্যাশবোর্ড
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 256a112 112 0 1 0-112-112 112 112 0 0 0 112 112zm-192 192v-32a96 96 0 0 1 96-96h192a96 96 0 0 1 96 96v32z"/></svg>
                প্রতিষ্ঠানসমূহ (টেন্যান্ট)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M112 64h288v32H112zm0 96h288v32H112zm0 96h176v32H112zM480 64H32v384h448zM64 416V96h384v320z"/></svg>
                সাবস্ক্রিপশন প্ল্যান
            </a>
        </li>
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>

<div class="wrapper d-flex flex-column min-vh-100">
    <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
            <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px">
                <svg class="icon icon-lg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M80 96h352v32H80zm0 144h352v32H80zm0 144h352v32H80z"/></svg>
            </button>
            <ul class="header-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link py-0 pe-0 d-flex align-items-center gap-2" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center fw-bold">SA</div>
                        <div class="d-none d-xl-block text-start">
                            <div class="fw-semibold small">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'সুপার এডমিন' }}</div>
                            <div class="text-body-secondary" style="font-size:.72rem">সুপার এডমিন</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pt-0">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">লগআউট</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </header>
    <div class="body flex-grow-1">
        <div class="container-fluid px-4">{{ $slot }}</div>
    </div>
    <footer class="footer px-4">
        <div>কপিরাইট &copy; {{ date('Y') }} {{ config('app.name') }}.</div>
        <div class="ms-auto">Powered by <a href="https://coreui.io">CoreUI</a></div>
    </footer>
</div>
<script src="{{ asset('coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('coreui/vendors/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('coreui/js/main.js') }}"></script>
@stack('js')
</body>
</html>
