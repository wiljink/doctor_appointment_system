<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MediSync Clinic') }} - @yield('title', 'Doctor Appointment System')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN fallback for instant styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        clinic: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen font-sans text-slate-800 antialiased selection:bg-teal-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Demo Role Quick Switcher Banner (Top bar) -->
    <div class="no-print bg-slate-900 text-slate-300 text-xs py-1.5 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    Clinic Appointment System
                </span>
                <span class="hidden sm:inline text-slate-400">⚡ 1-Click Demo Login:</span>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('quick.login', 'admin') }}" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-amber-300 hover:text-amber-200 transition font-medium text-[11px] border border-slate-700">
                    👑 Admin
                </a>
                <a href="{{ route('quick.login', 'doctor') }}" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-cyan-300 hover:text-cyan-200 transition font-medium text-[11px] border border-slate-700">
                    🩺 Doctor (Dr. Sarah)
                </a>
                <a href="{{ route('quick.login', 'patient') }}" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-emerald-300 hover:text-emerald-200 transition font-medium text-[11px] border border-slate-700">
                    👤 Patient (John Doe)
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="no-print sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 flex items-center justify-center text-white shadow-md shadow-teal-500/20 group-hover:scale-105 transition transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <span class="font-black text-xl leading-none">+</span>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-teal-700 transition">
                                Medi<span class="text-teal-600">Sync</span>
                            </span>
                            <span class="block text-[10px] uppercase tracking-wider font-semibold text-slate-500 -mt-1">
                                Clinic & Specialist Care
                            </span>
                        </div>
                    </a>

                    <!-- Primary Links (Desktop) -->
                    <nav class="hidden md:flex items-center space-x-1 ml-6 border-l border-slate-200 pl-6 text-sm font-medium">
                        <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Home
                        </a>
                        <a href="{{ route('doctors.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('doctors.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Our Doctors
                        </a>
                        <a href="{{ route('booking.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('booking.index') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Book Appointment
                        </a>

                        @auth
                            @if(auth()->user()->isPatient())
                                <a href="{{ route('patient.dashboard') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('patient.dashboard') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    My Appointments
                                </a>
                                <a href="{{ route('patient.records.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('patient.records.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Medical Records
                                </a>
                            @elseif(auth()->user()->isDoctor())
                                <a href="{{ route('doctor.dashboard') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('doctor.dashboard') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Doctor Dashboard
                                </a>
                                <a href="{{ route('doctor.appointments') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('doctor.appointments*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Appointments
                                </a>
                                <a href="{{ route('doctor.schedule') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('doctor.schedule*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Schedule & Leaves
                                </a>
                            @elseif(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Admin Dashboard
                                </a>
                                <a href="{{ route('admin.appointments.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.appointments*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Appointments
                                </a>
                                <a href="{{ route('admin.doctors') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.doctors*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Doctors
                                </a>
                                <a href="{{ route('admin.departments') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.departments*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Departments
                                </a>
                                <a href="{{ route('admin.patients') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.patients*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    Patients
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Side Actions / User Profile -->
                <div class="hidden sm:flex items-center space-x-3">
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-teal-600 hover:bg-teal-700 text-white shadow-sm shadow-teal-600/30 transition transform active:scale-95">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Book Now
                    </a>

                    @auth
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none">
                                <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-sm border border-teal-200">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="text-left hidden lg:block">
                                    <div class="text-sm font-semibold text-slate-800 leading-tight">{{ auth()->user()->name }}</div>
                                    <div class="text-xs font-medium text-teal-600 capitalize">{{ auth()->user()->role }}</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs text-slate-500">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[11px] font-semibold rounded-md bg-teal-50 text-teal-700 capitalize">
                                        {{ auth()->user()->role }} Account
                                    </span>
                                </div>

                                @if(auth()->user()->isPatient())
                                    <a href="{{ route('patient.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        Patient Portal
                                    </a>
                                    <a href="{{ route('patient.records.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        My Medical Records
                                    </a>
                                @elseif(auth()->user()->isDoctor())
                                    <a href="{{ route('doctor.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Doctor Dashboard
                                    </a>
                                    <a href="{{ route('doctor.schedule') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        My Schedule & Hours
                                    </a>
                                @elseif(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                        Clinic Administration
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-teal-700 hover:bg-slate-100 rounded-xl transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl border border-teal-200 transition">
                            Register
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center sm:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="sm:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Home</a>
            <a href="{{ route('doctors.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Our Doctors</a>
            <a href="{{ route('booking.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-teal-700 font-semibold bg-teal-50">Book Appointment</a>

            @auth
                @if(auth()->user()->isPatient())
                    <a href="{{ route('patient.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">My Appointments</a>
                    <a href="{{ route('patient.records.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Medical Records</a>
                @elseif(auth()->user()->isDoctor())
                    <a href="{{ route('doctor.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Doctor Dashboard</a>
                    <a href="{{ route('doctor.appointments') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Appointments</a>
                    <a href="{{ route('doctor.schedule') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">My Schedule</a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Admin Dashboard</a>
                    <a href="{{ route('admin.appointments.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">All Appointments</a>
                    <a href="{{ route('admin.doctors') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Doctors</a>
                    <a href="{{ route('admin.departments') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Departments</a>
                @endif

                <div class="border-t border-slate-200 pt-2">
                    <div class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm text-rose-600 font-semibold py-1">Log Out</button>
                    </form>
                </div>
            @else
                <div class="border-t border-slate-200 pt-2 flex space-x-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl">Sign In</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-sm font-semibold text-white bg-teal-600 rounded-xl">Register</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Global Flash Notification Banner -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
            @if(session('status'))
                <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-3 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs">
                    <div class="flex items-center mb-1">
                        <svg class="w-5 h-5 mr-2 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="font-bold text-sm">Please resolve the following issues:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="no-print bg-slate-900 text-slate-400 mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center text-white font-bold text-lg">
                            +
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight">MediSync Clinic</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Modern patient-centered clinic & specialty care appointment system providing frictionless doctor booking and health record management.
                    </p>
                    <div class="pt-2 text-xs text-teal-400 font-semibold">
                        Emergency Hotline: 24/7 (555) 911-CARE
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Specialties</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('doctors.index', ['department' => 'cardiology']) }}" class="hover:text-teal-300 transition">Cardiology & Heart Care</a></li>
                        <li><a href="{{ route('doctors.index', ['department' => 'pediatrics']) }}" class="hover:text-teal-300 transition">Pediatrics & Child Health</a></li>
                        <li><a href="{{ route('doctors.index', ['department' => 'dermatology']) }}" class="hover:text-teal-300 transition">Dermatology & Skin</a></li>
                        <li><a href="{{ route('doctors.index', ['department' => 'orthopedics']) }}" class="hover:text-teal-300 transition">Orthopedics & Spine</a></li>
                        <li><a href="{{ route('doctors.index', ['department' => 'general-medicine']) }}" class="hover:text-teal-300 transition">Family & General Medicine</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Quick Navigation</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-teal-300 transition">Home Page</a></li>
                        <li><a href="{{ route('doctors.index') }}" class="hover:text-teal-300 transition">Doctor Directory</a></li>
                        <li><a href="{{ route('booking.index') }}" class="hover:text-teal-300 transition">Schedule Consultation</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-teal-300 transition">Patient Portal Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-teal-300 transition">Create New Patient Account</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Clinic Hours & Location</h4>
                    <p class="text-xs leading-relaxed text-slate-400">
                        📍 100 Medical Center Parkway, Suite 400<br>
                        Springfield Metro Health District
                    </p>
                    <div class="mt-3 text-xs space-y-1">
                        <div class="flex justify-between text-slate-300">
                            <span>Mon - Fri:</span> <span>8:00 AM - 8:00 PM</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Saturday:</span> <span>9:00 AM - 5:00 PM</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Sunday:</span> <span>Emergency On-Call</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} MediSync Clinic Management System. All rights reserved.</p>
                <p class="mt-2 sm:mt-0">Built with Laravel & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
