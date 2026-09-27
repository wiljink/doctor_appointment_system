@extends('layouts.app')

@section('title', 'Doctor Profile & Credentials - MediCare')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Doctor Profile & Clinic Credentials</h1>
        <p class="text-slate-500 mt-1">Manage your public directory bio, consultation fees, clinic room, and photo avatar.</p>
    </div>

    <form action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Avatar Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Doctor Avatar Photo</h2>
            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary-100 shadow-md">
                <div class="flex-1 text-center sm:text-left">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Upload Profile Photo</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-2">Display photo will appear on the public doctor directory and prescription slips. Max 2MB.</p>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Professional Credentials</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Doctor Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email (Login Account)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm py-2.5 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Department</label>
                    <input type="text" value="{{ $doctor->department->name ?? 'General' }}" disabled class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm py-2.5 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Contact Clinic Admin to transfer departments.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Specialization *</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}" required placeholder="e.g. Senior Interventional Cardiologist" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Degrees & Qualifications *</label>
                    <input type="text" name="qualifications" value="{{ old('qualifications', $doctor->qualifications) }}" required placeholder="e.g. MBBS, MD (Cardiology), FACC" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Experience (Years) *</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" min="0" max="60" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Consultation Fee ($ USD) *</label>
                    <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" min="0" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Clinic Room Number *</label>
                    <input type="text" name="room_number" value="{{ old('room_number', $doctor->room_number) }}" required placeholder="e.g. Suite 304, 3rd Floor" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Doctor Bio & Clinical Focus</label>
                <textarea name="bio" rows="4" placeholder="Brief clinical background, research areas, and patient approach..." class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">{{ old('bio', $doctor->bio) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('doctor.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm shadow-md shadow-primary-500/20 transition">
                Save Doctor Profile
            </button>
        </div>
    </form>
</div>
@endsection
