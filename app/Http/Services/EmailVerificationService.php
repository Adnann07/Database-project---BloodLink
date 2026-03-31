<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\OTPMail;

class EmailVerificationService
{
    /**
     * Generate and send OTP to user email
     */
    public function sendOTP($email)
    {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 15 minutes
        Cache::put("otp_{$email}", $otp, now()->addMinutes(15));
        
        // Send OTP email
        try {
            Mail::to($email)->send(new OTPMail($otp));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify OTP
     */
    public function verifyOTP($email, $otp)
    {
        $cachedOTP = Cache::get("otp_{$email}");
        
        if (!$cachedOTP) {
            return ['success' => false, 'message' => 'OTP expired or not found'];
        }
        
        if ($cachedOTP !== $otp) {
            return ['success' => false, 'message' => 'Invalid OTP'];
        }
        
        // Clear OTP after successful verification
        Cache::forget("otp_{$email}");
        
        return ['success' => true, 'message' => 'OTP verified successfully'];
    }
    
    /**
     * Resend OTP
     */
    public function resendOTP($email)
    {
        return $this->sendOTP($email);
    }
}
