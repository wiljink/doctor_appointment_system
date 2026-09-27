<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cardiology = Department::where('slug', 'cardiology')->first();
        $pediatrics = Department::where('slug', 'pediatrics')->first();
        $dermatology = Department::where('slug', 'dermatology')->first();
        $orthopedics = Department::where('slug', 'orthopedics')->first();
        $general = Department::where('slug', 'general-medicine')->first();

        $doctorsData = [
            [
                'name' => 'Dr. Sarah Jenkins',
                'email' => 'doctor.sarah@clinic.com',
                'phone' => '+1 (555) 234-5678',
                'gender' => 'female',
                'department_id' => $cardiology?->id,
                'specialization' => 'Senior Consultant Cardiologist',
                'qualifications' => 'MD, FACC, FSCAI (Harvard Medical School)',
                'experience_years' => 14,
                'consultation_fee' => 85.00,
                'room_number' => 'Suite 301 - Heart Center',
                'bio' => 'Dr. Sarah Jenkins has over 14 years of clinical experience in non-invasive cardiology, preventive cardiac care, and clinical echocardiography.',
                'working_days' => [1, 2, 3, 4, 5], // Mon to Fri
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'slot_duration' => 30,
            ],
            [
                'name' => 'Dr. Michael Chang',
                'email' => 'doctor.michael@clinic.com',
                'phone' => '+1 (555) 345-6789',
                'gender' => 'male',
                'department_id' => $pediatrics?->id,
                'specialization' => 'Pediatrician & Child Development Specialist',
                'qualifications' => 'MBBS, DCH, FAAP (Johns Hopkins University)',
                'experience_years' => 10,
                'consultation_fee' => 65.00,
                'room_number' => 'Room 105 - Pediatric Wing',
                'bio' => 'Dedicated to infant health, child immunizations, developmental screening, and pediatric chronic illness management.',
                'working_days' => [1, 2, 3, 4, 6], // Mon, Tue, Wed, Thu, Sat
                'start_time' => '08:30:00',
                'end_time' => '16:30:00',
                'slot_duration' => 30,
            ],
            [
                'name' => 'Dr. Emily Martinez',
                'email' => 'doctor.emily@clinic.com',
                'phone' => '+1 (555) 456-7890',
                'gender' => 'female',
                'department_id' => $dermatology?->id,
                'specialization' => 'Consultant Dermatologist & Dermatosurgeon',
                'qualifications' => 'MD (Dermatology), Board Certified Dermatologist',
                'experience_years' => 8,
                'consultation_fee' => 75.00,
                'room_number' => 'Suite 204 - Skin & Laser Clinic',
                'bio' => 'Specializes in medical and aesthetic dermatology, acne, eczema, psoriasis, mole screenings, and skin rejuvenation treatments.',
                'working_days' => [1, 2, 4, 5], // Mon, Tue, Thu, Fri
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'slot_duration' => 30,
            ],
            [
                'name' => 'Dr. James Wilson',
                'email' => 'doctor.james@clinic.com',
                'phone' => '+1 (555) 567-8901',
                'gender' => 'male',
                'department_id' => $orthopedics?->id,
                'specialization' => 'Orthopedic Specialist & Sports Medicine',
                'qualifications' => 'MS (Orthopedics), FRCS (Edin)',
                'experience_years' => 16,
                'consultation_fee' => 95.00,
                'room_number' => 'Room 210 - Orthopedic Clinic',
                'bio' => 'Experienced surgeon focused on joint preservation, sports injury arthroscopy, spine health, and musculoskeletal rehabilitation.',
                'working_days' => [1, 3, 5], // Mon, Wed, Fri
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'slot_duration' => 45,
            ],
            [
                'name' => 'Dr. Priya Patel',
                'email' => 'doctor.priya@clinic.com',
                'phone' => '+1 (555) 678-9012',
                'gender' => 'female',
                'department_id' => $general?->id,
                'specialization' => 'Family Physician & Primary Care Specialist',
                'qualifications' => 'MBBS, MRCGP (London)',
                'experience_years' => 11,
                'consultation_fee' => 50.00,
                'room_number' => 'Room 101 - Primary Care Suite',
                'bio' => 'Provides holistic family healthcare, annual wellness physicals, lifestyle counseling, diabetes and hypertension management.',
                'working_days' => [1, 2, 3, 4, 5, 6], // Mon to Sat
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'slot_duration' => 30,
            ],
        ];

        foreach ($doctorsData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'doctor',
                    'phone' => $data['phone'],
                    'gender' => $data['gender'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $doctor = Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id' => $data['department_id'],
                    'specialization' => $data['specialization'],
                    'qualifications' => $data['qualifications'],
                    'experience_years' => $data['experience_years'],
                    'consultation_fee' => $data['consultation_fee'],
                    'room_number' => $data['room_number'],
                    'bio' => $data['bio'],
                    'is_available' => true,
                ]
            );

            // Add Schedules
            foreach ($data['working_days'] as $day) {
                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                        'slot_duration_minutes' => $data['slot_duration'],
                        'break_start_time' => '12:30:00',
                        'break_end_time' => '13:30:00',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
