<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Http\Services\AuthService;
use App\Http\Services\EmailVerificationService;

class AuthServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function test_user_registration_creates_user_and_sends_otp()
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

        $emailService = $this->mock(EmailVerificationService::class);
        $emailService->shouldReceive('sendOTP')
            ->once()
            ->with('test@example.com')
            ->andReturn(true);

        $authService = new AuthService($emailService);
        $response = $authService->register($userData);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'donor'
        ]);

        $this->assertDatabaseHas('donor_profiles', [
            'blood_group' => 'O+',
            'gender' => 'male'
        ]);

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['requires_verification']);
        $this->assertEquals('test@example.com', $responseData['email']);
    }

    public function test_user_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_verified' => true
        ]);

        $authService = new AuthService($this->mock(EmailVerificationService::class));
        $response = $authService->login([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('user', $responseData);
    }

    public function test_user_login_with_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_verified' => false
        ]);

        $authService = new AuthService($this->mock(EmailVerificationService::class));
        $response = $authService->login([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Please verify your email before logging in', $responseData['message']);
        $this->assertTrue($responseData['requires_verification']);
    }
}
