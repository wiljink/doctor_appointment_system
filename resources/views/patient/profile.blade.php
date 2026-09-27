@extends('layouts.app')

@section('title', 'My Profile & Avatar - MediCare')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Patient Profile & Health Details</h1>
        <p class="text-slate-500 mt-1">Manage your personal information, emergency contact, and profile avatar.</p>
    </div>

    <form action="{{ route('patient.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Avatar Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Profile Avatar</h2>
            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover ring-4 ring-primary-100 shadow-md">
                <div class="flex-1 text-center sm:text-left">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Upload New Photo</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-2">Recommended: Square JPEG, PNG, or WebP. Max 2MB.</p>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Personal & Contact Info</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm py-2.5 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Email cannot be changed.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Gender</label>
                    <select name="gender" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                        <option value="other" @selected(old('gender', $user->gender) === 'other')>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Blood Group</label>
                    <select name="blood_group" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                        <option value="">Select blood group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}" @selected(old('blood_group', $user->blood_group) === $bg)>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $user->emergency_contact) }}" placeholder="e.g. Spouse / Parent +1 555-0199" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Home Residential Address</label>
                    <textarea name="address" rows="2" placeholder="Street, City, Postal Code" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500 py-2">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('patient.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm shadow-md shadow-primary-500/20 transition">
                Save Profile Changes
            </button>
        </div>
    </form>
</div>
@endsection
