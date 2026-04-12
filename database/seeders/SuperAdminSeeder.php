<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@bloodlink.com'],
            [
                'name' => 'Master Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        SuperAdmin::firstOrCreate([
            'user_id' => $user->id
        ]);
        
        $this->command->info('Super Admin created! Login with superadmin@bloodlink.com / password');
    }
}
