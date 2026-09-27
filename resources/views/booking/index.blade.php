@extends('layouts.app')

@section('title', 'Book Doctor Appointment')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
     x-data="bookingEngine({
        doctorId: '{{ $selectedDoctor ? $selectedDoctor->id : (old('doctor_id') ?? '') }}',
        date: '{{ old('appointment_date', date('Y-m-d')) }}',
        selectedSlot: '{{ old('appointment_time', '') }}',
        doctors: {{ Js::from($doctors) }}
     })"
     x-init="init()">
    
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Schedule an Appointment</h1>
        <p class="text-sm text-slate-500 mt-1">Select your physician, pick a convenient date, and reserve your consultation slot.</p>
    </div>

    @guest
        <div class="mb-6 p-4 rounded-2xl bg-teal-50 border border-teal-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-teal-900">
            <div class="flex items-center space-x-2 text-sm">
                <span class="text-lg">ℹ️</span>
                <span>You can choose your slot now. You will be prompted to sign in or register before final submission, or you can 
                    <a href="{{ route('login') }}" class="font-bold underline text-teal-800 hover:text-teal-950">sign in here</a>.
                </span>
            </div>
            <div class="flex space-x-2 shrink-0">
                <a href="{{ route('quick.login', 'patient') }}" class="px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs transition">
                    ⚡ 1-Click Patient Login
                </a>
            </div>
        </div>
    @endguest

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Booking Form -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <form method="POST" action="{{ route('booking.store') }}" class="space-y-6">
                @csrf

                <!-- Step 1: Select Doctor & Department -->
                <div>
                    <div class="flex items-center space-x-2 text-sm font-bold uppercase tracking-wider text-teal-700 mb-3">
                        <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">1</span>
                        <span>Select Specialty & Physician</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Doctor Specialty</label>
                            <select x-model="selectedDepartmentId" @change="onDepartmentChange()"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 bg-white outline-none">
                                <option value="">All Specialties</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $selectedDeptId == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Doctor *</label>
                            <select name="doctor_id" x-model="doctorId" @change="fetchSlots()" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 bg-white outline-none">
                                <option value="">-- Choose a Doctor --</option>
                                <template x-for="doc in filteredDoctors" :key="doc.id">
                                    <option :value="doc.id" x-text="`${doc.user.name} - ${doc.specialization} ($${doc.consultation_fee})`"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Date Selection -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center space-x-2 text-sm font-bold uppercase tracking-wider text-teal-700 mb-3">
                        <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">2</span>
                        <span>Select Appointment Date</span>
                    </div>

                    <div class="max-w-xs">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Consultation Date *</label>
                        <input type="date" name="appointment_date" x-model="date" @change="fetchSlots()" min="{{ date('Y-m-d') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 bg-white outline-none">
                    </div>
                </div>

                <!-- Step 3: Interactive Time Slots -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2 text-sm font-bold uppercase tracking-wider text-teal-700">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">3</span>
                            <span>Choose Available Time Slot</span>
                        </div>
                        <span class="text-xs text-slate-400 font-medium" x-show="loadingSlots">Fetching available slots...</span>
                    </div>

                    <!-- Hidden input to store chosen slot -->
                    <input type="hidden" name="appointment_time" x-model="selectedSlot" required>

                    <!-- Loading State -->
                    <div x-show="loadingSlots" class="py-8 text-center text-slate-400 text-xs">
                        <svg class="animate-spin h-6 w-6 text-teal-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading doctor's availability...
                    </div>

                    <!-- Message when doctor is not selected -->
                    <div x-show="!doctorId && !loadingSlots" class="p-6 bg-slate-50 rounded-2xl text-center text-xs text-slate-500">
                        Please pick a doctor and date above to load live consultation openings.
                    </div>

                    <!-- Error or Day-off message from service -->
                    <div x-show="doctorId && slotMessage && !loadingSlots" class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
                        <span x-text="slotMessage"></span>
                    </div>

                    <!-- Slots Grid -->
                    <div x-show="doctorId && slots.length > 0 && !loadingSlots" class="space-y-3">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pb-1">
                            <span>Available slots for <span class="font-bold text-slate-700" x-text="formatDate(date)"></span>:</span>
                            <div class="flex items-center space-x-3">
                                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-teal-500 mr-1"></span> Available</span>
                                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-slate-300 mr-1"></span> Booked / Past</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <template x-for="slot in slots" :key="slot.start_time">
                                <button type="button"
                                        @click="if (slot.is_available) { selectedSlot = slot.start_time; }"
                                        :disabled="!slot.is_available"
                                        :class="{
                                            'bg-teal-600 text-white font-black shadow-md shadow-teal-600/30 scale-102 ring-2 ring-teal-500': selectedSlot === slot.start_time,
                                            'bg-teal-50 hover:bg-teal-100 text-teal-900 border border-teal-200 font-semibold cursor-pointer': slot.is_available && selectedSlot !== slot.start_time,
                                            'bg-slate-100 text-slate-400 cursor-not-allowed opacity-60 line-through': !slot.is_available
                                        }"
                                        class="py-2.5 px-3 rounded-xl text-xs text-center transition flex flex-col items-center justify-center">
                                    <span class="font-mono text-xs" x-text="slot.start_time"></span>
                                    <span class="text-[10px] mt-0.5" x-text="slot.is_booked ? 'Booked' : (slot.is_past ? 'Expired' : 'Available')"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Symptoms & Consultation Reason -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center space-x-2 text-sm font-bold uppercase tracking-wider text-teal-700 mb-3">
                        <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">4</span>
                        <span>Reason for Consultation / Symptoms</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Chief Complaint & Symptoms *</label>
                        <textarea name="reason" rows="3" required
                                  placeholder="Please briefly explain your symptoms, medical concerns, or whether this is a routine wellness follow-up..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('reason') }}</textarea>
                    </div>
                </div>

                <!-- Step 5: Payment Preference -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center space-x-2 text-sm font-bold uppercase tracking-wider text-teal-700 mb-3">
                        <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">5</span>
                        <span>Payment Method</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-300 hover:border-teal-500 cursor-pointer text-xs font-semibold text-slate-700 bg-white">
                            <input type="radio" name="payment_method" value="cash" checked class="text-teal-600 focus:ring-teal-500">
                            <span class="ml-2">💵 Pay Cash at Reception</span>
                        </label>
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-300 hover:border-teal-500 cursor-pointer text-xs font-semibold text-slate-700 bg-white">
                            <input type="radio" name="payment_method" value="card" class="text-teal-600 focus:ring-teal-500">
                            <span class="ml-2">💳 Credit / Debit Card</span>
                        </label>
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-300 hover:border-teal-500 cursor-pointer text-xs font-semibold text-slate-700 bg-white">
                            <input type="radio" name="payment_method" value="online" class="text-teal-600 focus:ring-teal-500">
                            <span class="ml-2">🌐 Online Banking</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit"
                            :disabled="!doctorId || !selectedSlot"
                            :class="(!doctorId || !selectedSlot) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-teal-700 active:scale-95 shadow-lg shadow-teal-600/30'"
                            class="w-full py-4 px-6 rounded-2xl bg-teal-600 text-white font-extrabold text-base transition transform">
                        Confirm Appointment Booking &rarr;
                    </button>
                    <p class="text-[11px] text-center text-slate-400 mt-2">
                        By confirming, your appointment slot will be reserved instantly in our clinic database.
                    </p>
                </div>
            </form>
        </div>

        <!-- Right: Live Booking Summary Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100">
                    Booking Summary
                </h3>

                <div class="space-y-4 text-xs">
                    <!-- Selected Doctor Profile -->
                    <div>
                        <span class="text-slate-400 block font-medium">Selected Physician:</span>
                        <template x-if="currentDoctor">
                            <div class="mt-1">
                                <div class="font-extrabold text-slate-900 text-sm" x-text="currentDoctor.user.name"></div>
                                <div class="text-teal-700 font-semibold" x-text="currentDoctor.specialization"></div>
                                <div class="text-[11px] text-slate-500" x-text="currentDoctor.department.name"></div>
                            </div>
                        </template>
                        <template x-if="!currentDoctor">
                            <div class="text-slate-400 italic">No doctor chosen yet</div>
                        </template>
                    </div>

                    <!-- Date & Time -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 block font-medium">Scheduled Date:</span>
                        <div class="font-bold text-slate-800" x-text="date ? formatDate(date) : 'Not selected'"></div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 block font-medium">Consultation Slot:</span>
                        <div class="font-bold text-teal-700 text-sm" x-text="selectedSlot ? selectedSlot + ' (' + (currentDoctor ? currentDoctor.schedules[0]?.slot_duration_minutes || 30 : 30) + ' mins)' : 'Choose a slot on the left'"></div>
                    </div>

                    <!-- Room / Location -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 block font-medium">Clinic Suite:</span>
                        <div class="font-bold text-slate-800" x-text="currentDoctor?.room_number || 'Consultation Wing, Springfield Clinic'"></div>
                    </div>

                    <!-- Fee -->
                    <div class="pt-3 border-t border-slate-100 bg-slate-50 -mx-6 -mb-6 p-6 rounded-b-3xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600">Consultation Fee:</span>
                            <span class="text-xl font-black text-slate-900" x-text="currentDoctor ? '$' + Number(currentDoctor.consultation_fee).toFixed(2) : '$0.00'"></span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Official clinic receipt issued upon arrival
                        </div>
                    </div>
                </div>
            </div>

            <!-- Need Help Box -->
            <div class="p-5 rounded-2xl bg-teal-50 border border-teal-200 text-teal-900 text-xs space-y-2">
                <div class="font-bold flex items-center">
                    <span class="text-sm mr-1.5">📞</span> Need Urgent or Assistance?
                </div>
                <p class="text-[11px] leading-relaxed text-teal-800">
                    For emergency appointments or questions regarding specialized diagnostic preparation, call clinic reception directly at <strong>(555) 019-CARE</strong>.
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function bookingEngine(config) {
    return {
        doctorId: config.doctorId || '',
        selectedDepartmentId: '{{ $selectedDeptId ?? '' }}',
        date: config.date || '{{ date("Y-m-d") }}',
        selectedSlot: config.selectedSlot || '',
        allDoctors: config.doctors || [],
        filteredDoctors: [],
        slots: [],
        slotMessage: null,
        loadingSlots: false,

        init() {
            this.filterDoctors();
            if (this.doctorId) {
                this.fetchSlots();
            }
        },

        get currentDoctor() {
            return this.allDoctors.find(d => String(d.id) === String(this.doctorId));
        },

        onDepartmentChange() {
            this.filterDoctors();
            if (!this.filteredDoctors.some(d => String(d.id) === String(this.doctorId))) {
                this.doctorId = '';
                this.slots = [];
                this.selectedSlot = '';
            }
        },

        filterDoctors() {
            if (!this.selectedDepartmentId) {
                this.filteredDoctors = this.allDoctors;
            } else {
                this.filteredDoctors = this.allDoctors.filter(d => String(d.department_id) === String(this.selectedDepartmentId));
            }
        },

        async fetchSlots() {
            if (!this.doctorId || !this.date) {
                this.slots = [];
                return;
            }

            this.loadingSlots = true;
            this.slotMessage = null;

            try {
                const response = await fetch(`/api/booking/slots?doctor_id=${this.doctorId}&date=${this.date}`);
                const data = await response.json();

                this.slots = data.slots || [];
                this.slotMessage = data.message || null;

                // If currently selected slot is not in the newly retrieved available slots, reset it
                const valid = this.slots.some(s => s.start_time === this.selectedSlot && s.is_available);
                if (!valid) {
                    this.selectedSlot = '';
                }
            } catch (err) {
                console.error(err);
                this.slotMessage = 'Could not load slots at this moment. Please try again.';
            } finally {
                this.loadingSlots = false;
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const [y, m, d] = dateStr.split('-');
            const dateObj = new Date(y, m - 1, d);
            return dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
        }
    };
}
</script>
@endpush
@endsection
