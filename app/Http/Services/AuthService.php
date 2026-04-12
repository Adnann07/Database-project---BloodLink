<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\DonorProfile;
use App\Models\HospitalProfile;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => $data['role'],
            'phone'             => $data['phone'] ?? null,
            'address'           => $data['address'] ?? null,
            'is_verified'       => true,
            'email_verified_at' => now(),
        ]);

        if ($user->role === 'donor') {
            DonorProfile::create([
                'user_id'       => $user->id,
                'blood_group'   => $data['blood_group'],
                'date_of_birth' => $data['date_of_birth'],
                'gender'        => $data['gender'],
                'weight_kg'     => $data['weight_kg'] ?? null,
            ]);
        }

        if ($user->role === 'hospital') {
            HospitalProfile::create([
                'user_id'        => $user->id,
                'hospital_name'  => $data['hospital_name'],
                'license_number' => $data['license_number'] ?? null,
                'city'           => $data['city'] ?? null,
            ]);
        }

        if ($user->role === 'admin') {
            \App\Models\AdminProfile::create([
                'user_id' => $user->id,
                'status'  => 'pending',
            ]);

            return response()->json([
                'message' => 'Registration successful! Your account is pending Super Admin verification.',
                'is_admin_pending' => true,
                'user'    => $user,
            ], 201);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful!',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($user->role === 'admin') {
            $user->load('adminProfile');
            if ($user->adminProfile && $user->adminProfile->status !== 'approved') {
                return response()->json([
                    'message' => 'Your account is still pending Super Admin verification.',
                    'is_admin_pending' => true,
                ], 403);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $redirectUrl = '/dashboard';
        if ($user->role === 'hospital') $redirectUrl = '/hospital/dashboard';
        if ($user->role === 'admin') $redirectUrl = '/admin/dashboard';
        if ($user->role === 'super_admin') $redirectUrl = '/super-admin-dashboard';

        return response()->json([
            'message'      => 'Login successful',
            'token'        => $token,
            'user'         => $user,
            'redirect_url' => $redirectUrl
        ]);
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function me($user)
    {
        if ($user->role === 'donor') {
            $user->load('donorProfile');
        } elseif ($user->role === 'hospital') {
            $user->load('hospitalProfile');
        }

        return response()->json($user);
    }
}