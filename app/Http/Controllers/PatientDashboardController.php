<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Notifications\AppointmentStatusChangedNotification;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $upcomingAppointments = Appointment::where('patient_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->with(['doctor.user', 'department'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $pastAppointments = Appointment::where('patient_id', $user->id)
            ->where(function ($query): void {
                $query->whereIn('status', ['completed', 'cancelled', 'no_show'])
                    ->orWhereDate('appointment_date', '<', now()->toDateString());
            })
            ->with(['doctor.user', 'department', 'medicalRecord'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(5, ['*'], 'past_page');

        $stats = [
            'upcoming' => $upcomingAppointments->count(),
            'total' => Appointment::where('patient_id', $user->id)->count(),
            'completed' => Appointment::where('patient_id', $user->id)->where('status', 'completed')->count(),
            'prescriptions' => MedicalRecord::where('patient_id', $user->id)->has('prescriptions')->count(),
        ];

        return view('patient.dashboard', compact('upcomingAppointments', 'pastAppointments', 'stats'));
    }

    public function showAppointment(Appointment $appointment): View
    {
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $appointment->load(['doctor.user', 'department', 'medicalRecord.prescriptions']);

        return view('patient.appointments.show', compact('appointment'));
    }

    public function cancelAppointment(Request $request, Appointment $appointment, SmsService $smsService): RedirectResponse
    {
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (! $appointment->canBeCancelled()) {
            return back()->with('error', 'This appointment cannot be cancelled at this stage.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_by' => 'patient',
        ]);

        // Dispatch notifications
        $patient = Auth::user();
        $patient->notify(new AppointmentStatusChangedNotification($appointment, 'cancelled', $validated['cancellation_reason']));

        if ($appointment->doctor && $appointment->doctor->user) {
            $appointment->doctor->user->notify(new AppointmentStatusChangedNotification($appointment, 'cancelled', $validated['cancellation_reason']));
        }

        $smsService->sendStatusUpdate($appointment, 'cancelled', $validated['cancellation_reason']);

        return back()->with('status', 'Appointment has been cancelled successfully.');
    }

    public function medicalRecords(): View
    {
        $records = MedicalRecord::where('patient_id', Auth::id())
            ->with(['doctor.user', 'doctor.department', 'appointment', 'prescriptions'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('patient.records.index', compact('records'));
    }

    public function showPrescription(MedicalRecord $record): View
    {
        if ($record->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this medical record.');
        }

        $record->load(['doctor.user', 'doctor.department', 'appointment', 'prescriptions', 'patient']);

        return view('patient.records.prescription', compact('record'));
    }

    /**
     * Download prescription as formatted PDF.
     */
    public function downloadPrescriptionPdf(MedicalRecord $record): Response
    {
        if ($record->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this prescription.');
        }

        $record->load(['doctor.user', 'doctor.department', 'appointment', 'prescriptions', 'patient']);

        $pdf = Pdf::loadView('prescriptions.pdf', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Prescription-{$record->appointment->appointment_number}.pdf");
    }

    /**
     * Show patient profile editing form.
     */
    public function profile(): View
    {
        $user = Auth::user();

        return view('patient.profile', compact('user'));
    }

    /**
     * Update patient profile and upload avatar.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('status', 'Your profile and avatar have been updated successfully.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markNotificationsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Notifications marked as read.');
    }
}
