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

        // Hit the OpenAI-compatible Groq endpoint
        $response = Http::withToken(config('services.groq.api_key'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                
                'model' => 'llama-3.3-70b-versatile', 
                
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