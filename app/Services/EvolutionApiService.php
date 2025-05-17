<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class EvolutionApiService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = Config::get('services.evolution_api.url');
        $this->apiKey = Config::get('services.evolution_api.key');
    }

    /**
     * Create a new group in Evolution API
     *
     * @param string $instance The instance name
     * @param array $participants Array of participant phone numbers
     * @param string $description Group description
     * @return array
     */
    public function createGroup(string $instance, array $participants, string $description = '')
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $this->apiKey
        ])->post("{$this->baseUrl}/group/create/{$instance}", [
            'subject' => (object)[], // Empty object as per API specification
            'description' => $description,
            'participants' => $participants
        ]);

        return $response->json();
    }

    /**
     * Send a text message using Evolution API
     *
     * @param string $instance The instance name
     * @param string $number Recipient's phone number
     * @param string $text Message text content
     * @param array $options Optional message options
     * @return array
     */
    public function sendTextMessage(
        string $instance,
        string $number,
        string $text,
        array $options = []
    ) {
        $payload = [
            'number' => $number,
            'textMessage' => [
                'text' => $text
            ]
        ];

        // Add options if provided
        if (!empty($options)) {
            $payload['options'] = $options;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $this->apiKey
        ])->post("{$this->baseUrl}/message/sendText/{$instance}", $payload);

        return $response->json();
    }
} 