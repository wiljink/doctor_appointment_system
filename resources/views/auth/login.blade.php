@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-teal-600 rounded-2xl flex items-center justify-center text-white mx-auto shadow-lg shadow-teal-600/30 mb-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Welcome to MediSync</h2>
        <p class="text-sm text-slate-500 mt-1">Sign in to your patient, doctor, or staff portal</p>
    </div>

    <!-- Quick Demo Logins Card -->
    <div class="mb-6 p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-md border border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-400">⚡ Instant 1-Click Demo Login</span>
            <span class="text-[10px] text-slate-400">Pre-seeded accounts</span>
        </div>
        <p class="text-xs text-slate-300 mb-3">Click any profile below to authenticate immediately without typing credentials:</p>
        <div class="grid grid-cols-3 gap-2 text-xs">
            <a href="{{ route('quick.login', 'admin') }}" class="p-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-center border border-slate-700 hover:border-amber-400 transition group">
                <div class="font-bold text-amber-300 group-hover:scale-105 transition transform">👑 Admin</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Clinic Staff</div>
            </a>
            <a href="{{ route('quick.login', 'doctor') }}" class="p-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-center border border-slate-700 hover:border-cyan-400 transition group">
                <div class="font-bold text-cyan-300 group-hover:scale-105 transition transform">🩺 Doctor</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Dr. Sarah</div>
            </a>
            <a href="{{ route('quick.login', 'patient') }}" class="p-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-center border border-slate-700 hover:border-emerald-400 transition group">
                <div class="font-bold text-emerald-300 group-hover:scale-105 transition transform">👤 Patient</div>
                <div class="text-[10px] text-slate-400 mt-0.5">John Doe</div>
            </a>
        </div>
    </div>

    <!-- Standard Login Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm transition outline-none"
                       placeholder="you@example.com">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                </div>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm transition outline-none"
                       placeholder="••••••••">
                <p class="text-[11px] text-slate-400 mt-1">Default password for demo accounts is: <span class="font-mono font-semibold text-slate-600">password</span></p>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                    <span class="ml-2 text-xs text-slate-600 font-medium">Remember me</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-[0.99] text-white font-bold text-sm shadow-md shadow-teal-600/30 transition transform">
                Sign In to Account
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                New patient at MediSync?
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700 ml-1">
                    Create patient account &rarr;
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
