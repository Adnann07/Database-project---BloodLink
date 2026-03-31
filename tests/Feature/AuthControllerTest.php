<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_endpoint()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'donor',
            'blood_group' => 'O+',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'requires_verification',
            'email'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_user_registration_validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'role' => 'invalid-role'
        ];

        $response = $this->postJson('/api/register', $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'email', 
            'password',
            'role'
        ]);
    }

    public function test_user_login_endpoint()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_verified' => true
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'token',
            'user',
            'redirect_url'
        ]);
    }

    public function test_email_verification_endpoint()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verification_token' => '123456',
            'is_verified' => false
        ]);

        $verificationData = [
            'email' => 'test@example.com',
            'otp' => '123456'
        ];

        $response = $this->postJson('/api/verify-email', $verificationData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'token',
            'user',
            'redirect_url'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'is_verified' => true,
            'email_verified_at' => now()
        ]);
    }

    public function test_hospital_dashboard_endpoint_requires_authentication()
    {
        $response = $this->getJson('/api/hospital/dashboard');

        $response->assertStatus(401);
    }

    public function test_hospital_dashboard_endpoint_works_for_authenticated_hospital()
    {
        $hospital = User::factory()->create([
            'role' => 'hospital',
            'is_verified' => true
        ]);

        $response = $this->actingAs($hospital)
                        ->getJson('/api/hospital/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'stats',
            'recent_activities',
            'urgent_requests'
        ]);
    }
}
