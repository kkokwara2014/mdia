<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
    @yield('css')
    <style>
    :root {
        --tblr-primary: #13400C;
        --tblr-primary-rgb: 19, 64, 12;
        --tblr-primary-fg: #fff;
        --tblr-danger: #8C160B;
        --tblr-danger-rgb: 140, 22, 11;
        /* Fluid design tokens - must be in layout since app.css is not loaded here */
        --spacing-xs: clamp(0.5rem, 1vw, 0.75rem);
        --spacing-sm: clamp(0.75rem, 2vw, 1rem);
        --spacing-md: clamp(1rem, 3vw, 1.5rem);
        --spacing-lg: clamp(1.5rem, 4vw, 2.5rem);
        --spacing-xl: clamp(2rem, 5vw, 4rem);
        --font-size-xs: clamp(0.75rem, 1.5vw, 0.875rem);
        --font-size-sm: clamp(0.875rem, 2vw, 1rem);
        --font-size-base: clamp(1rem, 2.5vw, 1.125rem);
        --font-size-lg: clamp(1.125rem, 3vw, 1.5rem);
        --avatar-sm: clamp(24px, 5vw, 32px);
        --avatar-md: clamp(40px, 8vw, 60px);
        --avatar-lg: clamp(60px, 12vw, 80px);
        --avatar-xl: clamp(80px, 15vw, 120px);
        --card-padding: clamp(1rem, 3vw, 1.5rem);
        --card-header-padding: clamp(0.75rem, 2.5vw, 1.25rem);
        --card-gap: clamp(1rem, 2.5vw, 1.5rem);
        --card-border-radius: clamp(0.375rem, 0.5vw, 0.5rem);
        --sidebar-width-expanded: clamp(12rem, 20vw, 16rem);
        --sidebar-width-collapsed: clamp(3rem, 5vw, 4rem);
    }
    * { font-family: 'Space Grotesk', sans-serif !important; }
    
    /* Force constrained sizes for logos and avatars - prevent blowout */
    .navbar-vertical img,
    .navbar-vertical .navbar-brand img {
        width: 40px !important;
        height: 40px !important;
        max-width: 48px !important;
        max-height: 48px !important;
        object-fit: contain !important;
    }
    .page-wrapper > .navbar .navbar-brand img {
        width: 28px !important;
        height: 28px !important;
        max-width: 32px !important;
        max-height: 32px !important;
        object-fit: contain !important;
    }
    .page-wrapper .navbar .navbar-nav img.rounded-circle {
        width: 32px !important;
        height: 32px !important;
        max-width: 36px !important;
        max-height: 36px !important;
        object-fit: cover !important;
    }
    /* Prevent any avatar-style images from blowing out */
    img.rounded-circle,
    img[style*="border-radius: 50%"] {
        max-width: 120px !important;
        max-height: 120px !important;
    }
    
    /* Navbar vertical styling */
    .navbar-vertical { 
        background-color: #ffffff !important; 
        scrollbar-width: none; 
        -ms-overflow-style: none; 
    }
    .navbar-vertical::-webkit-scrollbar { display: none; }
    .navbar-vertical .container-fluid { 
        scrollbar-width: none; 
        -ms-overflow-style: none; 
        padding-left: var(--spacing-sm) !important; 
        padding-right: var(--spacing-sm) !important; 
    }
    .navbar-vertical .container-fluid::-webkit-scrollbar { display: none; }
    .navbar-vertical .navbar-brand { 
        display: flex !important; 
        width: 100% !important; 
        justify-content: flex-start !important; 
        text-align: left !important; 
        margin: 0 !important; 
        padding: var(--spacing-sm) !important; 
    }
    .navbar-vertical .navbar-brand a { 
        justify-content: flex-start !important; 
        text-align: left !important; 
        margin-right: 0 !important; 
    }
    .navbar-vertical .container-fluid { justify-content: flex-start !important; }
    .navbar-vertical .nav-item.dropdown .collapse,
    .navbar-vertical .nav-item.dropdown .collapse.collapsing { 
        transition: none !important; 
        overflow: visible !important; 
    }
    
    /* Desktop (≥992px): sidebar pushes content */
    @media (min-width: 992px) {
        .navbar-expand-lg.navbar-vertical { 
            width: var(--sidebar-width-expanded) !important; 
            transition: width 0.2s ease; 
        }
        .navbar-expand-lg.navbar-vertical ~ .page-wrapper { 
            margin-left: var(--sidebar-width-expanded) !important; 
            transition: margin-left 0.2s ease; 
        }
        .page.sidebar-collapsed .navbar-expand-lg.navbar-vertical { 
            width: var(--sidebar-width-collapsed) !important; 
        }
        .page.sidebar-collapsed .navbar-expand-lg.navbar-vertical ~ .page-wrapper { 
            margin-left: var(--sidebar-width-collapsed) !important; 
        }
        .page.sidebar-collapsed .navbar-vertical .nav-link-title { display: none !important; }
        .page.sidebar-collapsed .navbar-vertical .navbar-brand .sidebar-brand-text { display: none !important; }
        .page.sidebar-collapsed .navbar-vertical .navbar-brand a { justify-content: center !important; }
        .page.sidebar-collapsed .navbar-vertical .container-fluid { 
            padding-left: 0.5rem !important; 
            padding-right: 0.5rem !important; 
        }
        .page.sidebar-collapsed .navbar-vertical .navbar-brand { 
            padding-left: 0.5rem !important; 
            padding-right: 0.5rem !important; 
            justify-content: center !important; 
        }
        .page.sidebar-collapsed .navbar-vertical .nav-item.dropdown .collapse { display: none !important; }
        .page.sidebar-collapsed .navbar-vertical .nav-item.dropdown .collapse .navbar-nav { padding-left: 0 !important; }
        .page.sidebar-collapsed .navbar-vertical .nav-link.dropdown-toggle::after { display: none !important; }
        #sidebar-open { display: none !important; }
        #sidebar-backdrop { display: none !important; }
    }
    
    /* Mobile (<992px): sidebar overlays content, fixed off-canvas */
    @media (max-width: 991.98px) {
        .navbar-vertical {
            display: block !important;
            position: fixed !important;
            top: 0;
            left: 0;
            height: 100vh;
            width: clamp(250px, 75vw, 320px);
            z-index: 1050;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            overflow-y: auto;
        }
        .page.sidebar-mobile-open .navbar-vertical { transform: translateX(0); }
        .page-wrapper { margin-left: 0 !important; }
        #sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            transition: opacity 0.25s ease;
        }
        #sidebar-backdrop.active { display: block; }
        .navbar-vertical #sidebar-menu { display: block !important; }
        .navbar-vertical .navbar-brand.d-lg-flex { display: none !important; }
    }
    
    /* Tablet (768px-991px) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .navbar-vertical {
            width: clamp(250px, 40vw, 280px);
        }
    }
    
    body.sidebar-mobile-open { overflow: hidden; }
    .sidebar-backdrop:not(.active) { display: none; }
    
    /* Responsive container padding */
    .page-wrapper > .navbar .container-xl {
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
        padding-left: clamp(1rem, 5vw, 3rem) !important;
        padding-right: clamp(1rem, 5vw, 3rem) !important;
    }
    .page-wrapper .page-body .container-xl {
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
        padding-left: clamp(1rem, 5vw, 3rem) !important;
        padding-right: clamp(1rem, 5vw, 3rem) !important;
    }
    
    /* Small mobile (320px-575px) */
    @media (max-width: 575.98px) {
        .page-wrapper .container-xl {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        .card-body {
            padding: var(--spacing-md) !important;
        }
    }
    
    /* Large desktop (1440px+) */
    @media (min-width: 1440px) {
        .container-xl {
            max-width: 1400px;
        }
    }
    
    /* Tables: no wrapping, horizontal scroll */
    .page-wrapper .table td,
    .page-wrapper .table th { white-space: nowrap; }
    .page-wrapper .table { width: max-content; min-width: 100%; }
    .page-wrapper .table-responsive,
    .page-wrapper .card-body:has(.table),
    .page-wrapper .card:has(> .table) {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Responsive card system */
    .card {
        border-radius: var(--card-border-radius);
        margin-bottom: var(--card-gap);
    }
    .card-body {
        padding: var(--card-padding);
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
    }
    .card-body img:not([style*="width"]):not(.rounded-circle):not(.avatar) {
        max-width: 100%;
        height: auto;
    }
    .card-body table {
        width: 100%;
    }
    .card-header {
        padding: var(--card-header-padding) var(--card-padding);
    }
    .card-footer {
        padding: var(--card-header-padding) var(--card-padding);
    }
    
    /* Card grids - must override Tabler's row/row-cards flex behavior */
    .row-cards.stats-grid {
        display: grid !important;
        gap: var(--card-gap);
    }
    .row-cards {
        display: grid;
        gap: var(--card-gap);
    }
    
    /* Stats grid: fluid clamp 3 cols (desktop) down to 1 col (mobile) */
    .row-cards.stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(280px, 100%), 1fr)) !important;
    }
    
    /* Small mobile: 1 column for other row-cards */
    @media (max-width: 575.98px) {
        .row-cards {
            grid-template-columns: 1fr;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    }
    
    /* Tablet+: card shadows */
    @media (min-width: 576px) {
        .card {
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
    }
    
    /* Desktop: content grid */
    @media (min-width: 992px) {
        .row-cards.content-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    /* Card layouts */
    .card-responsive-stack {
        display: flex;
        flex-direction: column;
        gap: var(--card-gap);
    }
    .card-title {
        font-size: var(--font-size-lg);
        margin-bottom: var(--spacing-sm);
    }
    .card-centered .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    /* Page header with actions: title left, buttons right on desktop; stacked on mobile */
    .page-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-width: 0;
    }
    .page-header-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
    }
    @media (max-width: 767.98px) {
        .page-header-actions {
            flex-direction: column;
            align-items: stretch;
        }
        .page-header-buttons {
            flex-direction: column;
        }
        .page-header-buttons .btn {
            width: 100%;
        }
    }
    
    /* Member show: prevent cards from overflowing */
    .member-show-content {
        max-width: 100%;
        min-width: 0;
        overflow-x: hidden;
    }
    .member-show-content .card {
        max-width: 100%;
    }
    
    /* Member show: info row - flex row on desktop, stacked on mobile */
    .member-info-row {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        align-items: flex-start;
    }
    .member-info-avatar {
        flex-shrink: 0;
    }
    .member-info-details {
        flex: 1;
        min-width: 0;
    }
    .member-info-meta {
        flex-shrink: 0;
    }
    @media (max-width: 575.98px) {
        .member-info-row {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .member-info-meta {
            text-align: center;
        }
    }
</style>
</head>
<body>
<div class="page" id="page-wrapper-root">
    <div class="sidebar-backdrop" id="sidebar-backdrop" aria-hidden="true"></div>
    <aside class="navbar navbar-vertical navbar-expand-lg" id="sidebar" data-bs-theme="light">
        <div class="container-fluid">
            {{-- Mobile: logo + title + close on one row (same as desktop brand, close on far right) --}}
            <div class="d-flex d-lg-none align-items-center justify-content-between w-100 py-2">
                <a href="{{ route('dashboard') }}" class="text-reset text-decoration-none d-flex align-items-center text-start min-w-0 flex-grow-1">
                    <img src="{{ asset('assets/transparent_circulr_logo.png') }}" alt="MDIA" style="height: var(--avatar-md); width: var(--avatar-md); object-fit: contain; flex-shrink: 0;">
                    <span class="ms-2 fw-semibold sidebar-brand-text text-truncate">MDIA</span>
                </a>
                <button type="button" class="btn btn-icon flex-shrink-0 ms-2" id="sidebar-close" aria-label="Close sidebar">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            {{-- Desktop: logo + title only --}}
            <h1 class="navbar-brand navbar-brand-autodark d-none d-lg-flex">
                <a href="{{ route('dashboard') }}" class="text-reset text-decoration-none d-flex align-items-center text-start">
                    <img src="{{ asset('assets/transparent_circulr_logo.png') }}" alt="MDIA" style="height: var(--avatar-md); width: var(--avatar-md); object-fit: contain; flex-shrink: 0;">
                    <span class="ms-2 fw-semibold sidebar-brand-text">MDIA</span>
                </a>
            </h1>
            <div class="collapse navbar-collapse show" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon"><i class="ti ti-home"></i></span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                    @php
                        $canAccessMembers = auth()->user()->hasPermission('admin') || auth()->user()->hasPermission('super_admin');
                        $canValidatePayment = auth()->user()->hasPermission('validate_payment');
                        $isMember = !auth()->user()->hasPermission('validate_payment') && !auth()->user()->hasPermission('admin') && !auth()->user()->hasPermission('super_admin');
                        $canAccessPaymentTypes = auth()->user()->hasPermission('admin') || auth()->user()->hasPermission('super_admin');
                        $canGenerateReports = auth()->user()->hasPermission('generate_reports');
                    @endphp
                    @if($canAccessMembers)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-members" data-bs-toggle="collapse" data-bs-target="#navbar-members" aria-expanded="false">
                            <span class="nav-link-icon"><i class="ti ti-users"></i></span>
                            <span class="nav-link-title">Members</span>
                        </a>
                        <div class="collapse" id="navbar-members">
                            <ul class="navbar-nav" style="padding-left: 1.25rem;">
                                <li class="nav-item"><a class="nav-link" href="{{ route('members.index') }}"><span class="nav-link-icon"><i class="ti ti-users"></i></span><span class="nav-link-title">All Members</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('members.create') }}"><span class="nav-link-icon"><i class="ti ti-user-plus"></i></span><span class="nav-link-title">Add Member</span></a></li>
                            </ul>
                        </div>
                    </li>
                    @endif
                    @if($canValidatePayment || $isMember)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-payments" data-bs-toggle="collapse" data-bs-target="#navbar-payments" aria-expanded="false">
                            <span class="nav-link-icon"><i class="ti ti-credit-card"></i></span>
                            <span class="nav-link-title">Payments</span>
                        </a>
                        <div class="collapse" id="navbar-payments">
                            <ul class="navbar-nav" style="padding-left: 1.25rem;">
                                @if($canValidatePayment)
                                <li class="nav-item"><a class="nav-link" href="{{ route('payments.index') }}"><span class="nav-link-icon"><i class="ti ti-credit-card"></i></span><span class="nav-link-title">All Payments</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('payments.create') }}"><span class="nav-link-icon"><i class="ti ti-plus"></i></span><span class="nav-link-title">Add Payment</span></a></li>
                                @endif
                                @if($isMember)
                                <li class="nav-item"><a class="nav-link" href="{{ route('payments.my-payments') }}"><span class="nav-link-icon"><i class="ti ti-wallet"></i></span><span class="nav-link-title">My Payments</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('payments.submit') }}"><span class="nav-link-icon"><i class="ti ti-upload"></i></span><span class="nav-link-title">Submit Payment</span></a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif
                    @if($canAccessPaymentTypes)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('payment-types.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-tag"></i></span>
                            <span class="nav-link-title">Payment Types</span>
                        </a>
                    </li>
                    @endif
                    @if($canGenerateReports)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-chart-bar"></i></span>
                            <span class="nav-link-title">Payment Reports</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('super_admin'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('leaders.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-crown"></i></span>
                            <span class="nav-link-title">Leaders</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-admin" data-bs-toggle="collapse" data-bs-target="#navbar-admin" aria-expanded="false">
                            <span class="nav-link-icon"><i class="ti ti-settings"></i></span>
                            <span class="nav-link-title">Administration</span>
                        </a>
                        <div class="collapse" id="navbar-admin">
                            <ul class="navbar-nav" style="padding-left: 1.25rem;">
                                <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}"><span class="nav-link-icon"><i class="ti ti-shield"></i></span><span class="nav-link-title">Roles</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permissions.index') }}"><span class="nav-link-icon"><i class="ti ti-key"></i></span><span class="nav-link-title">Permissions</span></a></li>
                            </ul>
                        </div>
                    </li>
                    @endif
                    <li class="nav-item mt-3 pt-3 border-top">
                        <form method="POST" action="{{ route('logout') }}" class="d-block">
                            @csrf
                            <button type="submit" onclick="return confirm('Do you want to logout?');" class="nav-link border-0 bg-transparent w-100 text-start d-flex align-items-center p-0">
                                <span class="nav-link-icon"><i class="ti ti-logout"></i></span>
                                <span class="nav-link-title">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    <div class="page-wrapper">
        <header class="navbar navbar-expand-md navbar-light d-print-none sticky-top">
            <div class="container-xl d-flex align-items-center w-100">
                {{-- Mobile: logo + shrunk app name. Desktop: app name only (no logo) --}}
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3 mb-0 me-auto">
                    <a href="{{ route('dashboard') }}" class="text-reset text-decoration-none d-flex d-lg-none align-items-center text-start min-w-0">
                        <img src="{{ asset('assets/transparent_circulr_logo.png') }}" alt="MDIA" class="flex-shrink-0" style="height: var(--avatar-sm); width: var(--avatar-sm); object-fit: contain;">
                        <span class="page-title ms-2 text-truncate" style="font-size: 0.9rem;">{{ config('app.name') }}</span>
                    </a>
                    <span class="page-title d-none d-lg-inline">{{ config('app.name') }}</span>
                </h1>
                <div class="navbar-nav flex-row align-items-center gap-1">
                    <div class="nav-item d-lg-none">
                        <button type="button" class="btn btn-ghost-secondary btn-icon" id="sidebar-open" aria-label="Open sidebar">
                            <i class="ti ti-menu-2"></i>
                        </button>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0 align-items-center" data-bs-toggle="dropdown" aria-label="Open user menu">
                            <img src="{{ auth()->user()->getAvatarUrl() }}" alt="{{ auth()->user()->name }}" class="rounded-circle me-2" style="width: var(--avatar-sm); height: var(--avatar-sm); object-fit: cover;">
                            <span class="nav-link-title d-none d-md-block">{{ auth()->user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('profile.show') }}">My Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" >
                                @csrf
                                <button type="submit" onclick="return confirm('Do you want to logout?');" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="page-body">
            <div class="container-xl">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>{{ session('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>{{ session('error') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
<script>
(function () {
    var page = document.getElementById('page-wrapper-root');
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    var openBtn = document.getElementById('sidebar-open');
    var closeBtn = document.getElementById('sidebar-close');

    function isDesktop() { return window.innerWidth >= 992; }

    function openMobileSidebar() {
        if (page) page.classList.add('sidebar-mobile-open');
        if (backdrop) backdrop.classList.add('active');
        document.body.classList.add('sidebar-mobile-open');
    }

    function closeMobileSidebar() {
        if (page) page.classList.remove('sidebar-mobile-open');
        if (backdrop) backdrop.classList.remove('active');
        document.body.classList.remove('sidebar-mobile-open');
    }

    if (openBtn) openBtn.addEventListener('click', openMobileSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeMobileSidebar);
    if (backdrop) backdrop.addEventListener('click', closeMobileSidebar);

    window.addEventListener('popstate', closeMobileSidebar);

    window.addEventListener('resize', function () {
        if (isDesktop()) closeMobileSidebar();
    });
})();
</script>
@yield('js')
</body>
</html>
