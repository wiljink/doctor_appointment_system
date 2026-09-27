@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-1">
                Patient Portal
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Welcome, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-slate-500">Manage your clinic bookings, consultation records, and prescription passes.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('patient.records.index') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
                📋 Medical History
            </a>
            <a href="{{ route('booking.index') }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                + Book New Appointment
            </a>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Upcoming Visits</span>
            <div class="text-3xl font-black text-teal-600 mt-1">{{ $stats['upcoming'] }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Total Bookings</span>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Completed</span>
            <div class="text-3xl font-black text-emerald-600 mt-1">{{ $stats['completed'] }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Prescriptions</span>
            <div class="text-3xl font-black text-blue-600 mt-1">{{ $stats['prescriptions'] }}</div>
        </div>
    </div>

    <!-- Main Content: Upcoming & Past Appointments -->
    <div class="space-y-8">
        
        <!-- Section 1: Upcoming Appointments -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Upcoming Consultations</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Visits scheduled for today and future dates</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700">
                    {{ $upcomingAppointments->count() }} Scheduled
                </span>
            </div>

            @if($upcomingAppointments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($upcomingAppointments as $appt)
                        <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition flex flex-col justify-between"
                             x-data="{ showCancelModal: false }">
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <span class="font-mono text-xs font-bold text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                        {{ $appt->appointment_number }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $appt->getStatusBadgeClass() }}">
                                        ● {{ ucfirst($appt->status) }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-11 h-11 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                        {{ substr($appt->doctor->user->name, 4, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm leading-tight">{{ $appt->doctor->user->name }}</div>
                                        <div class="text-xs text-teal-700 font-semibold">{{ $appt->doctor->specialization }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $appt->department->name }} Dept • {{ $appt->doctor->room_number ?? 'Suite A' }}</div>
                                    </div>
                                </div>

                                <div class="p-3 bg-white rounded-xl border border-slate-200/80 text-xs mb-3 space-y-1">
                                    <div class="flex items-center text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="font-bold">{{ $appt->appointment_date->format('l, F j, Y') }}</span>
                                    </div>
                                    <div class="flex items-center text-teal-700">
                                        <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="font-semibold">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($appt->end_time)->format('g:i A') }}</span>
                                    </div>
                                </div>

                                <div class="text-[11px] text-slate-500 mb-4 line-clamp-2">
                                    <strong class="text-slate-700">Reason:</strong> {{ $appt->reason }}
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between gap-2">
                                <a href="{{ route('booking.confirmation', $appt) }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                                    View Pass / Ticket
                                </a>
                                @if($appt->canBeCancelled())
                                    <button @click="showCancelModal = true" class="py-2 px-3 rounded-xl bg-white hover:bg-rose-50 text-rose-600 font-bold text-xs border border-rose-200 transition">
                                        Cancel
                                    </button>
                                @endif
                            </div>

                            <!-- Cancel Confirmation Modal -->
                            <div x-show="showCancelModal" x-cloak
                                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200" @click.away="showCancelModal = false">
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">Cancel Appointment?</h3>
                                    <p class="text-xs text-slate-500 mb-4">
                                        Are you sure you want to cancel your consultation with {{ $appt->doctor->user->name }} on {{ $appt->appointment_date->format('M d, Y') }}?
                                    </p>

                                    <form method="POST" action="{{ route('patient.appointments.cancel', $appt) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Reason for cancellation *</label>
                                            <textarea name="cancellation_reason" rows="2" required placeholder="e.g. Schedule conflict, feeling better, rescheduling..."
                                                      class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none"></textarea>
                                        </div>

                                        <div class="flex items-center space-x-2 pt-2">
                                            <button type="button" @click="showCancelModal = false" class="flex-1 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                                Keep Appointment
                                            </button>
                                            <button type="submit" class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                                                Confirm Cancellation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-sm font-semibold text-slate-600">You have no upcoming consultations scheduled.</p>
                    <a href="{{ route('booking.index') }}" class="inline-block mt-3 px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-xs shadow-sm hover:bg-teal-700 transition">
                        Find a Doctor & Book Now
                    </a>
                </div>
            @endif
        </div>

        <!-- Section 2: Past Appointments & Medical History -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Appointment History & Reports</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Past consultations and electronic clinical prescriptions</p>
                </div>
                <a href="{{ route('patient.records.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">
                    All Medical Records &rarr;
                </a>
            </div>

            @if($pastAppointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                                <th class="pb-3">Reference</th>
                                <th class="pb-3">Doctor & Specialty</th>
                                <th class="pb-3">Date & Time</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pastAppointments as $appt)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 font-mono font-bold text-slate-800">
                                        {{ $appt->appointment_number }}
                                    </td>
                                    <td class="py-3.5">
                                        <div class="font-bold text-slate-900">{{ $appt->doctor->user->name }}</div>
                                        <div class="text-[11px] text-teal-700">{{ $appt->department->name }}</div>
                                    </td>
                                    <td class="py-3.5">
                                        <div class="font-semibold text-slate-800">{{ $appt->appointment_date->format('M d, Y') }}</div>
                                        <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</div>
                                    </td>
                                    <td class="py-3.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $appt->getStatusBadgeClass() }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-right space-x-1">
                                        @if($appt->medicalRecord)
                                            <a href="{{ route('patient.records.prescription', $appt->medicalRecord) }}" class="inline-block px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 font-bold text-[11px] border border-teal-200">
                                                📄 Prescription
                                            </a>
                                        @endif
                                        <a href="{{ route('patient.appointments.show', $appt) }}" class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold text-[11px]">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $pastAppointments->links() }}
                </div>
            @else
                <div class="text-center py-8 text-xs text-slate-400">
                    No past consultation records found.
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
