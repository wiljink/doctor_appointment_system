@extends('layouts.app')

@section('title', 'Doctor Dashboard - Dr. ' . $doctor->user->name)

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Top Header & Doctor Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-200 mb-1">
                🩺 Doctor Portal • {{ $doctor->department->name }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinical Dashboard</h1>
            <p class="text-sm text-slate-500">Welcome, {{ $doctor->user->name }} — Manage today's patient queue and recorded consultations.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('doctor.schedule') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
                ⚙️ Weekly Schedule & Leaves
            </a>
            <a href="{{ route('doctor.appointments') }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                All Appointments &rarr;
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Today's Patients</span>
            <div class="text-3xl font-black text-teal-600 mt-1">{{ $stats['today_total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['today_confirmed'] }} confirmed, {{ $stats['today_pending'] }} pending</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Completed Today</span>
            <div class="text-3xl font-black text-emerald-600 mt-1">{{ $stats['today_completed'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Consultations finished</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">All Completed Visits</span>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ $stats['total_completed'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Lifetime total</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Total Patients</span>
            <div class="text-3xl font-black text-blue-600 mt-1">{{ $stats['total_patients'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Unique individuals</div>
        </div>
    </div>

    <!-- Today's Queue Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 mb-8">
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Today's Patient Queue</h2>
                <p class="text-xs text-slate-500 mt-0.5">Appointments scheduled for today, {{ date('F j, Y') }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700">
                {{ $todayAppointments->count() }} In Queue
            </span>
        </div>

        @if($todayAppointments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                            <th class="pb-3">Slot Time</th>
                            <th class="pb-3">Patient Name</th>
                            <th class="pb-3">Phone & Details</th>
                            <th class="pb-3">Symptoms / Reason</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($todayAppointments as $appt)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 font-mono font-bold text-teal-800 text-sm whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                                </td>
                                <td class="py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $appt->patient->name }}</div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $appt->appointment_number }}</span>
                                </td>
                                <td class="py-4 text-slate-600">
                                    <div>{{ $appt->patient->phone ?? 'No phone' }}</div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ ucfirst($appt->patient->gender ?? '') }}
                                        @if($appt->patient->date_of_birth)
                                            ({{ \Carbon\Carbon::parse($appt->patient->date_of_birth)->age }} yrs)
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 text-slate-700 max-w-xs truncate">
                                    {{ $appt->reason }}
                                </td>
                                <td class="py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $appt->getStatusBadgeClass() }}">
                                        ● {{ ucfirst($appt->status) }}
                                    </span>
                                </td>
                                <td class="py-4 text-right space-x-1 whitespace-nowrap">
                                    @if($appt->status !== 'completed' && $appt->status !== 'cancelled')
                                        <a href="{{ route('doctor.consultation.show', $appt) }}" class="inline-block px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                                            🩺 Start Consultation
                                        </a>

                                        @if($appt->status === 'pending')
                                            <form method="POST" action="{{ route('doctor.appointments.status', $appt) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs border border-emerald-200 transition">
                                                    Confirm
                                                </button>
                                            </form>
                                        @endif
                                    @elseif($appt->status === 'completed')
                                        <a href="{{ route('doctor.consultation.show', $appt) }}" class="inline-block px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                                            View Rx Record
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2 text-xl">
                    ✓
                </div>
                <p class="text-sm font-bold text-slate-700">No appointments scheduled for today.</p>
                <p class="text-xs text-slate-400 mt-1">Review upcoming appointments or manage your weekly availability in schedule settings.</p>
            </div>
        @endif
    </div>

    <!-- Upcoming Days Preview -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Upcoming Consultations This Week</h3>
            <a href="{{ route('doctor.appointments') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">
                View All Appointments &rarr;
            </a>
        </div>

        @if($upcomingAppointments->count() > 0)
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($upcomingAppointments as $upAppt)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            <div>
                                <span class="font-bold text-slate-900">{{ $upAppt->patient->name }}</span>
                                <span class="text-slate-400 ml-2">({{ $upAppt->reason }})</span>
                            </div>
                        </div>
                        <div class="text-slate-600 flex items-center space-x-3">
                            <span class="font-semibold">{{ $upAppt->appointment_date->format('l, M d') }}</span>
                            <span class="font-mono text-teal-700">{{ \Carbon\Carbon::parse($upAppt->appointment_time)->format('g:i A') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $upAppt->getStatusBadgeClass() }}">
                                {{ ucfirst($upAppt->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 py-4 text-center">No upcoming appointments recorded for the next few days.</p>
        @endif
    </div>

</div>
@endsection
