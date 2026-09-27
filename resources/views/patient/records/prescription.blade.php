@extends('layouts.app')

@section('title', 'Prescription Slip - ' . $record->appointment->appointment_number)

@section('content')
<div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6 flex items-center justify-between no-print">
        <a href="{{ route('patient.records.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">
            &larr; Back to Medical Records
        </a>

        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Prescription (Rx)
        </button>
    </div>

    <!-- Official Rx Letterhead Slip -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0">
        
        <!-- Clinic Letterhead Header -->
        <div class="border-b-2 border-slate-900 pb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-lg bg-teal-600 text-white font-black flex items-center justify-center text-lg">+</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight">MediSync Medical Clinic</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">100 Medical Center Parkway, Suite 400 • Springfield District</p>
                <p class="text-xs text-slate-500">Phone: (555) 019-CARE • Web: www.medisyncclinic.test</p>
            </div>

            <div class="text-left sm:text-right">
                <div class="text-base font-extrabold text-slate-900">{{ $record->doctor->user->name }}</div>
                <div class="text-xs font-semibold text-teal-700">{{ $record->doctor->specialization }}</div>
                <div class="text-[11px] text-slate-500">{{ $record->doctor->qualifications }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Dept: {{ $record->doctor->department->name }} • {{ $record->doctor->room_number }}</div>
            </div>
        </div>

        <!-- Patient Demographics & Vitals Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4 border-b border-slate-200 text-xs bg-slate-50/70 p-4 rounded-xl mt-4">
            <div>
                <span class="text-slate-400 block font-medium">Patient Name:</span>
                <span class="font-bold text-slate-900">{{ $record->patient->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Gender / Age:</span>
                <span class="font-bold text-slate-900">
                    {{ ucfirst($record->patient->gender ?? 'N/A') }}
                    @if($record->patient->date_of_birth)
                        ({{ \Carbon\Carbon::parse($record->patient->date_of_birth)->age }} yrs)
                    @endif
                </span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Date & Ref:</span>
                <span class="font-bold text-slate-900">{{ $record->created_at->format('M d, Y') }}</span>
                <span class="block text-[10px] text-slate-500 font-mono">{{ $record->appointment->appointment_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Blood Pressure:</span>
                <span class="font-bold text-slate-900">{{ $record->blood_pressure ?: 'Recorded normal' }}</span>
            </div>
        </div>

        <!-- Clinical Diagnosis -->
        <div class="my-6">
            <span class="text-[11px] uppercase font-bold tracking-wider text-slate-400 block mb-1">Clinical Diagnosis</span>
            <div class="text-base font-extrabold text-slate-900 bg-teal-50/40 p-3 rounded-xl border border-teal-100">
                {{ $record->diagnosis }}
            </div>
        </div>

        <!-- Rx Prescription Section -->
        <div class="my-6">
            <div class="flex items-center space-x-2 text-2xl font-black text-slate-900 mb-4 font-serif">
                <span>℞</span>
                <span class="text-sm font-sans font-bold uppercase tracking-wider text-slate-500">Prescribed Medications</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b-2 border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="pb-2 w-1/3">Medicine & Dosage</th>
                            <th class="pb-2">Frequency</th>
                            <th class="pb-2">Duration</th>
                            <th class="pb-2">Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($record->prescriptions as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="font-bold text-slate-900 text-sm">{{ $item->medicine_name }}</div>
                                    <div class="text-[11px] text-teal-700 font-semibold">{{ $item->dosage }}</div>
                                </td>
                                <td class="py-3 font-medium text-slate-800">
                                    {{ $item->frequency }}
                                </td>
                                <td class="py-3 font-medium text-slate-800">
                                    {{ $item->duration }}
                                </td>
                                <td class="py-3 text-slate-600 text-[11px]">
                                    {{ $item->instructions ?: 'As directed by physician' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Advice & Follow up -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-200 text-xs">
            <div>
                <span class="font-bold text-slate-700 block mb-1">Physician Advice & Lifestyle Notes:</span>
                <p class="text-slate-600 bg-slate-50 p-3 rounded-xl leading-relaxed">
                    {{ $record->advice ?: 'Maintain adequate hydration, balanced diet, and monitor vital symptoms as instructed.' }}
                </p>
            </div>

            <div>
                @if($record->follow_up_date)
                    <div class="mb-4">
                        <span class="font-bold text-slate-700 block mb-0.5">Recommended Follow-up Visit:</span>
                        <span class="font-extrabold text-teal-700 text-sm">{{ \Carbon\Carbon::parse($record->follow_up_date)->format('l, F j, Y') }}</span>
                    </div>
                @endif

                <div class="pt-8 text-right">
                    <div class="inline-block text-center border-t border-slate-400 pt-2 min-w-[160px]">
                        <span class="font-bold text-slate-800 block text-xs">{{ $record->doctor->user->name }}</span>
                        <span class="text-[10px] text-slate-400">Authorized Medical Examiner</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
