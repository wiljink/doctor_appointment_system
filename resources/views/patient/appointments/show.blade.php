@extends('layouts.app')

@section('title', 'Appointment ' . $appointment->appointment_number)

@section('content')
<div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('patient.dashboard') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">
            &larr; Back to Patient Dashboard
        </a>
        <a href="{{ route('booking.confirmation', $appointment) }}" class="inline-flex items-center text-xs font-bold text-slate-700 bg-white border border-slate-300 px-3 py-1.5 rounded-xl shadow-xs hover:bg-slate-50">
            🖨️ View Printable Ticket Pass
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="text-xs font-mono font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-200">
                    {{ $appointment->appointment_number }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 mt-2">Consultation Details</h1>
            </div>

            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $appointment->getStatusBadgeClass() }}">
                    ● {{ ucfirst($appointment->status) }}
                </span>
            </div>
        </div>

        <!-- Schedule & Doctor Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl space-y-2">
                <span class="font-bold text-slate-400 uppercase tracking-wider block">Doctor Information</span>
                <div class="text-base font-bold text-slate-900">{{ $appointment->doctor->user->name }}</div>
                <div class="text-teal-700 font-semibold">{{ $appointment->doctor->specialization }}</div>
                <div class="text-slate-600">Department: {{ $appointment->department->name }}</div>
                <div class="text-slate-600">Location: {{ $appointment->doctor->room_number ?? 'Consultation Suite' }}</div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl space-y-2">
                <span class="font-bold text-slate-400 uppercase tracking-wider block">Schedule & Fee</span>
                <div class="text-base font-bold text-slate-900">{{ $appointment->appointment_date->format('l, F j, Y') }}</div>
                <div class="text-teal-700 font-semibold">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}</div>
                <div class="text-slate-600">Fee: ${{ number_format($appointment->fee, 2) }} ({{ ucfirst($appointment->payment_method ?? 'cash') }})</div>
                @if($appointment->payment_status === 'paid')
                    <div class="pt-1 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                            ✓ Paid Online (Ref: {{ $appointment->transaction_id ?? 'N/A' }})
                        </span>
                        <a href="{{ route('payment.receipt', $appointment) }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 underline">
                            View Receipt
                        </a>
                    </div>
                @else
                    <div class="pt-1 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">
                            Payment Pending
                        </span>
                        <a href="{{ route('payment.checkout', $appointment) }}" class="inline-flex items-center px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-xs shadow-xs transition">
                            Pay Online Now &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chief Complaint -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Chief Complaint & Reason for Visit</h3>
            <p class="text-xs text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                {{ $appointment->reason }}
            </p>
        </div>

        @if($appointment->cancellation_reason)
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                <span class="font-bold">Cancellation Details:</span>
                <p>Cancelled by: <strong class="capitalize">{{ $appointment->cancelled_by ?? 'Clinic' }}</strong></p>
                <p>Reason: {{ $appointment->cancellation_reason }}</p>
            </div>
        @endif

        <!-- Consultation Medical Record if Completed -->
        @if($appointment->medicalRecord)
            <div class="p-6 rounded-2xl bg-teal-50/60 border border-teal-200 text-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-teal-950 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Clinical Consultation Summary
                    </h3>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('patient.records.pdf', $appointment->medicalRecord) }}" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Download PDF</span>
                        </a>
                        <a href="{{ route('patient.records.prescription', $appointment->medicalRecord) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg shadow-xs transition">
                            View Rx Slip &rarr;
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="font-bold text-slate-700 block mb-1">Diagnosis:</span>
                        <div class="p-2.5 bg-white rounded-xl border border-teal-200/60 text-slate-800">
                            {{ $appointment->medicalRecord->diagnosis }}
                        </div>
                    </div>
                    <div>
                        <span class="font-bold text-slate-700 block mb-1">Vital Signs:</span>
                        <div class="p-2.5 bg-white rounded-xl border border-teal-200/60 text-slate-800 space-y-0.5">
                            @if($appointment->medicalRecord->blood_pressure) <div>BP: {{ $appointment->medicalRecord->blood_pressure }}</div> @endif
                            @if($appointment->medicalRecord->heart_rate) <div>Pulse: {{ $appointment->medicalRecord->heart_rate }}</div> @endif
                            @if($appointment->medicalRecord->temperature) <div>Temp: {{ $appointment->medicalRecord->temperature }}</div> @endif
                            @if($appointment->medicalRecord->weight) <div>Weight: {{ $appointment->medicalRecord->weight }}</div> @endif
                        </div>
                    </div>
                </div>

                @if($appointment->medicalRecord->advice)
                    <div>
                        <span class="font-bold text-slate-700 block mb-1">Doctor's Advice:</span>
                        <div class="p-2.5 bg-white rounded-xl border border-teal-200/60 text-slate-800">
                            {{ $appointment->medicalRecord->advice }}
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
