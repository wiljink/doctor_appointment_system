@extends('layouts.app')

@section('title', 'Admin - Specialty Departments')

@section('content')
<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ openAddModal: false }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 mb-1">
                Admin Panel
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinical Departments & Specialties</h1>
            <p class="text-sm text-slate-500">Configure medical departments, doctor assignments, and active patient booking categories.</p>
        </div>

        <div class="flex items-center space-x-2">
            <button @click="openAddModal = true" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                + Add New Department
            </button>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition">
                &larr; Dashboard
            </a>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($departments as $dept)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between"
                 x-data="{ openEditModal: false }">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center text-xl font-bold">
                            🩺
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $dept->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                            {{ $dept->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900">{{ $dept->name }}</h3>
                    <span class="text-[11px] font-mono text-slate-400 block mb-2">/{{ $dept->slug }}</span>

                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                        {{ $dept->description ?: 'No detailed department summary provided.' }}
                    </p>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-xl mb-4">
                        <div>
                            <span class="text-slate-400 block text-[10px] font-bold uppercase">Doctors</span>
                            <span class="font-bold text-slate-800">{{ $dept->doctors_count }} Assigned</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] font-bold uppercase">Bookings</span>
                            <span class="font-bold text-slate-800">{{ $dept->appointments_count }} Visits</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button @click="openEditModal = true" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Edit Department
                    </button>

                    <!-- Edit Department Modal -->
                    <div x-show="openEditModal" x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 text-left" @click.away="openEditModal = false">
                            <h3 class="text-base font-bold text-slate-900 mb-1">Edit: {{ $dept->name }}</h3>
                            <p class="text-xs text-slate-500 mb-4">Modify department title, description, and status.</p>

                            <form method="POST" action="{{ route('admin.departments.update', $dept) }}" class="space-y-3.5 text-xs">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Department Name *</label>
                                    <input type="text" name="name" value="{{ $dept->name }}" required
                                           class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Description</label>
                                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">{{ $dept->description }}</textarea>
                                </div>

                                <label class="flex items-center space-x-2 font-semibold text-slate-700">
                                    <input type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }} class="rounded text-teal-600">
                                    <span>Active in online booking search</span>
                                </label>

                                <div class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                                    <button type="button" @click="openEditModal = false" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-xs transition">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Department Modal -->
    <div x-show="openAddModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 text-left" @click.away="openAddModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Add New Medical Department</h3>
            <p class="text-xs text-slate-500 mb-4">Creates a new specialty wing for doctor and booking categorization.</p>

            <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-3.5 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Ophthalmology / ENT"
                           class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Clinical Description</label>
                    <textarea name="description" rows="3" placeholder="Overview of medical services and clinical focus..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-xl outline-none"></textarea>
                </div>

                <label class="flex items-center space-x-2 font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-teal-600">
                    <span>Activate immediately for patient bookings</span>
                </label>

                <div class="flex items-center space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-xs transition">
                        Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
