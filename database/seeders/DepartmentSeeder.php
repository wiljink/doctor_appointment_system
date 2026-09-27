<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Cardiology',
                'slug' => 'cardiology',
                'description' => 'Comprehensive cardiac care, ECG, echocardiography, hypertension and preventative heart health.',
                'icon' => 'heart-pulse',
                'is_active' => true,
            ],
            [
                'name' => 'Pediatrics',
                'slug' => 'pediatrics',
                'description' => 'Compassionate medical care for infants, children, and adolescents, including immunizations and milestones.',
                'icon' => 'baby',
                'is_active' => true,
            ],
            [
                'name' => 'Dermatology',
                'slug' => 'dermatology',
                'description' => 'Expert diagnosis and therapy for conditions affecting skin, hair, nails, and cosmetic wellness.',
                'icon' => 'sparkles',
                'is_active' => true,
            ],
            [
                'name' => 'Orthopedics',
                'slug' => 'orthopedics',
                'description' => 'Bone and joint care, treatment of fractures, arthritis, sports injuries, and rehabilitation.',
                'icon' => 'bone',
                'is_active' => true,
            ],
            [
                'name' => 'General Medicine',
                'slug' => 'general-medicine',
                'description' => 'Primary health assessments, preventative health checkups, chronic disease management, and acute care.',
                'icon' => 'stethoscope',
                'is_active' => true,
            ],
            [
                'name' => 'Neurology',
                'slug' => 'neurology',
                'description' => 'Specialized care for neurological disorders, migraines, seizures, neuropathy, and stroke recovery.',
                'icon' => 'brain',
                'is_active' => true,
            ],
            [
                'name' => 'Dentistry',
                'slug' => 'dentistry',
                'description' => 'Full dental examinations, cleanings, root canal therapy, smile design, and restorative oral care.',
                'icon' => 'smile',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['slug' => $dept['slug']], $dept);
        }
    }
}
