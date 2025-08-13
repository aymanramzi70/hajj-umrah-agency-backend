<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'وكالة الحج والعمرة') }} - لوحة التحكم</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f4f7f6;
        }

        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
            border-radius: 5px;
        }

        .main-content {
            padding: 20px;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    {{ config('app.name', 'وكالة الحج والعمرة') }} - لوحة التحكم
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    </ul>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                            <div class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">الملف الشخصي</a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                                    تسجيل الخروج
                                                </a>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </div>
                </div>
            </div>
        </nav>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                </div>
            </div>
        </div>


    </div>

    <div class="d-flex">
        <div class="sidebar p-3 text-white">
            <h5 class="mb-4">القائمة الرئيسية</h5>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-fill"></i> لوحة القيادة
                    </a>
                </li>

                @if (Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="bi bi-people-fill"></i> إدارة المستخدمين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}"
                            href="{{ route('branches.index') }}">
                            <i class="bi bi-geo-alt-fill"></i> الفروع
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('agents.*') ? 'active' : '' }}"
                            href="{{ route('agents.index') }}">
                            <i class="bi bi-building-fill"></i> الوكلاء الخارجيين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                            href="{{ route('notifications.create') }}">
                            <i class="bi bi-bell-fill"></i> إرسال إشعارات
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'branch_manager')
                @endif

                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'reservation_agent' || Auth::user()->role === 'agent')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                            href="{{ route('customers.index') }}">
                            <i class="bi bi-person-lines-fill"></i> العملاء
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}"
                            href="{{ route('packages.index') }}">
                            <i class="bi bi-box-seam-fill"></i> الباقات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}"
                            href="{{ route('bookings.index') }}">
                            <i class="bi bi-calendar-check-fill"></i> الحجوزات
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'accountant' || Auth::user()->role === 'reservation_agent')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                            href="{{ route('payments.index') }}">
                            <i class="bi bi-wallet-fill"></i> المدفوعات
                        </a>
                    </li>
                @endif

            </ul>
        </div>


        <main class="main-content flex-grow-1">
            {{ $slot }}
        </main>
    </div>
    </div>
    @stack('scripts')
</body>

</html>
