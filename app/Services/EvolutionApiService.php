<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\Response;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instance;
    protected int $timeout;
    protected int $retryAttempts;
    protected int $retryDelay;

    public function __construct(?string $instance = null)
    {
        $this->baseUrl = Config::get('services.evolution_api.url');
        $this->apiKey = Config::get('services.evolution_api.key');
        $this->instance = $instance ?? Config::get('services.evolution_api.instance');
        $this->timeout = Config::get('services.evolution_api.timeout', 30);
        $this->retryAttempts = Config::get('services.evolution_api.retry_attempts', 3);
        $this->retryDelay = Config::get('services.evolution_api.retry_delay', 5);
    }

    /**
     * Create a new group in Evolution API
     *
     * @param array $participants Array of participant phone numbers
     * @param string $description Group description
     * @return array
     */
    public function createGroup(array $participants, string $description = ''): array
    {
        $response = $this->makeRequest()->post("{$this->baseUrl}/group/create/{$this->instance}", [
            'subject' => (object)[], // Empty object as per API specification
            'description' => $description,
            'participants' => $participants
        ]);

        return $response->json();
    }

    /**
     * Send a text message using Evolution API
     *
     * @param string $number Recipient's phone number
     * @param string $text Message text content
     * @param array $options Optional message options
     * @return array
     */
    public function sendTextMessage(
        string $number,
        string $text,
        array $options = []
    ): array {
        $payload = [
            'number' => $number,
            'textMessage' => [
                'text' => $text
            ]
        ];

        if (!empty($options)) {
            $payload['options'] = $options;
        }

        $response = $this->makeRequest()->post(
            "{$this->baseUrl}/message/sendText/{$this->instance}",
            $payload
        );

        return $response->json();
    }

    /**
     * Set the instance for subsequent requests
     */
    public function setInstance(string $instance): self
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * Get the current instance
     */
    public function getInstance(): string
    {
        return $this->instance;
    }

    /**
     * Create a configured HTTP client instance
     */
    protected function makeRequest()
    {
        return Http::timeout($this->timeout)
            ->retry($this->retryAttempts, $this->retryDelay)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $this->apiKey
            ]);
    }
} 