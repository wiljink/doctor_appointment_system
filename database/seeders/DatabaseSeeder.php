<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin account
        User::updateOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'name' => 'Clinic Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+1 (555) 000-1122',
                'gender' => 'other',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Child Seeders
        $this->call([
            DepartmentSeeder::class,
            DoctorSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
