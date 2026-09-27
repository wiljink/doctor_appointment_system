@extends('layouts.app')

@section('title', 'Payment Receipt #' . $appointment->transaction_id . ' - MediCare Clinic')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <a href="{{ route('patient.appointments.show', $appointment) }}" class="inline-flex items-center text-sm font-semibold text-slate-600 hover:text-primary-600">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Appointment
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Receipt
            </button>
            <a href="{{ route('patient.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition">
                Patient Portal
            </a>
        </div>
    </div>

    <!-- Official Printable Receipt Paper -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden print:border-none print:shadow-none print:p-0">
        <!-- Paid Watermark Stamp -->
        <div class="absolute -right-8 -top-8 w-36 h-36 bg-emerald-50 rounded-full flex items-center justify-center opacity-80 pointer-events-none border-4 border-dashed border-emerald-400 transform rotate-12">
            <span class="text-emerald-700 font-black text-xl tracking-widest uppercase">PAID</span>
        </div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-200 pb-6 mb-8">
            <div>
                <div class="flex items-center space-x-2.5 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-600 to-teal-500 flex items-center justify-center text-white font-black text-lg">
                        M+
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">MediCare Clinic</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    124 Health Sciences Avenue, Medical District<br>
                    support@medicare-clinic.com • +1 (800) 555-0199<br>
                    Clinic Reg: MC-CLINIC-2026-X
                </p>
            </div>
            <div class="mt-4 sm:mt-0 text-left sm:text-right">
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold font-mono uppercase tracking-wider mb-1">
                    Official Receipt
                </span>
                <h2 class="text-lg font-bold text-slate-900 font-mono">{{ $appointment->transaction_id ?? 'TXN-'.$appointment->appointment_number }}</h2>
                <p class="text-xs text-slate-500">Date: {{ $appointment->paid_at ? $appointment->paid_at->format('M d, Y • h:i A') : $appointment->updated_at->format('M d, Y • h:i A') }}</p>
            </div>
        </div>

        <!-- Bill To / Patient Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 rounded-2xl p-5 mb-8 border border-slate-100">
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Billed To (Patient)</h4>
                <p class="text-sm font-bold text-slate-900">{{ $appointment->patient->name }}</p>
                <p class="text-xs text-slate-600">{{ $appointment->patient->email }}</p>
                @if($appointment->patient->phone)
                    <p class="text-xs text-slate-600">{{ $appointment->patient->phone }}</p>
                @endif
                @if($appointment->patient->blood_group)
                    <p class="text-xs text-slate-500 mt-1">Blood Group: <span class="font-bold text-slate-800">{{ $appointment->patient->blood_group }}</span></p>
                @endif
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Consultation Details</h4>
                <p class="text-sm font-bold text-slate-900">Dr. {{ $appointment->doctor->user->name }}</p>
                <p class="text-xs text-primary-600 font-semibold">{{ $appointment->doctor->specialization }} ({{ $appointment->department->name }})</p>
                <p class="text-xs text-slate-600 mt-1">
                    Scheduled: {{ $appointment->appointment_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                </p>
                <p class="text-xs text-slate-500">Ticket Ref: <span class="font-mono font-semibold text-slate-800">#{{ $appointment->appointment_number }}</span></p>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-8">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Item & Description</th>
                        <th class="py-3 px-4 text-center">Room</th>
                        <th class="py-3 px-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900">Doctor Medical Consultation</p>
                            <p class="text-xs text-slate-500">Comprehensive examination with Dr. {{ $appointment->doctor->user->name }}</p>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono text-xs">{{ $appointment->doctor->room_number }}</td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">${{ number_format($appointment->fee, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-700">Digital Health Record & Prescription Archival</p>
                            <p class="text-xs text-slate-400">Electronic portal access and PDF prescription export</p>
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-slate-400">—</td>
                        <td class="py-3 px-4 text-right text-xs text-emerald-600 font-semibold">FREE ($0.00)</td>
                    </tr>
                </tbody>
                <tfoot class="bg-slate-50/80 border-t border-slate-200 font-semibold">
                    <tr>
                        <td colspan="2" class="py-3 px-4 text-slate-600 text-right">Subtotal:</td>
                        <td class="py-3 px-4 text-right text-slate-900 font-bold">${{ number_format($appointment->fee, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="py-3 px-4 text-slate-600 text-right">Tax (0% Medical Exemption):</td>
                        <td class="py-3 px-4 text-right text-slate-900 font-bold">$0.00</td>
                    </tr>
                    <tr class="text-base font-black text-slate-900 border-t border-slate-200">
                        <td colspan="2" class="py-3.5 px-4 text-right">Total Paid:</td>
                        <td class="py-3.5 px-4 text-right text-primary-600">${{ number_format($appointment->fee, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Meta & Terms -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-500 border-t border-slate-200 pt-6">
            <div>
                <p class="font-bold text-slate-700 mb-1">Payment Method</p>
                <p class="capitalize">{{ $appointment->payment_method === 'card' ? 'Credit / Debit Card (Stripe)' : ($appointment->payment_method === 'digital_wallet' ? 'Digital Wallet (Instant Authorization)' : 'Cash at Clinic Desk') }}</p>
                <p class="font-mono text-slate-400 mt-0.5">Reference: {{ $appointment->transaction_id ?? 'N/A' }}</p>
            </div>
            <div class="text-left sm:text-right">
                <p class="font-bold text-slate-700 mb-1">Receipt Verification</p>
                <p>This is a computer-generated receipt valid without physical signature.</p>
                <p class="text-slate-400 mt-0.5">Thank you for choosing MediCare Clinic.</p>
            </div>
        </div>
    </div>
</div>
@endsection
