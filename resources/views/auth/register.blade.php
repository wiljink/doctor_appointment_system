@extends('layouts.app')

@section('title', 'Patient Registration')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-teal-600 rounded-2xl flex items-center justify-center text-white mx-auto shadow-lg shadow-teal-600/30 mb-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Create Patient Account</h2>
        <p class="text-sm text-slate-500 mt-1">Register to book doctors, review prescriptions, and manage clinic appointments</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
        <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
            @csrf

            <!-- Personal Details -->
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-teal-700 mb-3">1. Personal & Contact Details</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                               placeholder="e.g. John Doe">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                               placeholder="john@example.com">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Phone Number *</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                               placeholder="+1 (555) 000-0000">
                    </div>

                    <div>
                        <label for="gender" class="block text-xs font-bold text-slate-700 mb-1">Gender</label>
                        <select id="gender" name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none bg-white">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-xs font-bold text-slate-700 mb-1">Date of Birth</label>
                        <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none">
                    </div>

                    <div>
                        <label for="blood_group" class="block text-xs font-bold text-slate-700 mb-1">Blood Group</label>
                        <select id="blood_group" name="blood_group" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none bg-white">
                            <option value="">Unknown / Select</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="address" class="block text-xs font-bold text-slate-700 mb-1">Residential Address</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                           placeholder="Street address, City, State">
                </div>

                <div class="mt-4">
                    <label for="emergency_contact" class="block text-xs font-bold text-slate-700 mb-1">Emergency Contact Person & Phone</label>
                    <input id="emergency_contact" type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                           placeholder="e.g. Mary Doe (Spouse) +1 555-999-1234">
                </div>
            </div>

            <!-- Security Credentials -->
            <div class="pt-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-teal-700 mb-3">2. Account Password</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password *</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                               placeholder="Minimum 8 characters">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm Password *</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none"
                               placeholder="Repeat password">
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-[0.99] text-white font-bold text-sm shadow-md shadow-teal-600/30 transition transform">
                    Complete Registration & Access Portal
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Already registered?
                <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700 ml-1">
                    Sign in here &rarr;
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
