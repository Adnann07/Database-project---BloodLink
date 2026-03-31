<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\HospitalProfile;
use Illuminate\Support\Facades\Hash;

class TestHospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test hospital user
        $hospitalUser = User::create([
            'name' => 'City General Hospital',
            'email' => 'hospital@bloodlink.com',
            'password' => Hash::make('password123'),
            'role' => 'hospital',
            'phone' => '+1-555-0123',
            'address' => '123 Medical Center Drive, Cityville, State 12345',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create hospital profile
        HospitalProfile::create([
            'user_id' => $hospitalUser->id,
            'hospital_name' => 'City General Hospital',
            'license_number' => 'HOSP-2024-001',
            'city' => 'Cityville',
        ]);

        $this->command->info('Test hospital created successfully!');
        $this->command->info('Email: hospital@bloodlink.com');
        $this->command->info('Password: password123');
    }
}
