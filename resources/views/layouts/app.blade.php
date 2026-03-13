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
    <style>* { font-family: 'Space Grotesk', sans-serif !important; }</style>
</head>
<body>
<div class="page">
    <aside class="navbar navbar-vertical navbar-expand-sm position-absolute" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="navbar-brand navbar-brand-autodark">
                <a href="{{ route('dashboard') }}" class="text-reset text-decoration-none">
                    <img src="/assets/logo_full.jpeg" alt="MDIA" style="height: 40px; width: 40px; object-fit: cover; border-radius: 6px;">
                </a>
            </h1>
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon"><i class="ti ti-home"></i></span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-members" data-bs-toggle="collapse" data-bs-target="#navbar-members" aria-expanded="false">
                            <span class="nav-link-icon"><i class="ti ti-users"></i></span>
                            <span class="nav-link-title">Members</span>
                        </a>
                        <div class="collapse" id="navbar-members">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="{{ route('members.index') }}"><span class="nav-link-icon"><i class="ti ti-users"></i></span><span class="nav-link-title">All Members</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('members.create') }}"><span class="nav-link-icon"><i class="ti ti-user-plus"></i></span><span class="nav-link-title">Add Member</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-payments" data-bs-toggle="collapse" data-bs-target="#navbar-payments" aria-expanded="false">
                            <span class="nav-link-icon"><i class="ti ti-credit-card"></i></span>
                            <span class="nav-link-title">Payments</span>
                        </a>
                        <div class="collapse" id="navbar-payments">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="{{ route('payments.index') }}"><span class="nav-link-icon"><i class="ti ti-credit-card"></i></span><span class="nav-link-title">All Payments</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('payments.create') }}"><span class="nav-link-icon"><i class="ti ti-plus"></i></span><span class="nav-link-title">Log Payment</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('payment-types.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-tag"></i></span>
                            <span class="nav-link-title">Payment Types</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-chart-bar"></i></span>
                            <span class="nav-link-title">Payment Reports</span>
                        </a>
                    </li>
                    @if(auth()->user()->hasPermission('super_admin'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-admin" data-bs-toggle="collapse" data-bs-target="#navbar-admin" aria-expanded="false">
                            <span class="nav-link-icon"><i class="ti ti-settings"></i></span>
                            <span class="nav-link-title">Administration</span>
                        </a>
                        <div class="collapse" id="navbar-admin">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}"><span class="nav-link-icon"><i class="ti ti-shield"></i></span><span class="nav-link-title">Roles</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('permissions.index') }}"><span class="nav-link-icon"><i class="ti ti-key"></i></span><span class="nav-link-title">Permissions</span></a></li>
                            </ul>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </aside>
    <div class="page-wrapper">
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <span class="page-title">@yield('title', config('app.name'))</span>
                </h1>
                <div class="navbar-nav flex-row order-md-last ms-auto align-items-center">
                    <span class="nav-link-title me-3">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">Logout</button>
                    </form>
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
@yield('js')
</body>
</html>
