@extends('layouts.app')

@section('title', $doctor->user->name . ' - ' . $doctor->specialization)

@section('content')
<div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6">
        <a href="{{ route('doctors.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 inline-flex items-center">
            &larr; Back to all doctors
        </a>
    </div>

    <!-- Doctor Profile Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-center space-x-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-teal-600 to-emerald-400 text-white font-black text-3xl sm:text-4xl flex items-center justify-center shrink-0 shadow-md shadow-teal-600/20">
                    {{ substr($doctor->user->name, 4, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                            {{ $doctor->department->name }}
                        </span>
                        @if($doctor->is_available)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                ● Accepting Patients
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                Unavailable
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                        {{ $doctor->user->name }}
                    </h1>
                    <p class="text-sm text-teal-700 font-semibold">{{ $doctor->specialization }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $doctor->qualifications }}</p>
                </div>
            </div>

            <div class="w-full sm:w-auto text-left sm:text-right bg-slate-50 sm:bg-transparent p-4 sm:p-0 rounded-2xl">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Consultation Fee</div>
                <div class="text-3xl font-black text-slate-900">${{ number_format($doctor->consultation_fee, 2) }}</div>
                <a href="{{ route('booking.index', ['doctor_id' => $doctor->id]) }}" class="mt-3 inline-flex items-center justify-center px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/30 transition w-full sm:w-auto">
                    Book Appointment Now &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Doctor Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Left: Bio & Qualifications -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Biography & Clinical Focus
                </h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    {{ $doctor->bio ?: 'Dedicated specialist committed to delivering exceptional patient-centered care and evidence-based diagnostic evaluation.' }}
                </p>

                <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-100">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-400 block font-medium">Years in Practice</span>
                        <span class="text-base font-bold text-slate-900">{{ $doctor->experience_years }}+ Years</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-400 block font-medium">Consultation Office</span>
                        <span class="text-base font-bold text-slate-900">{{ $doctor->room_number ?: 'Clinic Suite A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Weekly Schedule Timetable -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Weekly Practice Schedule
                </h2>

                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($doctor->schedules->sortBy('day_of_week') as $schedule)
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="font-bold text-slate-800 w-28">{{ $schedule->getDayName() }}</span>
                            @if($schedule->is_active)
                                <div class="text-slate-600">
                                    <span class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}</span>
                                    to
                                    <span class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</span>
                                    <span class="text-slate-400 ml-2">({{ $schedule->slot_duration_minutes }} min slots)</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700 text-[10px]">Open</span>
                            @else
                                <span class="text-slate-400">Off Duty</span>
                                <span class="px-2 py-0.5 rounded-full font-semibold bg-slate-100 text-slate-500 text-[10px]">Closed</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Clinic Info & Direct Booking Widget -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-teal-700 to-emerald-800 rounded-3xl p-6 text-white shadow-lg">
                <h3 class="font-black text-xl mb-2">Book with Dr. {{ $doctor->user->name }}</h3>
                <p class="text-xs text-teal-100 leading-relaxed mb-6">
                    Select a convenient date and time to reserve your appointment instantly with live slot verification.
                </p>

                <a href="{{ route('booking.index', ['doctor_id' => $doctor->id]) }}"
                   class="w-full py-3.5 px-4 rounded-xl bg-white hover:bg-teal-50 text-teal-900 font-extrabold text-sm text-center block shadow-md transition transform active:scale-95">
                    Proceed to Booking &rarr;
                </a>

                <div class="mt-4 pt-4 border-t border-teal-600/60 text-[11px] text-teal-200 space-y-1">
                    <div>✓ No upfront credit card required</div>
                    <div>✓ Free cancellation up to 2 hours prior</div>
                    <div>✓ Instant digital prescription delivery</div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-xs space-y-3">
                <h4 class="font-bold text-slate-900 text-sm">Clinic Location & Room</h4>
                <p class="text-slate-600 leading-relaxed">
                    MediSync Main Clinic Facility<br>
                    <strong>Room:</strong> {{ $doctor->room_number ?? 'Consultation Wing' }}<br>
                    100 Medical Center Parkway, Springfield
                </p>
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-400">Department Phone:</span><br>
                    <span class="font-bold text-slate-800">{{ $doctor->user->phone ?? '+1 (555) 012-3456' }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
