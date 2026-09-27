<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\PrescriptionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sarah = Doctor::whereHas('user', fn ($q) => $q->where('email', 'doctor.sarah@clinic.com'))->first();
        $michael = Doctor::whereHas('user', fn ($q) => $q->where('email', 'doctor.michael@clinic.com'))->first();
        $emily = Doctor::whereHas('user', fn ($q) => $q->where('email', 'doctor.emily@clinic.com'))->first();

        // Create sample patients
        $patient1 = User::updateOrCreate(
            ['email' => 'patient.john@clinic.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'patient',
                'phone' => '+1 (555) 789-0123',
                'gender' => 'male',
                'date_of_birth' => '1990-05-14',
                'blood_group' => 'O+',
                'address' => '742 Evergreen Terrace, Springfield',
                'emergency_contact' => '+1 (555) 789-9999 (Spouse)',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $patient2 = User::updateOrCreate(
            ['email' => 'jane.smith@clinic.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'role' => 'patient',
                'phone' => '+1 (555) 890-1234',
                'gender' => 'female',
                'date_of_birth' => '1995-08-22',
                'blood_group' => 'A+',
                'address' => '120 Elm St, Boston, MA',
                'emergency_contact' => '+1 (555) 890-8888 (Parent)',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $today = Carbon::today();

        // 1. Past completed appointment with medical record & prescription
        if ($sarah) {
            $pastDate = $today->copy()->subDays(3);
            $pastAppt = Appointment::updateOrCreate(
                ['appointment_number' => 'APT-'.$pastDate->format('Ymd').'-001'],
                [
                    'patient_id' => $patient1->id,
                    'doctor_id' => $sarah->id,
                    'department_id' => $sarah->department_id,
                    'appointment_date' => $pastDate->toDateString(),
                    'appointment_time' => '10:00:00',
                    'end_time' => '10:30:00',
                    'status' => 'completed',
                    'reason' => 'Experiencing occasional palpitations, shortness of breath after climbing stairs, and mild chest tightness.',
                    'notes' => 'Patient advised on regular blood pressure monitoring and low sodium diet.',
                    'fee' => $sarah->consultation_fee,
                    'payment_status' => 'paid',
                    'payment_method' => 'card',
                ]
            );

            $record = MedicalRecord::updateOrCreate(
                ['appointment_id' => $pastAppt->id],
                [
                    'doctor_id' => $sarah->id,
                    'patient_id' => $patient1->id,
                    'diagnosis' => 'Stage 1 Essential Hypertension & Mild Sinus Tachycardia',
                    'symptoms' => 'Occasional palpitations, exertional shortness of breath, headache.',
                    'blood_pressure' => '138/88 mmHg',
                    'heart_rate' => '84 bpm',
                    'temperature' => '36.7 °C',
                    'weight' => '78 kg',
                    'clinical_notes' => 'Heart sounds S1/S2 regular, no murmurs detected. ECG demonstrates normal sinus rhythm with mild tachycardia.',
                    'advice' => 'Reduce dietary sodium, engage in 30 mins moderate aerobic exercise daily, avoid excessive caffeine.',
                    'follow_up_date' => $today->copy()->addDays(28)->toDateString(),
                ]
            );

            PrescriptionItem::updateOrCreate(
                [
                    'medical_record_id' => $record->id,
                    'medicine_name' => 'Amlodipine Besylate',
                ],
                [
                    'dosage' => '5 mg',
                    'frequency' => 'Once daily in morning',
                    'duration' => '30 days',
                    'instructions' => 'Take with water before breakfast. Monitor morning blood pressure.',
                ]
            );

            PrescriptionItem::updateOrCreate(
                [
                    'medical_record_id' => $record->id,
                    'medicine_name' => 'Metoprolol Tartrate',
                ],
                [
                    'dosage' => '25 mg',
                    'frequency' => 'Twice daily',
                    'duration' => '14 days',
                    'instructions' => 'Take after food. Do not skip doses.',
                ]
            );
        }

        // 2. Today's confirmed appointment
        if ($michael) {
            Appointment::updateOrCreate(
                ['appointment_number' => 'APT-'.$today->format('Ymd').'-002'],
                [
                    'patient_id' => $patient2->id,
                    'doctor_id' => $michael->id,
                    'department_id' => $michael->department_id,
                    'appointment_date' => $today->toDateString(),
                    'appointment_time' => '11:00:00',
                    'end_time' => '11:30:00',
                    'status' => 'confirmed',
                    'reason' => 'Annual wellness checkup and seasonal allergy evaluation.',
                    'notes' => 'Confirmed by clinic reception.',
                    'fee' => $michael->consultation_fee,
                    'payment_status' => 'unpaid',
                    'payment_method' => 'cash',
                ]
            );
        }

        // 3. Upcoming appointment next week
        if ($emily) {
            $nextDate = $today->copy()->addDays(2);
            // Ensure weekday
            if ($nextDate->isWeekend()) {
                $nextDate = $nextDate->next(Carbon::MONDAY);
            }

            Appointment::updateOrCreate(
                ['appointment_number' => 'APT-'.$nextDate->format('Ymd').'-003'],
                [
                    'patient_id' => $patient1->id,
                    'doctor_id' => $emily->id,
                    'department_id' => $emily->department_id,
                    'appointment_date' => $nextDate->toDateString(),
                    'appointment_time' => '14:00:00',
                    'end_time' => '14:30:00',
                    'status' => 'confirmed',
                    'reason' => 'Persistent rash on inner forearms and skin sensitivity.',
                    'notes' => 'Requested allergy skin patch test.',
                    'fee' => $emily->consultation_fee,
                    'payment_status' => 'unpaid',
                    'payment_method' => 'card',
                ]
            );
        }
    }
}
