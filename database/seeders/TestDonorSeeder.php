<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DonorProfile;
use Illuminate\Support\Facades\Hash;

class TestDonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test donor user
        $donorUser = User::create([
            'name' => 'John Doe',
            'email' => 'donor@bloodlink.com',
            'password' => Hash::make('password123'),
            'role' => 'donor',
            'phone' => '+1-555-0123',
            'address' => '123 Main Street, Cityville, State 12345',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create donor profile
        DonorProfile::create([
            'user_id' => $donorUser->id,
            'blood_group' => 'O+',
            'date_of_birth' => '1990-01-15',
            'gender' => 'male',
            'weight_kg' => 75,
        ]);

        $this->command->info('Test donor created successfully!');
        $this->command->info('Email: donor@bloodlink.com');
        $this->command->info('Password: password123');
        $this->command->info('Blood Group: O+');
        $this->command->info('Date of Birth: 1990-01-15');
        $this->command->info('Gender: male');
        $this->command->info('Weight: 75kg');
    }
}
