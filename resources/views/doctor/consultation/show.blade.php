@extends('layouts.app')

@section('title', 'Consultation - Patient ' . $appointment->patient->name)

@section('content')
<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
     x-data="consultationForm({
        medicines: {{ Js::from($appointment->medicalRecord?->prescriptions ?? [
            ['name' => '', 'dosage' => '', 'frequency' => '', 'duration' => '', 'instructions' => '']
        ]) }}
     })">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('doctor.dashboard') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">
                &larr; Back to Queue
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                Patient Consultation Room
            </h1>
            <p class="text-xs text-slate-500 font-mono">
                Appointment #{{ $appointment->appointment_number }} • {{ $appointment->appointment_date->format('l, F j, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $appointment->getStatusBadgeClass() }}">
                ● {{ ucfirst($appointment->status) }}
            </span>
            @if($appointment->medicalRecord)
                <a href="{{ route('doctor.records.pdf', $appointment->medicalRecord) }}" class="px-3 py-1.5 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary-700 font-bold text-xs border border-primary-200 transition flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download PDF</span>
                </a>
                <a href="{{ route('patient.records.prescription', $appointment->medicalRecord) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    🖨️ View Rx Slip
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Patient Profile & Past Medical History -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Patient Demographics Card -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-800 font-black text-xl flex items-center justify-center border border-teal-200">
                        {{ strtoupper(substr($appointment->patient->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $appointment->patient->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $appointment->patient->email }}</p>
                        <p class="text-xs font-semibold text-slate-700">{{ $appointment->patient->phone ?? 'No phone registered' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs pt-3 border-t border-slate-100">
                    <div class="p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Age / Gender</span>
                        <span class="font-bold text-slate-800">
                            {{ ucfirst($appointment->patient->gender ?? 'Unknown') }}
                            @if($appointment->patient->date_of_birth)
                                ({{ \Carbon\Carbon::parse($appointment->patient->date_of_birth)->age }} yrs)
                            @endif
                        </span>
                    </div>

                    <div class="p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Blood Group</span>
                        <span class="font-bold text-rose-600">
                            {{ $appointment->patient->blood_group ?: 'Not set' }}
                        </span>
                    </div>
                </div>

                @if($appointment->patient->emergency_contact)
                    <div class="mt-3 text-xs p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Emergency Contact</span>
                        <span class="font-medium text-slate-800">{{ $appointment->patient->emergency_contact }}</span>
                    </div>
                @endif
            </div>

            <!-- Chief Complaint for Today -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-2">Today's Chief Complaint</span>
                <p class="text-xs text-slate-800 bg-amber-50/70 p-3 rounded-xl border border-amber-200/80 leading-relaxed font-medium">
                    "{{ $appointment->reason }}"
                </p>
            </div>

            <!-- Past Consultation History Timeline -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Prior Clinical Consultations ({{ $pastRecords->count() }})</h3>
                
                @if($pastRecords->count() > 0)
                    <div class="space-y-4 divide-y divide-slate-100 text-xs">
                        @foreach($pastRecords as $past)
                            <div class="pt-3 first:pt-0">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-bold text-slate-800">{{ $past->created_at->format('M d, Y') }}</span>
                                    <span class="text-teal-700 font-semibold">{{ $past->doctor->user->name }}</span>
                                </div>
                                <div class="font-semibold text-slate-900">{{ $past->diagnosis }}</div>
                                @if($past->blood_pressure)
                                    <div class="text-[10px] text-slate-500 mt-0.5">BP: {{ $past->blood_pressure }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">No previous clinical history on file for this patient.</p>
                @endif
            </div>

        </div>

        <!-- Right Column: Doctor Consultation Form -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <form method="POST" action="{{ route('doctor.consultation.store', $appointment) }}" class="space-y-6">
                @csrf

                <!-- Section 1: Vital Signs -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-teal-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        1. Patient Vital Signs (Optional)
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Blood Pressure</label>
                            <input type="text" name="blood_pressure" value="{{ old('blood_pressure', $appointment->medicalRecord?->blood_pressure) }}"
                                   placeholder="e.g. 120/80 mmHg"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Heart / Pulse Rate</label>
                            <input type="text" name="heart_rate" value="{{ old('heart_rate', $appointment->medicalRecord?->heart_rate) }}"
                                   placeholder="e.g. 72 bpm"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Temperature</label>
                            <input type="text" name="temperature" value="{{ old('temperature', $appointment->medicalRecord?->temperature) }}"
                                   placeholder="e.g. 36.8 °C"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Weight</label>
                            <input type="text" name="weight" value="{{ old('weight', $appointment->medicalRecord?->weight) }}"
                                   placeholder="e.g. 72 kg"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Clinical Assessment & Diagnosis -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-teal-700 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        2. Clinical Diagnosis & Findings *
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Primary Clinical Diagnosis *</label>
                        <input type="text" name="diagnosis" required
                               value="{{ old('diagnosis', $appointment->medicalRecord?->diagnosis) }}"
                               placeholder="e.g. Acute Bronchitis / Stage 1 Hypertension / Contact Dermatitis"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Observed Symptoms / Examination Findings</label>
                            <textarea name="symptoms" rows="3"
                                      placeholder="Clinical physical examination findings, symptom onset..."
                                      class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 outline-none">{{ old('symptoms', $appointment->medicalRecord?->symptoms) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Physician Advice & Lifestyle Instructions</label>
                            <textarea name="advice" rows="3"
                                      placeholder="Dietary recommendations, hydration, rest, red flag symptoms to watch for..."
                                      class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 outline-none">{{ old('advice', $appointment->medicalRecord?->advice) }}</textarea>
                        </div>
                    </div>

                    <div class="max-w-xs">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Recommended Follow-up Date (Optional)</label>
                        <input type="date" name="follow_up_date" min="{{ date('Y-m-d') }}"
                               value="{{ old('follow_up_date', $appointment->medicalRecord?->follow_up_date?->format('Y-m-d')) }}"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    </div>
                </div>

                <!-- Section 3: Dynamic Rx Prescription Builder -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-teal-700 flex items-center">
                            <span class="text-base font-serif font-black mr-1.5">℞</span>
                            3. Prescribe Medications
                        </h3>
                        <button type="button" @click="addMedicine()"
                                class="px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs border border-teal-200 transition">
                            + Add Medicine Row
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(med, index) in medicines" :key="index">
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/90 relative">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 text-xs">
                                    <div class="sm:col-span-4">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Medicine Name</label>
                                        <input type="text" :name="`medicines[${index}][name]`" x-model="med.name"
                                               placeholder="e.g. Amoxicillin / Paracetamol"
                                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Dosage</label>
                                        <input type="text" :name="`medicines[${index}][dosage]`" x-model="med.dosage"
                                               placeholder="e.g. 500mg"
                                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs outline-none">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Frequency</label>
                                        <input type="text" :name="`medicines[${index}][frequency]`" x-model="med.frequency"
                                               placeholder="e.g. 3 times daily"
                                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Duration</label>
                                        <input type="text" :name="`medicines[${index}][duration]`" x-model="med.duration"
                                               placeholder="e.g. 7 days"
                                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs outline-none">
                                    </div>
                                    <div class="sm:col-span-1 flex items-end justify-center">
                                        <button type="button" @click="removeMedicine(index)"
                                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-sm flex items-center justify-center transition"
                                                title="Remove Medicine">
                                            ✕
                                        </button>
                                    </div>
                                    <div class="sm:col-span-12 mt-1">
                                        <input type="text" :name="`medicines[${index}][instructions]`" x-model="med.instructions"
                                               placeholder="Instructions (e.g. Take with water after meals)"
                                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs outline-none">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Section 4: Payment Status & Final Action -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Consultation Payment Status</label>
                        <select name="payment_status" class="px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white font-semibold outline-none">
                            <option value="paid" {{ $appointment->payment_status === 'paid' ? 'selected' : '' }}>Paid (${{ number_format($appointment->fee, 2) }})</option>
                            <option value="unpaid" {{ $appointment->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid / Pending Reception</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="py-3 px-6 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-sm shadow-md shadow-teal-600/30 transition transform active:scale-95">
                            ✓ Save Consultation & Complete Visit
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
function consultationForm(config) {
    return {
        medicines: config.medicines && config.medicines.length > 0 ? config.medicines : [
            { name: '', dosage: '', frequency: '', duration: '', instructions: '' }
        ],

        addMedicine() {
            this.medicines.push({
                name: '',
                dosage: '',
                frequency: '',
                duration: '',
                instructions: ''
            });
        },

        removeMedicine(index) {
            if (this.medicines.length > 1) {
                this.medicines.splice(index, 1);
            } else {
                this.medicines = [{ name: '', dosage: '', frequency: '', duration: '', instructions: '' }];
            }
        }
    };
}
</script>
@endpush
@endsection
