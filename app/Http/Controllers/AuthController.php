<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        try {
            $rules = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role'     => 'required|string|in:donor,hospital',
                'phone'    => 'nullable|regex:/^[\d\s\-\+\(\)]+$/|max:20',
                'address'  => 'nullable|string|max:500',
            ];

            if ($request->role === 'donor') {
                $rules['blood_group']   = 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-';
                $rules['date_of_birth'] = 'required|date|before:today';
                $rules['gender']        = 'required|string|in:male,female,other';
                $rules['weight_kg']     = 'nullable|numeric|min:30|max:300';
            }

            if ($request->role === 'hospital') {
                $rules['hospital_name']  = 'required|string|max:255';
                $rules['license_number'] = 'nullable|string|max:255';
                $rules['city']           = 'nullable|string|max:100';
            }

            $validated = $request->validate($rules);

            return $this->authService->register($validated);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Registration failed',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        return $this->authService->login($request->all());
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request->user());
    }

    public function me(Request $request)
    {
        return $this->authService->me($request->user());
    }
}