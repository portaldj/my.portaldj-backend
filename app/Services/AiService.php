<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Send a chat message to the specified AI provider.
     *
     * @param string $provider 'openai' or 'gemini'
     * @param string $systemPrompt The instruction including the documentation
     * @param string $userMessage The user's question
     * @return string|null The AI response or null on failure
     */
    public function chat(string $provider, string $systemPrompt, string $userMessage, ?\App\Models\User $user = null): ?string
    {
        if ($provider === 'openai') {
            return $this->chatOpenAi($systemPrompt, $userMessage, $user);
        } elseif ($provider === 'gemini') {
            return $this->chatGemini($systemPrompt, $userMessage, $user);
        }

        return null;
    }

    protected function chatOpenAi(string $systemPrompt, string $userMessage, ?\App\Models\User $user = null): ?string
    {
        $apiKey = ($user && $user->openai_key) ? $user->openai_key : config('services.openai.api_key');

        if (!$apiKey) {
            Log::error('OpenAI API Key missing');
            return 'Error: OpenAI configuration missing. Please add your API Key in Profile Settings.';
        }

        try {
            $response = Http::withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo', // Cost-effective default
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('OpenAI API Error: ' . $response->body());
            return 'I am unable to reach the OpenAI service right now.';
        } catch (\Exception $e) {
            Log::error('OpenAI Exception: ' . $e->getMessage());
            return 'An error occurred while contacting OpenAI.';
        }
    }

    protected function chatGemini(string $systemPrompt, string $userMessage, ?\App\Models\User $user = null): ?string
    {
        // Use user key if available, otherwise fallback
        $apiKey = ($user && $user->gemini_key) ? $user->gemini_key : config('services.gemini.api_key');

        if (!$apiKey) {
            Log::error('Gemini API Key missing');
            return 'Error: Gemini configuration missing. Please add your API Key in Profile Settings.';
        }

        // Gemini doesn't use 'system' role in the same way as OpenAI in the basic API, 
        // but we can prepend it to the user message or use the 'system_instruction' field in newer APIs.
        // For simplicity and compatibility, we'll establish context in the first turn.
        // Wait, standard gemini-pro endpoint:

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiKey}";

            // Constructing a structured prompt
            $response = Http::post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nUser Question: " . $userMessage]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }

            Log::error('Gemini API Error: ' . $response->body());
            return 'I am unable to reach the Gemini service right now.';

        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return 'An error occurred while contacting Gemini.';
        }
    }
}
