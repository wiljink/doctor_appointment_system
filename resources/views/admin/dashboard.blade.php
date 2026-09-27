@extends('layouts.app')

@section('title', 'Admin Clinic Management Dashboard')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                👑 Clinic Administration & Operations
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Executive Dashboard</h1>
            <p class="text-sm text-slate-500">Real-time clinic throughput, provider schedules, patient queues, and financials.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                All Appointments
            </a>
            <a href="{{ route('admin.doctors') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition">
                Manage Doctors
            </a>
        </div>
    </div>

    <!-- Main Operational Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Today's Visits</span>
            <div class="text-3xl font-black text-teal-600 mt-1">{{ $stats['today_appointments'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['month_appointments'] }} this month</div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Total Consultations</span>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ $stats['total_appointments'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['completed_appointments'] }} successfully completed</div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Clinic Revenue</span>
            <div class="text-3xl font-black text-emerald-600 mt-1">${{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Paid consultation fees</div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Active Roster</span>
            <div class="text-3xl font-black text-blue-600 mt-1">{{ $stats['total_doctors'] }} Doctors</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['total_patients'] }} registered patients</div>
        </div>
    </div>

    <!-- Appointment Status Distribution Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-amber-700 tracking-wider block">Pending</span>
                <span class="text-xl font-black">{{ $stats['pending_appointments'] }}</span>
            </div>
            <span class="text-xl">⏳</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider block">Confirmed</span>
                <span class="text-xl font-black">{{ $stats['confirmed_appointments'] }}</span>
            </div>
            <span class="text-xl">✓</span>
        </div>

        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-blue-700 tracking-wider block">Completed</span>
                <span class="text-xl font-black">{{ $stats['completed_appointments'] }}</span>
            </div>
            <span class="text-xl">🩺</span>
        </div>

        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-rose-700 tracking-wider block">Cancelled</span>
                <span class="text-xl font-black">{{ $stats['cancelled_appointments'] }}</span>
            </div>
            <span class="text-xl">✕</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Today's Clinical Schedule -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Today's Clinic Roster</h3>
                <span class="text-xs text-slate-400 font-mono">{{ date('M d, Y') }}</span>
            </div>

            @if($todaySchedule->count() > 0)
                <div class="space-y-3">
                    @foreach($todaySchedule as $sch)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-3">
                                <div class="font-mono font-bold text-teal-800 text-xs px-2 py-1 rounded-lg bg-teal-50 border border-teal-200 shrink-0">
                                    {{ \Carbon\Carbon::parse($sch->appointment_time)->format('g:i A') }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $sch->patient->name }}</div>
                                    <div class="text-[11px] text-slate-500">with {{ $sch->doctor->user->name }} ({{ $sch->doctor->room_number ?? 'Room 1' }})</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $sch->getStatusBadgeClass() }}">
                                {{ ucfirst($sch->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center">No appointments scheduled for today.</p>
            @endif
        </div>

        <!-- Right: Recent Bookings & Quick Actions -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Recent Appointments</h3>
                <a href="{{ route('admin.appointments.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">
                    View All ({{ $stats['total_appointments'] }}) &rarr;
                </a>
            </div>

            @if($recentAppointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                                <th class="pb-2">Ref</th>
                                <th class="pb-2">Patient</th>
                                <th class="pb-2">Doctor</th>
                                <th class="pb-2">Date</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentAppointments as $recent)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 font-mono font-bold text-slate-800">
                                        {{ $recent->appointment_number }}
                                    </td>
                                    <td class="py-2.5 font-semibold text-slate-900">
                                        {{ $recent->patient->name }}
                                    </td>
                                    <td class="py-2.5 text-slate-600">
                                        {{ $recent->doctor->user->name }}
                                    </td>
                                    <td class="py-2.5 text-slate-600">
                                        {{ $recent->appointment_date->format('M d') }} {{ \Carbon\Carbon::parse($recent->appointment_time)->format('g:i A') }}
                                    </td>
                                    <td class="py-2.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $recent->getStatusBadgeClass() }}">
                                            {{ ucfirst($recent->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center">No appointments booked yet.</p>
            @endif
        </div>

    </div>
</div>
@endsection
