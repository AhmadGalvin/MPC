<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Enums\UserRole;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of doctors data
        $doctors = [
            [
                'user' => [
                    'name' => 'Dr. John Smith',
                    'email' => 'john.smith@example.com',
                    'password' => bcrypt('password'),
                    'role' => UserRole::DOCTOR,
                ],
                'doctor' => [
                    'specialization' => 'Small Animal Medicine',
                    'sip_number' => 'SIP-001-2024',
                    'consultation_fee' => 100000,
                    'is_available_for_consultation' => false,
                    'schedule' => [
                        [
                            'day' => 'monday',
                            'start_time' => '09:00',
                            'end_time' => '17:00'
                        ],
                        [
                            'day' => 'wednesday',
                            'start_time' => '09:00',
                            'end_time' => '17:00'
                        ],
                        [
                            'day' => 'friday',
                            'start_time' => '09:00',
                            'end_time' => '17:00'
                        ]
                    ]
                ]
            ],
            [
                'user' => [
                    'name' => 'Dr. Sarah Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'password' => bcrypt('password'),
                    'role' => UserRole::DOCTOR,
                ],
                'doctor' => [
                    'specialization' => 'Surgery',
                    'sip_number' => 'SIP-002-2024',
                    'consultation_fee' => 100000,
                    'is_available_for_consultation' => false,
                    'schedule' => [
                        [
                            'day' => 'tuesday',
                            'start_time' => '08:00',
                            'end_time' => '16:00'
                        ],
                        [
                            'day' => 'thursday',
                            'start_time' => '08:00',
                            'end_time' => '16:00'
                        ],
                        [
                            'day' => 'saturday',
                            'start_time' => '09:00',
                            'end_time' => '14:00'
                        ]
                    ]
                ]
            ],
            [
                'user' => [
                    'name' => 'Dr. Michael Chen',
                    'email' => 'michael.chen@example.com',
                    'password' => bcrypt('password'),
                    'role' => UserRole::DOCTOR,
                ],
                'doctor' => [
                    'specialization' => 'Internal Medicine',
                    'sip_number' => 'SIP-003-2024',
                    'consultation_fee' => 100000,
                    'is_available_for_consultation' => false,
                    'schedule' => [
                        [
                            'day' => 'monday',
                            'start_time' => '14:00',
                            'end_time' => '22:00'
                        ],
                        [
                            'day' => 'wednesday',
                            'start_time' => '14:00',
                            'end_time' => '22:00'
                        ],
                        [
                            'day' => 'friday',
                            'start_time' => '14:00',
                            'end_time' => '22:00'
                        ]
                    ]
                ]
            ],
        ];

        // Create doctors
        foreach ($doctors as $doctorData) {
            // Create user
            $user = User::create($doctorData['user']);

            // Create doctor profile
            $doctorData['doctor']['user_id'] = $user->id;
            Doctor::create($doctorData['doctor']);
        }
    }
} 