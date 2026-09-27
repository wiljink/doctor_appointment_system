<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Services\AppointmentSlotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AppointmentBookingController extends Controller
{
    public function index(Request $request): View
    {
        $selectedDoctorId = $request->query('doctor_id');
        $selectedDeptId = $request->query('department_id');

        $departments = Department::active()->with(['doctors' => function ($query): void {
            $query->where('is_available', true)->with('user');
        }])->get();

        $doctors = Doctor::available()->with(['user', 'department', 'schedules'])->get();

        $selectedDoctor = null;
        if ($selectedDoctorId) {
            $selectedDoctor = Doctor::with(['user', 'department', 'schedules'])->find($selectedDoctorId);
        }

        return view('booking.index', compact('departments', 'doctors', 'selectedDoctor', 'selectedDeptId'));
    }

    public function getSlots(Request $request, AppointmentSlotService $slotService): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $doctor = Doctor::with('schedules')->findOrFail($validated['doctor_id']);
        $slotData = $slotService->getAvailableSlots($doctor, $validated['date']);

        return response()->json($slotData);
    }

    public function store(Request $request, AppointmentSlotService $slotService): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please sign in or register to complete your booking.');
        }

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'appointment_time' => ['required', 'string'],
            'reason' => ['required', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cash,card,online'],
        ]);

        $doctor = Doctor::with('department')->findOrFail($validated['doctor_id']);

        // Verify slot is genuinely available
        $slotData = $slotService->getAvailableSlots($doctor, $validated['appointment_date']);

        $slotAvailable = false;
        $endTime = null;

        foreach ($slotData['slots'] as $slot) {
            if ($slot['start_time'] === $validated['appointment_time']) {
                if ($slot['is_available']) {
                    $slotAvailable = true;
                    $endTime = $slot['end_time'];
                }
                break;
            }
        }

        if (! $slotAvailable) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'The selected appointment slot is no longer available. Please pick another slot.']);
        }

        // Generate unique appointment number: APT-YYYYMMDD-XXXX
        $dateCode = Carbon::parse($validated['appointment_date'])->format('Ymd');
        $randomCode = strtoupper(Str::random(4));
        $appointmentNumber = 'APT-'.$dateCode.'-'.$randomCode;

        while (Appointment::where('appointment_number', $appointmentNumber)->exists()) {
            $appointmentNumber = 'APT-'.$dateCode.'-'.strtoupper(Str::random(4));
        }

        $appointment = Appointment::create([
            'appointment_number' => $appointmentNumber,
            'patient_id' => Auth::id(),
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'end_time' => $endTime ?? Carbon::parse($validated['appointment_time'])->addMinutes(30)->format('H:i:s'),
            'status' => 'pending',
            'reason' => $validated['reason'],
            'fee' => $doctor->consultation_fee,
            'payment_status' => 'unpaid',
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('booking.confirmation', $appointment)
            ->with('status', 'Appointment booked successfully!');
    }

    public function confirmation(Appointment $appointment): View
    {
        // Ensure access authorization
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isPatient() && $appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized access to this appointment.');
            }
        }

        $appointment->load(['doctor.user', 'doctor.department', 'patient']);

        return view('booking.confirmation', compact('appointment'));
    }
}
