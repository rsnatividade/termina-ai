<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;
    protected int $maxTokens;
    protected float $temperature;

    public function __construct()
    {
        $this->apiKey = Config::get('services.openai.key');
        $this->apiUrl = Config::get('services.openai.url', 'https://api.openai.com/v1/chat/completions');
        $this->model = Config::get('services.openai.model', 'gpt-4o');
        $this->maxTokens = Config::get('services.openai.max_tokens', 1000);
        $this->temperature = Config::get('services.openai.temperature', 0.7);
    }

    /**
     * Send a prompt to ChatGPT and get the response
     *
     * @param string $prompt
     * @param array $context Additional context for the AI
     * @return string|null
     */
    public function getResponse(string $prompt, array $context = []): ?string
    {
        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Você é um assistente especializado em ajudar a conduzir processos 
                    de termino de relacionamento da forma mais humoristica e sarcastica possivel. 
                    Sua função é coletar informações sobre o motivo do termino e quando estiver 
                    satisfeita com as respostas, deve criar 5 opcoes de mensagens sarcasticas e humoradas .'
                ]
            ];

            // Add context if provided
            if (!empty($context)) {
                $messages[] = [
                    'role' => 'system',
                    'content' => 'Contexto adicional: ' . json_encode($context)
                ];
            }

            // Add the user's prompt
            $messages[] = [
                'role' => 'user',
                'content' => $prompt
            ];
            Log::info('Sending request to OpenAI', [
                'messages' => $messages
            ]);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'] ?? null;
            }

            Log::error('Error getting AI response', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception in AI service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
} 