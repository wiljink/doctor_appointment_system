<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\PrescriptionItem;
use App\Notifications\AppointmentStatusChangedNotification;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DoctorDashboardController extends Controller
{
    private function getDoctor(): Doctor
    {
        $doctor = Auth::user()->doctorProfile;

        if (! $doctor) {
            abort(403, 'Doctor profile not found for this user account.');
        }

        return $doctor;
    }

    public function index(): View
    {
        $doctor = $this->getDoctor();
        $today = Carbon::today()->toDateString();

        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->with(['patient', 'department'])
            ->orderBy('appointment_time')
            ->get();

        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', '>', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['patient', 'department'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        $stats = [
            'today_total' => $todayAppointments->count(),
            'today_pending' => $todayAppointments->where('status', 'pending')->count(),
            'today_confirmed' => $todayAppointments->where('status', 'confirmed')->count(),
            'today_completed' => $todayAppointments->where('status', 'completed')->count(),
            'total_completed' => Appointment::where('doctor_id', $doctor->id)->where('status', 'completed')->count(),
            'total_patients' => Appointment::where('doctor_id', $doctor->id)->distinct('patient_id')->count('patient_id'),
        ];

        return view('doctor.dashboard', compact('doctor', 'todayAppointments', 'upcomingAppointments', 'stats'));
    }

    public function appointments(Request $request): View
    {
        $doctor = $this->getDoctor();
        $status = $request->query('status');
        $date = $request->query('date');

        $query = Appointment::where('doctor_id', $doctor->id)
            ->with(['patient', 'department', 'medicalRecord']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(10)
            ->withQueryString();

        return view('doctor.appointments.index', compact('doctor', 'appointments', 'status', 'date'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $this->getDoctor();

        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled,no_show'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'cancelled') {
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? 'Cancelled by doctor.';
            $updateData['cancelled_by'] = 'doctor';
        }

        $appointment->update($updateData);

        // Notify patient
        if ($appointment->patient) {
            $appointment->patient->notify(new AppointmentStatusChangedNotification($appointment, $validated['status'], $validated['cancellation_reason'] ?? null));
        }
        app(SmsService::class)->sendStatusUpdate($appointment, $validated['status'], $validated['cancellation_reason'] ?? null);

        return back()->with('status', 'Appointment status updated to '.ucfirst($validated['status']).'.');
    }

    public function showConsultation(Appointment $appointment): View
    {
        $doctor = $this->getDoctor();

        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $appointment->load(['patient', 'department', 'medicalRecord.prescriptions']);

        // Load past consultations of this patient
        $pastRecords = MedicalRecord::where('patient_id', $appointment->patient_id)
            ->where('appointment_id', '!=', $appointment->id)
            ->with(['doctor.user', 'prescriptions'])
            ->orderByDesc('created_at')
            ->get();

        return view('doctor.consultation.show', compact('doctor', 'appointment', 'pastRecords'));
    }

    public function storeConsultation(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = $this->getDoctor();

        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $validated = $request->validate([
            'diagnosis' => ['required', 'string', 'max:1000'],
            'symptoms' => ['nullable', 'string', 'max:1000'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'heart_rate' => ['nullable', 'string', 'max:20'],
            'temperature' => ['nullable', 'string', 'max:20'],
            'weight' => ['nullable', 'string', 'max:20'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
            'advice' => ['nullable', 'string', 'max:2000'],
            'follow_up_date' => ['nullable', 'date', 'after:today'],
            'medicines' => ['nullable', 'array'],
            'medicines.*.name' => ['required', 'string', 'max:255'],
            'medicines.*.dosage' => ['required', 'string', 'max:100'],
            'medicines.*.frequency' => ['required', 'string', 'max:100'],
            'medicines.*.duration' => ['required', 'string', 'max:100'],
            'medicines.*.instructions' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['required', 'in:paid,unpaid'],
        ]);

        DB::transaction(function () use ($validated, $appointment, $doctor): void {
            $record = MedicalRecord::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'doctor_id' => $doctor->id,
                    'patient_id' => $appointment->patient_id,
                    'diagnosis' => $validated['diagnosis'],
                    'symptoms' => $validated['symptoms'] ?? null,
                    'blood_pressure' => $validated['blood_pressure'] ?? null,
                    'heart_rate' => $validated['heart_rate'] ?? null,
                    'temperature' => $validated['temperature'] ?? null,
                    'weight' => $validated['weight'] ?? null,
                    'clinical_notes' => $validated['clinical_notes'] ?? null,
                    'advice' => $validated['advice'] ?? null,
                    'follow_up_date' => $validated['follow_up_date'] ?? null,
                ]
            );

            // Clear previous prescription items if re-saving
            $record->prescriptions()->delete();

            if (! empty($validated['medicines'])) {
                foreach ($validated['medicines'] as $med) {
                    if (! empty(trim($med['name'] ?? ''))) {
                        PrescriptionItem::create([
                            'medical_record_id' => $record->id,
                            'medicine_name' => $med['name'],
                            'dosage' => $med['dosage'],
                            'frequency' => $med['frequency'],
                            'duration' => $med['duration'],
                            'instructions' => $med['instructions'] ?? null,
                        ]);
                    }
                }
            }

            $appointment->update([
                'status' => 'completed',
                'payment_status' => $validated['payment_status'],
            ]);

            // Notify patient that prescription and consultation notes are ready
            if ($appointment->patient) {
                $appointment->patient->notify(new AppointmentStatusChangedNotification($appointment, 'completed'));
            }
            app(SmsService::class)->sendStatusUpdate($appointment, 'completed');
        });

        return redirect()->route('doctor.appointments')
            ->with('status', 'Consultation saved and appointment marked as completed!');
    }

    public function schedule(): View
    {
        $doctor = $this->getDoctor();
        $schedules = $doctor->schedules()->orderBy('day_of_week')->get()->keyBy('day_of_week');
        $leaves = $doctor->leaves()->whereDate('leave_date', '>=', now()->toDateString())->orderBy('leave_date')->get();

        return view('doctor.schedule.index', compact('doctor', 'schedules', 'leaves'));
    }

    public function updateSchedule(Request $request): RedirectResponse
    {
        $doctor = $this->getDoctor();

        $validated = $request->validate([
            'schedules' => ['required', 'array'],
            'schedules.*.is_active' => ['nullable', 'boolean'],
            'schedules.*.start_time' => ['required_with:schedules.*.is_active'],
            'schedules.*.end_time' => ['required_with:schedules.*.is_active'],
            'schedules.*.slot_duration_minutes' => ['required_with:schedules.*.is_active', 'integer', 'min:10', 'max:120'],
            'schedules.*.break_start_time' => ['nullable'],
            'schedules.*.break_end_time' => ['nullable'],
        ]);

        foreach ($validated['schedules'] as $day => $data) {
            $isActive = ! empty($data['is_active']);

            DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => (int) $day,
                ],
                [
                    'start_time' => $data['start_time'] ?? '09:00:00',
                    'end_time' => $data['end_time'] ?? '17:00:00',
                    'slot_duration_minutes' => (int) ($data['slot_duration_minutes'] ?? 30),
                    'break_start_time' => $data['break_start_time'] ?: null,
                    'break_end_time' => $data['break_end_time'] ?: null,
                    'is_active' => $isActive,
                ]
            );
        }

        return back()->with('status', 'Weekly schedule updated successfully.');
    }

    public function storeLeave(Request $request): RedirectResponse
    {
        $doctor = $this->getDoctor();

        $validated = $request->validate([
            'leave_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DoctorLeave::updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'leave_date' => $validated['leave_date'],
            ],
            [
                'reason' => $validated['reason'] ?? null,
            ]
        );

        return back()->with('status', 'Scheduled leave added successfully.');
    }

    public function deleteLeave(DoctorLeave $leave): RedirectResponse
    {
        $doctor = $this->getDoctor();

        if ($leave->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized.');
        }

        $leave->delete();

        return back()->with('status', 'Scheduled leave removed.');
    }

    /**
     * Download prescription PDF for doctor.
     */
    public function downloadPrescriptionPdf(MedicalRecord $record): Response
    {
        $doctor = $this->getDoctor();

        if ($record->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access to this prescription.');
        }

        $record->load(['doctor.user', 'doctor.department', 'appointment', 'prescriptions', 'patient']);

        $pdf = Pdf::loadView('prescriptions.pdf', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Prescription-{$record->appointment->appointment_number}.pdf");
    }

    /**
     * Show doctor profile edit form.
     */
    public function profile(): View
    {
        $doctor = $this->getDoctor();
        $user = Auth::user();

        return view('doctor.profile', compact('doctor', 'user'));
    }

    /**
     * Update doctor profile, clinic details, and avatar image.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $doctor = $this->getDoctor();
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialization' => ['required', 'string', 'max:255'],
            'qualifications' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'room_number' => ['required', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->save();

        $doctor->update([
            'specialization' => $validated['specialization'],
            'qualifications' => $validated['qualifications'],
            'experience_years' => $validated['experience_years'],
            'consultation_fee' => $validated['consultation_fee'],
            'room_number' => $validated['room_number'],
            'bio' => $validated['bio'],
        ]);

        return back()->with('status', 'Doctor profile and credentials updated successfully.');
    }
}
