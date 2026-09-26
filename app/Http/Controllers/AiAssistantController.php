<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function rewriteDailyLog(Request $request)
    {
        $request->validate([
            'raw_notes' => 'required|string|max:1000'
        ]);

        $apiKey = trim(config('services.groq.api_key') ?? env('GROQ_API_KEY'), '"\' ');

        // Hit the OpenAI-compatible Groq endpoint
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DALC-System/1.0',
            'Content-Type' => 'application/json',
        ])
        ->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'openai/gpt-oss-20b', 
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional Special Education teaching assistant. Rewrite the following rough notes into a professional, clear, and compassionate daily log entry for a parent. Keep it concise.'
                ],
                [
                    'role' => 'user', 
                    'content' => $request->raw_notes
                ]
            ],
            'temperature' => 0.7,
        ]);
            
        if ($response->successful()) {
            $result = $response->json();
            return response()->json([
                'success' => true,
                'formal_log' => $result['choices'][0]['message']['content'] ?? ''
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to AI server.'
        ], 500);
    }
}