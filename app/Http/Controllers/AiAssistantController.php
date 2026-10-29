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
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        $systemPrompt = <<<PROMPT
You are an expert Special Education Teaching Assistant. Rewrite the following rough notes into a professional, clear, compassionate, and factual daily log entry for a parent.

STRICT STANDARDS:
1. ZERO HALLUCINATIONS: Ground your rewrite 100% in the provided rough notes. Do NOT assume, extrapolate, or invent details, emotions, or activities not explicitly stated.
2. PRECISE & SPECIFIC: Accurately convey student progress, activities, and support levels.
3. CLEAR & SUSTAINABLE: Keep the language accessible, positive, and free of confusing clinical jargon.
4. FORMAT: Output ONLY the rewritten note (1-3 sentences). Do NOT use markdown asterisks (**) or conversational meta-commentary.
PROMPT;

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DALC-System/1.0',
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model, 
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user', 
                        'content' => $request->raw_notes
                    ]
                ],
                'temperature' => 0.2,
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
