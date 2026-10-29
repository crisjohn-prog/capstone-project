<?php

namespace Database\Seeders;

use App\Models\ServiceRecord;
use App\Models\ServiceRequest;
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
        // User::factory(10)->create();

        foreach ([
            ['AirCare Administrator', 'admin@aircare.test', 'admin', 'System administration'],
            ['Maria Santos', 'staff@aircare.test', 'staff', 'Operations coordinator'],
            ['Juan Dela Cruz', 'tech1@aircare.test', 'technician', 'Repair and diagnostics'],
            ['Ana Reyes', 'tech2@aircare.test', 'technician', 'Installation and maintenance'],
            ['Alex Customer', 'customer@aircare.test', 'customer', null],
        ] as [$name, $email, $role, $specialty]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name, 'role' => $role, 'specialty' => $specialty,
                'phone' => '09170000000', 'password' => Hash::make('password'),
            ]);
        }
        User::firstOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $customer = User::where('email', 'customer@aircare.test')->firstOrFail();
        $technician = User::where('email', 'tech1@aircare.test')->firstOrFail();
        $samples = [
            ['AC-DEMO-1001', 'Repair', 'pending', null, 'Water is dripping from the indoor unit during operation.', null],
            ['AC-DEMO-1002', 'Cleaning', 'scheduled', $technician->id, 'Routine cleaning; airflow has weakened over the last few weeks.', now()->addDays(1)->setTime(9, 0)],
            ['AC-DEMO-1003', 'Maintenance', 'in_progress', $technician->id, 'Annual preventive maintenance visit for two split-type units.', now()->addHours(1)],
            ['AC-DEMO-1004', 'Repair', 'completed', $technician->id, 'Unit runs but does not cool the room consistently.', now()->subDays(3)->setTime(14, 0)],
        ];
        foreach ($samples as [$reference, $serviceType, $status, $techId, $description, $scheduledAt]) {
            $item = ServiceRequest::updateOrCreate(['reference' => $reference], [
                'customer_id' => $customer->id, 'technician_id' => $techId, 'service_type' => $serviceType,
                'unit_brand' => 'Daikin', 'unit_model' => 'FTKF25', 'unit_location' => 'Living room',
                'service_address' => 'Quezon City, Metro Manila', 'issue_description' => $description,
                'preferred_at' => now()->addDays(2), 'scheduled_at' => $scheduledAt,
                'status' => $status, 'priority' => 'normal',
            ]);
            if ($status === 'completed') {
                ServiceRecord::updateOrCreate(['service_request_id' => $item->id], [
                    'technician_id' => $technician->id,
                    'diagnosis' => 'The air filter was clogged and the evaporator coil needed cleaning.',
                    'work_performed' => 'Cleaned filter and evaporator coil, checked drainage, and confirmed cooling operation.',
                    'parts_used' => 'None', 'recommendations' => 'Clean the filter every month during heavy use.',
                    'completed_at' => now()->subDays(3)->setTime(15, 30),
                ]);
            }
        }
    }
}
