@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 mb-1">
                Health Records
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinical & Prescription History</h1>
            <p class="text-sm text-slate-500">Access your documented medical diagnoses, clinical notes, and physician prescriptions.</p>
        </div>

        <a href="{{ route('booking.index') }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
            + Schedule New Consultation
        </a>
    </div>

    @if($records->count() > 0)
        <div class="space-y-6">
            @foreach($records as $rec)
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white font-bold flex items-center justify-center text-lg shadow-xs">
                                Rx
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">{{ $rec->doctor->user->name }}</h3>
                                <p class="text-xs text-teal-700 font-semibold">{{ $rec->doctor->specialization }} ({{ $rec->doctor->department->name }})</p>
                            </div>
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="text-xs font-bold text-slate-900 block">{{ $rec->created_at->format('F j, Y') }}</span>
                            <span class="font-mono text-[11px] text-slate-400">Ref: {{ $rec->appointment->appointment_number }}</span>
                        </div>
                    </div>

                    <!-- Diagnosis & Vitals Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-4 text-xs">
                        <div class="p-4 bg-slate-50 rounded-2xl md:col-span-2">
                            <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Diagnosis</span>
                            <div class="text-sm font-bold text-slate-900">{{ $rec->diagnosis }}</div>
                            @if($rec->symptoms)
                                <p class="text-slate-600 mt-2"><strong>Reported Symptoms:</strong> {{ $rec->symptoms }}</p>
                            @endif
                            @if($rec->advice)
                                <p class="text-slate-600 mt-2"><strong>Physician Advice:</strong> {{ $rec->advice }}</p>
                            @endif
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Recorded Vitals</span>
                            <ul class="space-y-1 text-slate-700 mt-1">
                                <li><strong>BP:</strong> {{ $rec->blood_pressure ?: 'N/A' }}</li>
                                <li><strong>Pulse:</strong> {{ $rec->heart_rate ?: 'N/A' }}</li>
                                <li><strong>Temperature:</strong> {{ $rec->temperature ?: 'N/A' }}</li>
                                <li><strong>Weight:</strong> {{ $rec->weight ?: 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Prescription Items Summary -->
                    @if($rec->prescriptions->count() > 0)
                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center space-x-2 text-slate-600">
                                <span class="font-bold text-slate-800">Prescription:</span>
                                <span>{{ $rec->prescriptions->count() }} {{ Str::plural('medication', $rec->prescriptions->count()) }} prescribed</span>
                            </div>

                            <a href="{{ route('patient.records.prescription', $rec) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                                📄 View & Print Official Prescription Slip &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="mt-4">
                {{ $records->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-2xl text-slate-400 mb-3">
                📋
            </div>
            <h3 class="text-lg font-bold text-slate-800">No medical records yet</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Once your attending physician completes your consultation, your medical notes and prescriptions will appear here.</p>
        </div>
    @endif
</div>
@endsection
