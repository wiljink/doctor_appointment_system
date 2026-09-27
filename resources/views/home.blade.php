@extends('layouts.app')

@section('title', 'Compassionate Healthcare & Specialist Appointments')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-teal-50/70 via-white to-slate-50 py-16 lg:py-24 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-teal-100/80 border border-teal-200 text-teal-800 text-xs font-bold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>ONLINE CLINIC & APPOINTMENT SCHEDULER</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
                    Exceptional Doctors.<br>
                    <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-teal-800 bg-clip-text text-transparent">
                        Instant Appointments.
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Connect with premier board-certified physicians, discover real-time available time slots, book visits seamlessly, and access digital prescriptions anytime.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                    <a href="{{ route('booking.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 active:scale-95 text-white font-bold text-base shadow-lg shadow-teal-600/30 transition transform">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Book Appointment Now
                    </a>
                    <a href="{{ route('doctors.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-base border border-slate-200 shadow-xs hover:border-slate-300 transition">
                        Browse Specialists &rarr;
                    </a>
                </div>

                <!-- Clinic Highlights Metrics -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-200/80 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900">14+</div>
                        <div class="text-xs text-slate-500 font-medium">Expert Doctors</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-teal-600">7</div>
                        <div class="text-xs text-slate-500 font-medium">Specialized Wings</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900">100%</div>
                        <div class="text-xs text-slate-500 font-medium">Verified Care</div>
                    </div>
                </div>
            </div>

            <!-- Quick Booking Card on Hero -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 relative">
                    <div class="absolute -top-3.5 right-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                        ⚡ Quick Slot Search
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-1">Find Available Time Slots</h3>
                    <p class="text-xs text-slate-500 mb-5">Select specialty and doctor to view live consultation openings</p>

                    <form action="{{ route('booking.index') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Specialty Department</label>
                            <select name="department_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 bg-white">
                                <option value="">All Clinical Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->doctors_count }} doctors)</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Direct Doctor Selection</label>
                            <select name="doctor_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 bg-white">
                                <option value="">Any Available Specialist</option>
                                @foreach($featuredDoctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->user->name }} - {{ $doc->specialization }} (${{ number_format($doc->consultation_fee, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>Check Schedule & Slots</span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span class="flex items-center text-emerald-600 font-semibold">
                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Instant Confirmation
                        </span>
                        <span>Zero Booking Fees</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Clinical Departments Section -->
<section class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-teal-600">Specialized Medicine</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-1">Our Departments & Centers</h2>
            </div>
            <p class="text-sm text-slate-500 max-w-md mt-2 md:mt-0">
                From preventive cardiology to pediatrics and sports medicine, access integrated clinic care under one roof.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($departments as $dept)
                <a href="{{ route('doctors.index', ['department' => $dept->slug]) }}"
                   class="group p-6 rounded-2xl bg-slate-50 hover:bg-teal-50/50 border border-slate-200/80 hover:border-teal-300 transition duration-200 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white group-hover:bg-teal-600 text-teal-600 group-hover:text-white border border-slate-200 group-hover:border-teal-600 flex items-center justify-center font-bold text-xl shadow-xs transition mb-4">
                            🩺
                        </div>
                        <h3 class="font-bold text-lg text-slate-900 group-hover:text-teal-700 transition">
                            {{ $dept->name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                            {{ $dept->description }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-600">
                            {{ $dept->doctors_count }} {{ Str::plural('Doctor', $dept->doctors_count) }}
                        </span>
                        <span class="text-teal-600 font-bold group-hover:translate-x-1 transition transform inline-flex items-center">
                            View &rarr;
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Doctors Showcase -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-teal-600">Qualified Specialists</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-1">Featured Clinic Doctors</h2>
            </div>
            <a href="{{ route('doctors.index') }}" class="mt-3 md:mt-0 font-bold text-sm text-teal-600 hover:text-teal-700 flex items-center">
                Explore All Doctors ({{ count($featuredDoctors) }}+) &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredDoctors as $doctor)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Header / Specialty Badge -->
                        <div class="flex items-start justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200/60">
                                {{ $doctor->department->name }}
                            </span>
                            <span class="text-xs font-extrabold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-full">
                                ${{ number_format($doctor->consultation_fee, 2) }}
                            </span>
                        </div>

                        <!-- Doctor Info -->
                        <div class="flex items-center space-x-3.5 mb-3">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-sm">
                                {{ substr($doctor->user->name, 4, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 leading-snug">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="hover:text-teal-600 transition">
                                        {{ $doctor->user->name }}
                                    </a>
                                </h3>
                                <p class="text-xs text-teal-700 font-semibold">{{ $doctor->specialization }}</p>
                                <p class="text-[11px] text-slate-400">{{ $doctor->qualifications }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                            {{ $doctor->bio }}
                        </p>

                        <!-- Room & Experience -->
                        <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-50 p-2.5 rounded-xl text-slate-600 mb-4">
                            <div>
                                <span class="text-slate-400 block">Experience:</span>
                                <span class="font-bold text-slate-800">{{ $doctor->experience_years }} Years</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Clinic Office:</span>
                                <span class="font-bold text-slate-800 truncate block">{{ $doctor->room_number ?? 'Consultation Suite' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center space-x-2">
                        <a href="{{ route('doctors.show', $doctor) }}" class="flex-1 py-2 text-center text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            View Profile
                        </a>
                        <a href="{{ route('booking.index', ['doctor_id' => $doctor->id]) }}" class="flex-1 py-2 text-center text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-xs transition">
                            Book Visit
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold uppercase tracking-wider text-teal-600">Seamless Healthcare Experience</span>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-1 mb-12">How To Book In 3 Easy Steps</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="w-12 h-12 bg-teal-600 text-white font-black text-lg rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-teal-600/20">
                    1
                </div>
                <h3 class="font-bold text-base text-slate-900 mb-2">Select Doctor & Specialty</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Filter our accredited specialists by medical department, reviews, fees, and location.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="w-12 h-12 bg-teal-600 text-white font-black text-lg rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-teal-600/20">
                    2
                </div>
                <h3 class="font-bold text-base text-slate-900 mb-2">Choose Available Time Slot</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Real-time calendar shows accurate free slots tailored to the doctor's weekly shift and breaks.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="w-12 h-12 bg-teal-600 text-white font-black text-lg rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-teal-600/20">
                    3
                </div>
                <h3 class="font-bold text-base text-slate-900 mb-2">Get Instant Pass & Consult</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Receive your confirmed appointment pass with room directions and access Rx records online.
                </p>
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('booking.index') }}" class="inline-flex items-center px-8 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-lg shadow-teal-600/30 transition transform active:scale-95">
                Start Booking Your Visit &rarr;
            </a>
        </div>
    </div>
</section>
@endsection
