@extends('layouts.app')

@section('title', 'Admin - Master Appointments Registry')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                Admin Panel
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Master Appointments Registry</h1>
            <p class="text-sm text-slate-500">Search, filter, review, and manage status for all clinic appointments.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
            &larr; Admin Dashboard
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-3xl shadow-xs border border-slate-200 mb-6">
        <form action="{{ route('admin.appointments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <div class="sm:col-span-3">
                <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Search Patient / Ref</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Patient name, phone, or ref..."
                       class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Doctor</label>
                <select name="doctor_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>
                            {{ $doc->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="no_show" {{ $status === 'no_show' ? 'selected' : '' }}>No Show</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none bg-white">
            </div>

            <div class="sm:col-span-2 flex items-end space-x-2">
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                    Filter
                </button>
                @if($search || $doctorId || $deptId || $status || $date)
                    <a href="{{ route('admin.appointments.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold flex items-center justify-center transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Master Appointments Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        @if($appointments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                            <th class="pb-3">Reference</th>
                            <th class="pb-3">Patient</th>
                            <th class="pb-3">Attending Doctor</th>
                            <th class="pb-3">Schedule Date & Slot</th>
                            <th class="pb-3">Fee / Payment</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($appointments as $appt)
                            <tr class="hover:bg-slate-50/50 transition" x-data="{ openStatusModal: false }">
                                <td class="py-3.5 font-mono font-bold text-slate-800">
                                    {{ $appt->appointment_number }}
                                </td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900">{{ $appt->patient->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $appt->patient->phone ?? 'No phone' }}</div>
                                </td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900">{{ $appt->doctor->user->name }}</div>
                                    <div class="text-[11px] text-teal-700">{{ $appt->department->name }}</div>
                                </td>
                                <td class="py-3.5">
                                    <div class="font-semibold text-slate-800">{{ $appt->appointment_date->format('M d, Y') }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</div>
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
                                    <button @click="openStatusModal = true" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition">
                                        Update Status
                                    </button>
                                    <a href="{{ route('booking.confirmation', $appt) }}" class="inline-block px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 font-bold text-[11px] transition">
                                        Pass
                                    </a>

                                    <!-- Status Update Modal -->
                                    <div x-show="openStatusModal" x-cloak
                                         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 text-left" @click.away="openStatusModal = false">
                                            <h3 class="text-base font-bold text-slate-900 mb-1">
                                                Update Appointment #{{ $appt->appointment_number }}
                                            </h3>
                                            <p class="text-xs text-slate-500 mb-4">Patient: {{ $appt->patient->name }} with {{ $appt->doctor->user->name }}</p>

                                            <form method="POST" action="{{ route('admin.appointments.status', $appt) }}" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Appointment Status</label>
                                                    <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs bg-white outline-none">
                                                        <option value="pending" {{ $appt->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="confirmed" {{ $appt->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                        <option value="completed" {{ $appt->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ $appt->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        <option value="no_show" {{ $appt->status === 'no_show' ? 'selected' : '' }}>No Show</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Status</label>
                                                    <select name="payment_status" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs bg-white outline-none">
                                                        <option value="unpaid" {{ $appt->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                        <option value="paid" {{ $appt->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                        <option value="refunded" {{ $appt->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Admin Notes / Cancellation Reason</label>
                                                    <textarea name="cancellation_reason" rows="2" placeholder="Optional notes or reason..."
                                                              class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">{{ $appt->cancellation_reason }}</textarea>
                                                </div>

                                                <div class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                                                    <button type="button" @click="openStatusModal = false" class="flex-1 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                                        Close
                                                    </button>
                                                    <button type="submit" class="flex-1 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
                No appointments found matching your filter criteria.
            </div>
        @endif
    </div>

</div>
@endsection
