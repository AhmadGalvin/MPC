<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Pet;
use App\Models\Clinic;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some doctors
        $doctors = User::where('role', 'doctor')->get();
        
        // Get some owners
        $owners = User::where('role', 'owner')->get();
        
        // Get clinic
        $clinic = Clinic::first();

        // Get some pets
        $pets = Pet::all();

        if ($doctors->isEmpty() || $owners->isEmpty() || !$clinic || $pets->isEmpty()) {
            echo "Please seed doctors, owners, clinic, and pets first.\n";
            return;
        }

        // Appointment types
        $types = ['checkup', 'vaccination', 'surgery', 'dental', 'grooming'];
        
        // Status types
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

        // Create appointments for the next 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            
            // Create 3-5 appointments per day
            $appointmentsPerDay = rand(3, 5);
            
            for ($j = 0; $j < $appointmentsPerDay; $j++) {
                $doctor = $doctors->random();
                $owner = $owners->random();
                $pet = $pets->where('owner_id', $owner->id)->first() ?? $pets->random();
                $status = $statuses[array_rand($statuses)];
                
                // Generate random time between 9 AM and 5 PM
                $hour = rand(9, 16);
                $minute = ['00', '30'][rand(0, 1)];
                $time = sprintf("%02d:%s", $hour, $minute);

                $appointment = Appointment::create([
                    'doctor_id' => $doctor->id,
                    'pet_id' => $pet->id,
                    'owner_id' => $owner->id,
                    'clinic_id' => $clinic->id,
                    'scheduled_date' => $date,
                    'scheduled_time' => $time,
                    'status' => $status,
                    'type' => $types[array_rand($types)],
                    'notes' => 'Sample appointment notes',
                    'fee' => rand(50, 500) . '.00',
                ]);

                // Add completed_at for completed appointments
                if ($status === 'completed') {
                    $appointment->update([
                        'completed_at' => Carbon::parse($date . ' ' . $time)->addHours(1)
                    ]);
                }
                
                // Add cancelled_at and reason for cancelled appointments
                if ($status === 'cancelled') {
                    $appointment->update([
                        'cancelled_at' => Carbon::parse($date . ' ' . $time)->subHours(2),
                        'cancellation_reason' => 'Schedule conflict'
                    ]);
                }
            }
        }
    }
} 