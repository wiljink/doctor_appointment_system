@extends('layouts.app')

@section('title', 'Appointment Confirmed - ' . $appointment->appointment_number)

@section('content')
<div class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Success Banner -->
    <div class="text-center mb-8 no-print">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-emerald-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Appointment Reserved!</h1>
        <p class="text-sm text-slate-500 mt-1">Your consultation has been recorded in the clinic scheduling system.</p>
    </div>

    <!-- Printable Official Appointment Ticket -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden print:border-none print:shadow-none">
        
        <!-- Ticket Header (Brand & Reference) -->
        <div class="bg-gradient-to-r from-teal-700 via-teal-800 to-slate-900 p-6 sm:p-8 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-black">MediSync Clinic</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-md bg-white/20 font-semibold uppercase tracking-wider">Pass</span>
                </div>
                <p class="text-xs text-teal-200 mt-1">100 Medical Center Parkway, Springfield • (555) 019-CARE</p>
            </div>
            
            <div class="text-left sm:text-right">
                <span class="text-[10px] text-teal-300 uppercase font-bold tracking-widest block">Reference No.</span>
                <span class="font-mono text-xl sm:text-2xl font-black text-white tracking-wider">{{ $appointment->appointment_number }}</span>
            </div>
        </div>

        <!-- Ticket Body -->
        <div class="p-6 sm:p-8 space-y-6">
            
            <!-- Status Badge & Date/Time highlight -->
            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-800 block mb-1">Appointment Schedule</span>
                    <div class="text-lg font-black text-slate-900">
                        {{ $appointment->appointment_date->format('l, F j, Y') }}
                    </div>
                    <div class="text-sm font-bold text-teal-700">
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                        -
                        {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
                    </div>
                </div>

                <div class="flex flex-col sm:items-end">
                    <span class="text-[11px] text-slate-500 mb-1">Status:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $appointment->getStatusBadgeClass() }}">
                        ● {{ ucfirst($appointment->status) }}
                    </span>
                </div>
            </div>

            <!-- Doctor & Patient 2-Column Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Attending Physician</span>
                    <div class="text-base font-black text-slate-900">{{ $appointment->doctor->user->name }}</div>
                    <div class="text-xs font-semibold text-teal-700">{{ $appointment->doctor->specialization }}</div>
                    <div class="text-xs text-slate-500">{{ $appointment->department->name }} Department</div>
                    <div class="text-xs text-slate-700 font-medium pt-1">
                        📍 <strong>Clinic Office:</strong> {{ $appointment->doctor->room_number ?? 'Consultation Suite' }}
                    </div>
                </div>

                <div class="space-y-1 sm:border-l sm:border-slate-100 sm:pl-6">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Patient Information</span>
                    <div class="text-base font-black text-slate-900">{{ $appointment->patient->name }}</div>
                    <div class="text-xs text-slate-600">Phone: {{ $appointment->patient->phone ?? 'N/A' }}</div>
                    <div class="text-xs text-slate-600">Email: {{ $appointment->patient->email }}</div>
                    @if($appointment->patient->blood_group)
                        <div class="text-xs text-slate-600">Blood Group: <strong>{{ $appointment->patient->blood_group }}</strong></div>
                    @endif
                </div>
            </div>

            <!-- Reason & Fee Details -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Consultation Reason / Chief Complaint</span>
                    <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        {{ $appointment->reason }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2 text-xs">
                    <div>
                        <span class="text-slate-400 block">Consultation Fee</span>
                        <span class="text-base font-bold text-slate-900">${{ number_format($appointment->fee, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Payment Method</span>
                        <span class="font-bold text-slate-800 capitalize">{{ $appointment->payment_method }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Payment Status</span>
                        <span class="font-bold {{ $appointment->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }} capitalize">
                            {{ $appointment->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Patient Instructions Notice -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                <span class="font-bold text-slate-800 block">Clinic Check-in Instructions:</span>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-600">
                    <li>Please arrive 10-15 minutes prior to your designated slot for registration.</li>
                    <li>Bring a valid government-issued ID card and any relevant prior medical lab results.</li>
                    <li>Present this appointment reference pass on your phone or printed slip at Reception.</li>
                </ul>
            </div>

        </div>

        <!-- Action Bar (Hidden on print) -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 no-print">
            <button onclick="window.print()" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Appointment Pass
            </button>

            <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                @if($appointment->payment_status !== 'paid')
                    <a href="{{ route('payment.checkout', $appointment) }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary-500/20 transition">
                        Pay Fee Online (${{ number_format($appointment->fee, 2) }}) &rarr;
                    </a>
                @else
                    <a href="{{ route('payment.receipt', $appointment) }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                        View Payment Receipt &rarr;
                    </a>
                @endif

                @auth
                    @if(auth()->user()->isPatient())
                        <a href="{{ route('patient.dashboard') }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-sm transition">
                            Patient Portal &rarr;
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.appointments.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-sm transition">
                            Admin Panel &rarr;
                        </a>
                    @endif
                @else
                    <a href="{{ route('home') }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition">
                        Back to Home &rarr;
                    </a>
                @endauth
            </div>
        </div>

    </div>
</div>
@endsection
