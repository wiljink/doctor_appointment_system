@extends('layouts.app')

@section('title', 'Find a Doctor - Specialists Directory')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Title & Filter Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Our Medical Specialists</h1>
                <p class="text-sm text-slate-500 mt-1">Book consultations with certified physicians across clinical specialties</p>
            </div>
            <a href="{{ route('booking.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-sm transition">
                ⚡ Quick Appointment Booking
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="mt-6 bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <form action="{{ route('doctors.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search doctor by name or specialty..."
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                </div>

                <div class="sm:col-span-4">
                    <select name="department" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->slug }}" {{ $departmentSlug === $dept->slug ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex space-x-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition">
                        Filter
                    </button>
                    @if($search || $departmentSlug)
                        <a href="{{ route('doctors.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center justify-center transition">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            <!-- Department Quick Filter Pills -->
            <div class="flex items-center space-x-2 overflow-x-auto pt-3 mt-3 border-t border-slate-100 text-xs">
                <span class="text-slate-400 font-semibold uppercase tracking-wider shrink-0 text-[10px]">Filter by:</span>
                <a href="{{ route('doctors.index') }}" class="px-3 py-1 rounded-full font-medium shrink-0 {{ empty($departmentSlug) ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </a>
                @foreach($departments as $dept)
                    <a href="{{ route('doctors.index', ['department' => $dept->slug]) }}"
                       class="px-3 py-1 rounded-full font-medium shrink-0 {{ $departmentSlug === $dept->slug ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $dept->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Doctors Cards Grid -->
    @if($doctors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($doctors as $doctor)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Header / Specialty Badge & Fee -->
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
                            <img src="{{ $doctor->user->avatar_url }}" alt="{{ $doctor->user->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 shadow-sm shrink-0">
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
                                <span class="text-slate-400 block">Room Number:</span>
                                <span class="font-bold text-slate-800 truncate block">{{ $doctor->room_number ?? 'Consultation Suite' }}</span>
                            </div>
                        </div>

                        <!-- Availability Schedule Days Pills -->
                        <div class="mb-4">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-1">Clinic Days</span>
                            <div class="flex flex-wrap gap-1">
                                @forelse($doctor->schedules->where('is_active', true) as $sch)
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ substr($sch->getDayName(), 0, 3) }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-slate-400">By appointment only</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center space-x-2">
                        <a href="{{ route('doctors.show', $doctor) }}" class="flex-1 py-2 text-center text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            View Full Profile
                        </a>
                        <a href="{{ route('booking.index', ['doctor_id' => $doctor->id]) }}" class="flex-1 py-2 text-center text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-xs transition">
                            Book Visit
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $doctors->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-2xl text-slate-400 mb-3">
                🔍
            </div>
            <h3 class="text-lg font-bold text-slate-800">No doctors match your criteria</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Try clearing your department filter or searching with a different term.</p>
            <a href="{{ route('doctors.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-xs">
                Reset All Filters
            </a>
        </div>
    @endif
</div>
@endsection
