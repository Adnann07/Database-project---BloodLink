<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function sendContact(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ], [
            'name.required' => 'Please enter your name',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'subject.required' => 'Please enter a subject',
            'message.required' => 'Please enter your message',
            'message.max' => 'Message should not exceed 2000 characters'
        ]);

        try {
            Log::info('New contact form submission', [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'subject' => $validatedData['subject']
            ]);

            Mail::to('hasibshahriar04@gmail.com')->send(new ContactFormMail($validatedData));

            Log::info('Contact email sent successfully to admin', [
                'recipient' => 'hasibshahriar04@gmail.com',
                'sender' => $validatedData['email']
            ]);

            return response()->json([
                'message' => 'Message sent successfully! We will get back to you soon.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to send contact email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validatedData
            ]);

            return response()->json([
                'message' => 'Failed to send message. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Email service error'
            ], 500);
        }
    }
}
