@extends('layouts.app')

@section('title', 'Doctor Appointments Management')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-200 mb-1">
                Doctor Portal
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Appointments Roster</h1>
            <p class="text-sm text-slate-500">Filter and manage all patient bookings assigned to Dr. {{ $doctor->user->name }}.</p>
        </div>

        <a href="{{ route('doctor.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
            &larr; Today's Dashboard
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200 mb-6">
        <form action="{{ route('doctor.appointments') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Filter by Status</label>
                <select name="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Confirmation</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="sm:col-span-5">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Filter by Date</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none bg-white">
            </div>

            <div class="sm:col-span-2 flex items-end space-x-2">
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                    Filter
                </button>
                @if($status || $date)
                    <a href="{{ route('doctor.appointments') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold flex items-center justify-center transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        @if($appointments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                            <th class="pb-3">Reference</th>
                            <th class="pb-3">Patient</th>
                            <th class="pb-3">Date & Slot</th>
                            <th class="pb-3">Reason</th>
                            <th class="pb-3">Fee / Payment</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($appointments as $appt)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 font-mono font-bold text-slate-800">
                                    {{ $appt->appointment_number }}
                                </td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900">{{ $appt->patient->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $appt->patient->phone ?? 'No phone' }}</div>
                                </td>
                                <td class="py-3.5">
                                    <div class="font-semibold text-slate-800">{{ $appt->appointment_date->format('M d, Y') }}</div>
                                    <div class="text-[11px] text-teal-700 font-mono">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</div>
                                </td>
                                <td class="py-3.5 max-w-xs truncate text-slate-600">
                                    {{ $appt->reason }}
                                </td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900">${{ number_format($appt->fee, 2) }}</div>
                                    <span class="inline-block text-[10px] uppercase font-bold {{ $appt->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $appt->payment_status }}
                                    </span>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $appt->getStatusBadgeClass() }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('doctor.consultation.show', $appt) }}" class="inline-block px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                                        {{ $appt->status === 'completed' ? 'View Record' : 'Consultation' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="text-center py-12 text-slate-400 text-xs">
                No appointments matched the specified filter criteria.
            </div>
        @endif
    </div>

</div>
@endsection
