@extends('layouts.app')

@section('title', 'Manage Practice Schedule & Leaves')

@section('content')
<div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-200 mb-1">
                Doctor Portal
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Practice Schedule & Time Slots</h1>
            <p class="text-sm text-slate-500">Configure your active clinic days, consultation slot duration, lunch breaks, and vacation leaves.</p>
        </div>

        <a href="{{ route('doctor.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
            &larr; Doctor Dashboard
        </a>
    </div>

    <div class="space-y-8">
        
        <!-- Weekly Schedule Form -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Weekly Consultation Timetable</h2>
                    <p class="text-xs text-slate-500">Patients will only be able to book slots within your marked working hours.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('doctor.schedule.update') }}" class="space-y-4">
                @csrf

                @php
                    $dayNames = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        0 => 'Sunday',
                    ];
                @endphp

                <div class="divide-y divide-slate-100">
                    @foreach($dayNames as $dayIndex => $dayLabel)
                        @php
                            $sch = $schedules->get($dayIndex);
                            $isActive = $sch ? $sch->is_active : false;
                            $startTime = $sch ? \Carbon\Carbon::parse($sch->start_time)->format('H:i') : '09:00';
                            $endTime = $sch ? \Carbon\Carbon::parse($sch->end_time)->format('H:i') : '17:00';
                            $duration = $sch ? $sch->slot_duration_minutes : 30;
                            $breakStart = $sch && $sch->break_start_time ? \Carbon\Carbon::parse($sch->break_start_time)->format('H:i') : '12:30';
                            $breakEnd = $sch && $sch->break_end_time ? \Carbon\Carbon::parse($sch->break_end_time)->format('H:i') : '13:30';
                        @endphp

                        <div class="py-4 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs"
                             x-data="{ active: {{ $isActive ? 'true' : 'false' }} }">
                            
                            <!-- Day Name & Active Toggle -->
                            <div class="sm:col-span-3 flex items-center space-x-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="schedules[{{ $dayIndex }}][is_active]" value="1"
                                           x-model="active" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-600"></div>
                                </label>
                                <span class="font-bold text-sm text-slate-800" :class="active ? 'text-slate-900' : 'text-slate-400'">
                                    {{ $dayLabel }}
                                </span>
                            </div>

                            <!-- Shift Start & End Time -->
                            <div class="sm:col-span-4 flex items-center space-x-2" x-show="active">
                                <div class="flex-1">
                                    <label class="block text-[10px] text-slate-400 font-bold uppercase">Start Time</label>
                                    <input type="time" name="schedules[{{ $dayIndex }}][start_time]" value="{{ $startTime }}"
                                           class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs outline-none bg-white">
                                </div>
                                <span class="text-slate-400 pt-3">to</span>
                                <div class="flex-1">
                                    <label class="block text-[10px] text-slate-400 font-bold uppercase">End Time</label>
                                    <input type="time" name="schedules[{{ $dayIndex }}][end_time]" value="{{ $endTime }}"
                                           class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs outline-none bg-white">
                                </div>
                            </div>

                            <!-- Slot Duration & Break -->
                            <div class="sm:col-span-5 flex items-center space-x-2" x-show="active">
                                <div class="w-24">
                                    <label class="block text-[10px] text-slate-400 font-bold uppercase">Slot (mins)</label>
                                    <select name="schedules[{{ $dayIndex }}][slot_duration_minutes]"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs outline-none bg-white">
                                        <option value="15" {{ $duration == 15 ? 'selected' : '' }}>15 min</option>
                                        <option value="20" {{ $duration == 20 ? 'selected' : '' }}>20 min</option>
                                        <option value="30" {{ $duration == 30 ? 'selected' : '' }}>30 min</option>
                                        <option value="45" {{ $duration == 45 ? 'selected' : '' }}>45 min</option>
                                        <option value="60" {{ $duration == 60 ? 'selected' : '' }}>60 min</option>
                                    </select>
                                </div>

                                <div class="flex-1">
                                    <label class="block text-[10px] text-slate-400 font-bold uppercase">Lunch Break Start</label>
                                    <input type="time" name="schedules[{{ $dayIndex }}][break_start_time]" value="{{ $breakStart }}"
                                           class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs outline-none bg-white">
                                </div>

                                <div class="flex-1">
                                    <label class="block text-[10px] text-slate-400 font-bold uppercase">Break End</label>
                                    <input type="time" name="schedules[{{ $dayIndex }}][break_end_time]" value="{{ $breakEnd }}"
                                           class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs outline-none bg-white">
                                </div>
                            </div>

                            <div class="sm:col-span-9 text-slate-400 italic text-[11px]" x-show="!active">
                                Clinic is closed on this day.
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="py-2.5 px-6 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                        Save Weekly Timetable
                    </button>
                </div>
            </form>
        </div>

        <!-- Scheduled Leaves / Blackout Dates Section -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Scheduled Leaves & Blackout Dates</h2>
                    <p class="text-xs text-slate-500">Block specific dates for vacation, medical conferences, or sick leave.</p>
                </div>
            </div>

            <!-- Add Leave Form -->
            <form method="POST" action="{{ route('doctor.leaves.store') }}" class="mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                @csrf
                <div class="sm:col-span-4">
                    <label class="block text-slate-700 font-bold mb-1">Leave Date *</label>
                    <input type="date" name="leave_date" min="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white outline-none">
                </div>

                <div class="sm:col-span-5">
                    <label class="block text-slate-700 font-bold mb-1">Reason (Optional)</label>
                    <input type="text" name="reason" placeholder="e.g. Annual Vacation / Medical Conference"
                           class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white outline-none">
                </div>

                <div class="sm:col-span-3 flex items-end">
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition">
                        + Add Blackout Date
                    </button>
                </div>
            </form>

            <!-- Active Leaves List -->
            @if($leaves->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($leaves as $leave)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-900">{{ $leave->leave_date->format('l, F j, Y') }}</span>
                                @if($leave->reason)
                                    <span class="text-slate-500 ml-2">({{ $leave->reason }})</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('doctor.leaves.delete', $leave) }}" onsubmit="return confirm('Remove this leave date?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-[11px] transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-3 text-center">No upcoming leaves scheduled. You are on active regular duty.</p>
            @endif
        </div>

    </div>
</div>
@endsection
