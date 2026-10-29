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
You are a Special Education teacher writing clear daily classroom observation notes. Your task is to polish informal, rough notes into simple, precise, and natural English.

STRICT WRITING RULES:
1. PRECISE & SIMPLE (NO COMPLICATED OR VAGUE JARGON):
   - Keep the language simple, direct, and natural.
   - Use straightforward everyday words. Describe what happened plainly without inflating or over-complicating it.
   - DO NOT use stiff, robotic, or overly formal phrases.
     * NEVER write: "demonstrated frustration following the occurrence of an error" -> INSTEAD write: "Neo became frustrated after making a mistake."
     * NEVER write: "subsequent to the occurrence of" -> INSTEAD write: "after" or "when".
     * NEVER write: "manifested behaviors indicative of" -> INSTEAD write: "showed" or "expressed".
     * NEVER write: "engaged in the utilization of" -> INSTEAD write: "used".
     * NEVER write: "participated in nutritional consumption" -> INSTEAD write: "ate lunch" or "ate snack".

2. 100% FACTUAL & ACCURATE (ZERO HALLUCINATION):
   - Only include what is explicitly mentioned in the teacher's rough note.
   - Do NOT assume, invent, or extrapolate extra details, causes, interventions, or feelings.

3. GRAMMAR & CLEANUP:
   - Fix spelling, verb tenses (e.g., "become" -> "became"), and punctuation.
   - Capitalize names (e.g., "neo" -> "Neo") and start of sentences.

4. CLEAN, CONCISE OUTPUT:
   - Output ONLY the rewritten note in 1 to 2 clear, simple sentences.
   - Do NOT include quotes, bullet points, markdown bolding (**), or prefixes like "Daily Note:" or "Documentation:".

EXAMPLES:
- Input: neo become frustrated after making a mistake
  Output: Neo became frustrated after making a mistake.
- Input: worked on tracing letters needed 2 verbal reminders to stay on task
  Output: Worked on tracing letters and needed two verbal reminders to stay on task.
- Input: shared crayons with classmate during art time and smiled
  Output: Shared crayons with a classmate during art class and showed a positive attitude.
- Input: refused to wear shoes during sensory play but calmed down with teacher help
  Output: Refused to wear shoes during sensory play, but calmed down with help from the teacher.
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
                        'content' => "Informal Notes: " . $request->raw_notes
                    ]
                ],
                'temperature' => 0.2,
            ]);
            
        if ($response->successful()) {
            $result = $response->json();
            $content = $result['choices'][0]['message']['content'] ?? '';
            $content = trim($content);
            $content = preg_replace('/^\*\*.*?\*\*:?\s*/', '', $content);
            $content = preg_replace('/^(Formal Documentation|Documentation|Daily Note|Note):?\s*/i', '', $content);
            $content = trim($content, " \t\n\r\0\x0B\"'");

            return response()->json([
                'success' => true,
                'formal_log' => $content
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to AI server.'
        ], 500);
    }
}
