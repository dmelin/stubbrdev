<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DanielController extends Controller
{
    public function chat(Request $request)
    {
        // Handle CORS preflight
        if ($request->isMethod('OPTIONS')) {
            return $this->corsResponse();
        }

        // Check authentication header
        if ($request->header('X-Talk-To') !== 'daniel') {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Get the incoming contents
        $contents = $request->input('contents');

        if (!$contents) {
            return response()->json(['error' => 'Missing contents'], 400);
        }

        // Get environment variables
        $geminiApiKey = env('GEMINI_API_KEY');
        $systemPrompt = env('DANIEL_SYSTEM_PROMPT', '');

        if (!$geminiApiKey) {
            Log::error('GEMINI_API_KEY not configured');
            return response()->json(['error' => 'Service configuration error'], 502);
        }

        // Build the Gemini API request payload
        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $contents
        ];

        try {
            // Call Gemini API
            $response = Http::timeout(30)
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$geminiApiKey}",
                    $payload
                );

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Upstream service error'], 502);
            }

            // Return Gemini's response with CORS headers
            return $this->corsResponse($response->json());

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Service unavailable'], 502);
        }
    }

    private function corsResponse($data = null)
    {
        $response = response()->json($data ?? ['status' => 'ok']);

        return $response
            ->header('Access-Control-Allow-Origin', request()->header('Origin', '*'))
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Talk-To')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
