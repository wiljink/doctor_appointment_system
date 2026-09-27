@extends('layouts.app')

@section('title', 'Admin - Doctor Management')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ openAddModal: false }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                Admin Panel
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Doctor & Specialist Registry</h1>
            <p class="text-sm text-slate-500">Manage physicians, assign departments, configure consultation fees, and toggle availability.</p>
        </div>

        <div class="flex items-center space-x-2">
            <button @click="openAddModal = true" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                + Register New Doctor
            </button>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
                &larr; Dashboard
            </a>
        </div>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                        <th class="pb-3">Doctor</th>
                        <th class="pb-3">Department</th>
                        <th class="pb-3">Fee</th>
                        <th class="pb-3">Room / Office</th>
                        <th class="pb-3">Appointments</th>
                        <th class="pb-3">Availability</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($doctors as $doctor)
                        <tr class="hover:bg-slate-50/50 transition" x-data="{ openEditModal: false }">
                            <td class="py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                        {{ substr($doctor->user->name, 4, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $doctor->user->name }}</div>
                                        <div class="text-[11px] text-teal-700 font-semibold">{{ $doctor->specialization }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $doctor->user->email }} • {{ $doctor->user->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 font-semibold text-slate-800">
                                {{ $doctor->department->name }}
                            </td>
                            <td class="py-4 font-bold text-slate-900 text-sm">
                                ${{ number_format($doctor->consultation_fee, 2) }}
                            </td>
                            <td class="py-4 text-slate-600">
                                {{ $doctor->room_number ?? 'Consultation Suite' }}
                            </td>
                            <td class="py-4 font-bold text-slate-800">
                                {{ $doctor->appointments_count }} visits
                            </td>
                            <td class="py-4">
                                <form method="POST" action="{{ route('admin.doctors.toggle', $doctor) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold border transition cursor-pointer {{ $doctor->is_available ? 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
                                        ● {{ $doctor->is_available ? 'Active & Available' : 'Unavailable' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 text-right space-x-1 whitespace-nowrap">
                                <button @click="openEditModal = true" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Edit Details
                                </button>

                                <!-- Edit Doctor Modal -->
                                <div x-show="openEditModal" x-cloak
                                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 text-left" @click.away="openEditModal = false">
                                        <h3 class="text-lg font-bold text-slate-900 mb-1">Edit Doctor: {{ $doctor->user->name }}</h3>
                                        <p class="text-xs text-slate-500 mb-4">Modify doctor credentials, department, and consultation fee.</p>

                                        <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Full Name *</label>
                                                    <input type="text" name="name" value="{{ $doctor->user->name }}" required
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Email Address *</label>
                                                    <input type="email" name="email" value="{{ $doctor->user->email }}" required
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Phone Number *</label>
                                                    <input type="text" name="phone" value="{{ $doctor->user->phone }}" required
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Gender *</label>
                                                    <select name="gender" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs bg-white outline-none">
                                                        <option value="female" {{ $doctor->user->gender === 'female' ? 'selected' : '' }}>Female</option>
                                                        <option value="male" {{ $doctor->user->gender === 'male' ? 'selected' : '' }}>Male</option>
                                                        <option value="other" {{ $doctor->user->gender === 'other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Department *</label>
                                                    <select name="department_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs bg-white outline-none">
                                                        @foreach($departments as $dept)
                                                            <option value="{{ $dept->id }}" {{ $doctor->department_id == $dept->id ? 'selected' : '' }}>
                                                                {{ $dept->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Consultation Fee ($) *</label>
                                                    <input type="number" step="0.01" name="consultation_fee" value="{{ $doctor->consultation_fee }}" required
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Specialization Title *</label>
                                                    <input type="text" name="specialization" value="{{ $doctor->specialization }}" required
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Clinic Room / Office</label>
                                                    <input type="text" name="room_number" value="{{ $doctor->room_number }}"
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Qualifications</label>
                                                    <input type="text" name="qualifications" value="{{ $doctor->qualifications }}"
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Years Experience</label>
                                                    <input type="number" name="experience_years" value="{{ $doctor->experience_years }}" required
                                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Biography / Clinical Overview</label>
                                                <textarea name="bio" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs outline-none">{{ $doctor->bio }}</textarea>
                                            </div>

                                            <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700">
                                                <input type="checkbox" name="is_available" value="1" {{ $doctor->is_available ? 'checked' : '' }} class="rounded text-teal-600">
                                                <span>Active & Available for patient bookings</span>
                                            </label>

                                            <div class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                                                <button type="button" @click="openEditModal = false" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition">
                                                    Update Doctor
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
    </div>

    <!-- Register New Doctor Modal -->
    <div x-show="openAddModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 text-left" @click.away="openAddModal = false">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Register New Doctor</h3>
            <p class="text-xs text-slate-500 mb-4">Creates doctor user login and initializes standard Monday-Friday timetable.</p>

            <form method="POST" action="{{ route('admin.doctors.store') }}" class="space-y-3.5 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Dr. Robert Vance"
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="doctor@clinic.com"
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password *</label>
                        <input type="password" name="password" required placeholder="Min 8 characters"
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number *</label>
                        <input type="text" name="phone" required placeholder="+1 (555) 000-0000"
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department *</label>
                        <select name="department_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white outline-none">
                            <option value="">Select Specialty</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white outline-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Specialization Title *</label>
                        <input type="text" name="specialization" required placeholder="e.g. Senior Pediatrician"
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Consultation Fee ($) *</label>
                        <input type="number" step="0.01" name="consultation_fee" value="70.00" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Qualifications</label>
                        <input type="text" name="qualifications" placeholder="e.g. MBBS, MD, Board Certified"
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Years Experience</label>
                        <input type="number" name="experience_years" value="5" min="0" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Clinic Room / Suite</label>
                    <input type="text" name="room_number" placeholder="e.g. Room 302, Wing B"
                           class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Biography</label>
                    <textarea name="bio" rows="2" placeholder="Summary of medical career, patient care philosophy..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-xs transition">
                        Register Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
