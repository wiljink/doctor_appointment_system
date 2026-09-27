@extends('layouts.app')

@section('title', 'Admin - Patient Registry')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                Admin Panel
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Patient Directory</h1>
            <p class="text-sm text-slate-500">View registered clinic patients, contact details, emergency contacts, and visit statistics.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
            &larr; Admin Dashboard
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200 mb-6">
        <form action="{{ route('admin.patients') }}" method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search patient by name, email, or phone number..."
                   class="flex-1 px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 outline-none">
            <button type="submit" class="py-2 px-5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.patients') }}" class="py-2 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        @if($patients->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 tracking-wider">
                            <th class="pb-3">Patient Name</th>
                            <th class="pb-3">Contact Email & Phone</th>
                            <th class="pb-3">Gender / Age</th>
                            <th class="pb-3">Blood Group</th>
                            <th class="pb-3">Emergency Contact</th>
                            <th class="pb-3">Visits</th>
                            <th class="pb-3 text-right">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($patients as $patient)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($patient->name, 0, 2)) }}
                                        </div>
                                        <div class="font-bold text-slate-900 text-sm">
                                            {{ $patient->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <div class="font-semibold text-slate-800">{{ $patient->email }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $patient->phone ?? 'No phone registered' }}</div>
                                </td>
                                <td class="py-4 text-slate-700">
                                    {{ ucfirst($patient->gender ?? 'Unknown') }}
                                    @if($patient->date_of_birth)
                                        <span class="text-slate-400">({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs)</span>
                                    @endif
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] {{ $patient->blood_group ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'text-slate-400' }}">
                                        {{ $patient->blood_group ?: 'Unknown' }}
                                    </span>
                                </td>
                                <td class="py-4 text-slate-600 text-[11px]">
                                    {{ $patient->emergency_contact ?: 'None provided' }}
                                </td>
                                <td class="py-4 font-bold text-teal-700">
                                    {{ $patient->appointments_count }} visits
                                </td>
                                <td class="py-4 text-right text-slate-400 text-[11px] font-mono">
                                    {{ $patient->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $patients->links() }}
            </div>
        @else
            <div class="text-center py-12 text-slate-400 text-xs">
                No patient records found matching your search.
            </div>
        @endif
    </div>

</div>
@endsection
