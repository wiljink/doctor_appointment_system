<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function cancelAppointment(Request $request, Appointment $appointment): RedirectResponse
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
}
