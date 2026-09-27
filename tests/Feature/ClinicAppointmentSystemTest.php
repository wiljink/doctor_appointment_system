<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Notifications\AppointmentBookedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicAppointmentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('MediSync');
        $response->assertSee('Instant Appointments');
    }

    public function test_doctors_directory_is_accessible(): void
    {
        $response = $this->get(route('doctors.index'));

        $response->assertStatus(200);
        $response->assertSee('Our Medical Specialists');
    }

    public function test_slots_api_returns_valid_schedule_slots(): void
    {
        $doctor = Doctor::with('schedules')->first();
        $this->assertNotNull($doctor);

        // Find a date with active schedule
        $schedule = $doctor->schedules->where('is_active', true)->first();
        $this->assertNotNull($schedule);

        $nextDate = Carbon::now();
        while ($nextDate->dayOfWeek !== $schedule->day_of_week) {
            $nextDate->addDay();
        }

        $response = $this->getJson(route('booking.slots', [
            'doctor_id' => $doctor->id,
            'date' => $nextDate->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'doctor_id',
            'date',
            'has_schedule',
            'slots',
        ]);
        $response->assertJson([
            'has_schedule' => true,
        ]);
    }

    public function test_patient_can_book_appointment(): void
    {
        $patient = User::where('role', 'patient')->first();
        $doctor = Doctor::with('schedules')->first();

        $schedule = $doctor->schedules->where('is_active', true)->first();
        $nextDate = Carbon::now()->addDays(7);
        while ($nextDate->dayOfWeek !== $schedule->day_of_week) {
            $nextDate->addDay();
        }

        // Get slots to pick a valid one
        $slotsResponse = $this->getJson(route('booking.slots', [
            'doctor_id' => $doctor->id,
            'date' => $nextDate->toDateString(),
        ]));

        $slots = $slotsResponse->json('slots');
        $availableSlot = collect($slots)->firstWhere('is_available', true);
        $this->assertNotNull($availableSlot);

        $bookingData = [
            'doctor_id' => $doctor->id,
            'appointment_date' => $nextDate->toDateString(),
            'appointment_time' => $availableSlot['start_time'],
            'reason' => 'Annual general wellness medical checkup.',
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($patient)->post(route('booking.store'), $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_time' => $availableSlot['start_time'],
            'status' => 'pending',
        ]);
    }

    public function test_doctor_can_record_consultation_and_prescribe_medicine(): void
    {
        $doctorUser = User::where('email', 'doctor.sarah@clinic.com')->first();
        $this->assertNotNull($doctorUser);
        $patient = User::where('role', 'patient')->first();
        $this->assertNotNull($patient);

        $appointment = Appointment::create([
            'appointment_number' => 'APT-TEST-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctorUser->doctorProfile->id,
            'department_id' => $doctorUser->doctorProfile->department_id,
            'appointment_date' => Carbon::today()->toDateString(),
            'appointment_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'confirmed',
            'reason' => 'Chest consultation and persistent cough',
            'fee' => 85.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
        ]);

        $consultationData = [
            'diagnosis' => 'Mild Acute Bronchitis',
            'symptoms' => 'Productive cough, low-grade fever, sore throat.',
            'blood_pressure' => '120/80 mmHg',
            'heart_rate' => '76 bpm',
            'temperature' => '37.4 °C',
            'weight' => '70 kg',
            'clinical_notes' => 'Bilateral chest sounds clear, no wheezing.',
            'advice' => 'Adequate hydration, warm saline gargles, steam inhalation.',
            'follow_up_date' => Carbon::tomorrow()->addDays(7)->toDateString(),
            'payment_status' => 'paid',
            'medicines' => [
                [
                    'name' => 'Amoxicillin 500mg',
                    'dosage' => '500mg',
                    'frequency' => '3 times daily',
                    'duration' => '7 days',
                    'instructions' => 'Take with water after meals',
                ],
            ],
        ];

        $response = $this->actingAs($doctorUser)->post(route('doctor.consultation.store', $appointment), $consultationData);

        $response->assertRedirect(route('doctor.appointments'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('medical_records', [
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Mild Acute Bronchitis',
        ]);

        $this->assertDatabaseHas('prescription_items', [
            'medicine_name' => 'Amoxicillin 500mg',
        ]);
    }

    public function test_admin_can_access_dashboard_and_update_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard');

        $appointment = Appointment::first();
        $this->assertNotNull($appointment);

        $statusUpdateResponse = $this->actingAs($admin)->post(route('admin.appointments.status', $appointment), [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $statusUpdateResponse->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
    }

    public function test_patient_can_cancel_pending_appointment(): void
    {
        $patient = User::where('role', 'patient')->first();
        $this->assertNotNull($patient);

        $doctor = Doctor::first();
        $this->assertNotNull($doctor);

        $appointment = Appointment::create([
            'appointment_number' => 'APT-CANCEL-TEST',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'appointment_time' => '11:00:00',
            'end_time' => '11:30:00',
            'status' => 'pending',
            'reason' => 'Schedule conflict test',
            'fee' => 70.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($patient)->post(route('patient.appointments.cancel', $appointment), [
            'cancellation_reason' => 'Work conflict, need to reschedule.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancelled_by' => 'patient',
        ]);
    }

    public function test_patient_can_checkout_and_pay_appointment_fee(): void
    {
        $patient = User::where('role', 'patient')->first();
        $this->assertNotNull($patient);

        $doctor = Doctor::first();
        $appointment = Appointment::create([
            'appointment_number' => 'APT-PAY-TEST',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'appointment_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'pending',
            'reason' => 'Payment test',
            'fee' => 75.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'card',
        ]);

        // Checkout page loads
        $checkoutResponse = $this->actingAs($patient)->get(route('payment.checkout', $appointment));
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertSee('Consultation Fee Checkout');

        // Process payment
        $payResponse = $this->actingAs($patient)->post(route('payment.process', $appointment), [
            'payment_method' => 'card',
            'card_holder' => 'John Doe',
            'card_number' => '4242 4242 4242 4242',
            'card_expiry' => '12/28',
            'card_cvv' => '123',
        ]);

        $payResponse->assertRedirect(route('payment.receipt', $appointment));

        $appointment->refresh();
        $this->assertEquals('paid', $appointment->payment_status);
        $this->assertNotNull($appointment->transaction_id);
        $this->assertNotNull($appointment->paid_at);

        // Receipt page loads
        $receiptResponse = $this->actingAs($patient)->get(route('payment.receipt', $appointment));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('Official Receipt');
        $receiptResponse->assertSee($appointment->transaction_id);
    }

    public function test_patient_and_doctor_can_download_prescription_pdf(): void
    {
        $record = MedicalRecord::with(['patient', 'doctor.user'])->first();
        $this->assertNotNull($record);

        $patient = $record->patient;
        $doctorUser = $record->doctor->user;

        // Patient downloads PDF
        $patientPdfResponse = $this->actingAs($patient)->get(route('patient.records.pdf', $record));
        $patientPdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $patientPdfResponse->headers->get('content-type'));

        // Doctor downloads PDF
        $doctorPdfResponse = $this->actingAs($doctorUser)->get(route('doctor.records.pdf', $record));
        $doctorPdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $doctorPdfResponse->headers->get('content-type'));
    }

    public function test_patient_can_update_profile_and_avatar(): void
    {
        Storage::fake('public');

        $patient = User::where('role', 'patient')->first();
        $this->assertNotNull($patient);

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($patient)->put(route('patient.profile.update'), [
            'name' => 'Johnathan Doe Updated',
            'phone' => '+1-555-9988',
            'date_of_birth' => '1990-05-15',
            'gender' => 'male',
            'blood_group' => 'O+',
            'address' => '123 Test Boulevard',
            'emergency_contact' => '+1-555-1122',
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $patient->refresh();

        $this->assertEquals('Johnathan Doe Updated', $patient->name);
        $this->assertEquals('+1-555-9988', $patient->phone);
        $this->assertNotNull($patient->avatar);
        Storage::disk('public')->assertExists($patient->avatar);
    }

    public function test_automated_appointment_reminders_command(): void
    {
        $patient = User::where('role', 'patient')->first();
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'appointment_number' => 'APT-REMIND-TEST',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'appointment_time' => '09:00:00',
            'end_time' => '09:30:00',
            'status' => 'confirmed',
            'reason' => 'Reminder command test',
            'fee' => 70.00,
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        $this->artisan('clinic:send-reminders')
            ->expectsOutputToContain('upcoming appointments eligible for reminders.')
            ->assertSuccessful();

        $appointment->refresh();
        $this->assertNotNull($appointment->reminder_sent_at);
    }

    public function test_in_app_notifications_can_be_marked_as_read(): void
    {
        $patient = User::where('role', 'patient')->first();
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'appointment_number' => 'APT-NOTIF-TEST',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'appointment_time' => '14:00:00',
            'end_time' => '14:30:00',
            'status' => 'pending',
            'reason' => 'Notification test',
            'fee' => 70.00,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
        ]);

        $patient->notify(new AppointmentBookedNotification($appointment, 'patient'));

        $this->assertTrue($patient->unreadNotifications->count() > 0);

        $response = $this->actingAs($patient)->post(route('notifications.markRead'));
        $response->assertRedirect();

        $patient->refresh();
        $this->assertEquals(0, $patient->unreadNotifications->count());
    }
}
