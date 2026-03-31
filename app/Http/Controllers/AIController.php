<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $apiKey = env('GEMINI_API_KEY');
        $userMessage = $request->input('message');

        try {
            // Using v1beta/gemini-1.5-flash which is standard for AI Studio keys in early 2026
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
            
            $response = Http::timeout(15)->post($url, [
                'contents' => [
                    ['parts' => [['text' => "You are a blood donation assistant. Answer user: {$userMessage} Lawyers: 1. Bangla/English. 2. Concise."]]]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json(['text' => $data['candidates'][0]['content']['parts'][0]['text'] ?? "Error: Empty AI response."]);
            }

            return response()->json(['error' => 'Model failure', 'details' => $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server connection error', 'details' => $e->getMessage()], 500);
        }
    }
}
