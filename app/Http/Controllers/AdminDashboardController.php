<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->startOfMonth()->toDateString();

        $stats = [
            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'month_appointments' => Appointment::whereDate('appointment_date', '>=', $thisMonth)->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'confirmed_appointments' => Appointment::where('status', 'confirmed')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            'total_revenue' => (float) Appointment::where('status', 'completed')->orWhere('payment_status', 'paid')->sum('fee'),
            'total_doctors' => Doctor::count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_departments' => Department::count(),
        ];

        $recentAppointments = Appointment::with(['patient', 'doctor.user', 'department'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $todaySchedule = Appointment::whereDate('appointment_date', $today)
            ->with(['patient', 'doctor.user', 'department'])
            ->orderBy('appointment_time')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAppointments', 'todaySchedule'));
    }

    public function appointments(Request $request): View
    {
        $doctorId = $request->query('doctor_id');
        $deptId = $request->query('department_id');
        $status = $request->query('status');
        $date = $request->query('date');
        $search = $request->query('search');

        $query = Appointment::with(['patient', 'doctor.user', 'department']);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($deptId) {
            $query->where('department_id', $deptId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('appointment_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($sub) use ($search): void {
                        $sub->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(15)
            ->withQueryString();

        $doctors = Doctor::with('user')->get();
        $departments = Department::all();

        return view('admin.appointments.index', compact('appointments', 'doctors', 'departments', 'doctorId', 'deptId', 'status', 'date', 'search'));
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
            'payment_status' => ['nullable', 'in:unpaid,paid,refunded'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $data = ['status' => $validated['status']];

        if (! empty($validated['payment_status'])) {
            $data['payment_status'] = $validated['payment_status'];
        }

        if ($validated['status'] === 'cancelled') {
            $data['cancellation_reason'] = $validated['cancellation_reason'] ?? 'Cancelled by administrator.';
            $data['cancelled_by'] = 'admin';
        }

        $appointment->update($data);

        return back()->with('status', 'Appointment #'.$appointment->appointment_number.' updated successfully.');
    }

    public function doctors(): View
    {
        $doctors = Doctor::with(['user', 'department', 'schedules'])
            ->withCount('appointments')
            ->get();

        $departments = Department::active()->get();

        return view('admin.doctors.index', compact('doctors', 'departments'));
    }

    public function storeDoctor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'department_id' => ['required', 'exists:departments,id'],
            'specialization' => ['required', 'string', 'max:255'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'room_number' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'department_id' => $validated['department_id'],
                'specialization' => $validated['specialization'],
                'qualifications' => $validated['qualifications'] ?? null,
                'experience_years' => $validated['experience_years'],
                'consultation_fee' => $validated['consultation_fee'],
                'room_number' => $validated['room_number'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'is_available' => true,
            ]);

            // Create standard Mon-Fri schedule
            for ($day = 1; $day <= 5; $day++) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'slot_duration_minutes' => 30,
                    'break_start_time' => '12:30:00',
                    'break_end_time' => '13:30:00',
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('admin.doctors')->with('status', 'Doctor added successfully with standard schedule!');
    }

    public function updateDoctor(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($doctor->user_id)],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'department_id' => ['required', 'exists:departments,id'],
            'specialization' => ['required', 'string', 'max:255'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'room_number' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $doctor->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
        ]);

        $doctor->update([
            'department_id' => $validated['department_id'],
            'specialization' => $validated['specialization'],
            'qualifications' => $validated['qualifications'] ?? null,
            'experience_years' => $validated['experience_years'],
            'consultation_fee' => $validated['consultation_fee'],
            'room_number' => $validated['room_number'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'is_available' => ! empty($validated['is_available']),
        ]);

        return redirect()->route('admin.doctors')->with('status', 'Doctor profile updated successfully.');
    }

    public function toggleDoctorAvailability(Doctor $doctor): RedirectResponse
    {
        $doctor->update(['is_available' => ! $doctor->is_available]);

        $status = $doctor->is_available ? 'active & available' : 'unavailable';

        return back()->with('status', 'Doctor status set to '.$status.'.');
    }

    public function departments(): View
    {
        $departments = Department::withCount(['doctors', 'appointments'])->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Department::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        Department::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'stethoscope',
            'is_active' => ! empty($validated['is_active']),
        ]);

        return redirect()->route('admin.departments')->with('status', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $department->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'stethoscope',
            'is_active' => ! empty($validated['is_active']),
        ]);

        return redirect()->route('admin.departments')->with('status', 'Department updated successfully.');
    }

    public function patients(Request $request): View
    {
        $search = $request->query('search');

        $query = User::where('role', 'patient')->withCount('appointments');

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients', 'search'));
    }
}
