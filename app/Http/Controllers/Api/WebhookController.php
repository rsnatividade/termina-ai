<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Termination;
use App\Models\TerminationParticipant;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\EvolutionApiService;
use App\Services\AIService;

class WebhookController extends Controller
{
    protected EvolutionApiService $evolutionApi;
    protected AIService $aiService;

    public function __construct(EvolutionApiService $evolutionApi, AIService $aiService)
    {
        $this->evolutionApi = $evolutionApi;
        $this->aiService = $aiService;
    }

    /**
     * Handle Evolution API webhook events
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $event = $request->input('event');
            $data = $request->input('data');
            $instance = $request->input('instance');
            $dateTime = $request->input('date_time');

            Log::info('Received webhook event', [
                'event' => $event,
                'instance' => $instance,
                'data' => $data
            ]);

            switch ($event) {
                case 'group-participants.update':
                    $this->handleGroupParticipantsUpdate($data);
                    break;
                case 'messages.upsert':
                    $this->handleMessageUpsert($data);
                    break;
                default:
                    Log::info('Unhandled event type', ['event' => $event]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error processing webhook'
            ], 500);
        }
    }

    /**
     * Handle group participants update event
     *
     * @param array $data
     * @return void
     */
    protected function handleGroupParticipantsUpdate(array $data): void
    {
        $groupId = $data['id'];
        $action = $data['action'];
        $participants = $data['participants'];

        // Find termination by group ID
        $termination = Termination::where('group_id', $groupId)->first();

        if (!$termination) {
            Log::warning('Termination not found for group', ['group_id' => $groupId]);
            return;
        }

        foreach ($participants as $participantJid) {
            // Clean phone number (remove @s.whatsapp.net and any other non-digit characters)
            $cleanPhone = preg_replace('/[^0-9]/', '', $participantJid);

            if ($action === 'add') {
                // Add new participant if they don't exist
                if (!$termination->participants()->where('phone', $cleanPhone)->exists()) {
                    $cuid = Str::uuid();
                    $termination->participants()->create([
                        'phone' => $cleanPhone,
                        'participant_jid' => $participantJid,
                        'token' => $cuid,
                        'type' => 'auditorium'
                    ]);

                    Log::info('Added new participant to termination', [
                        'termination_id' => $termination->id,
                        'phone' => $cleanPhone,
                        'participant_jid' => $participantJid
                    ]);
                }
            } elseif ($action === 'remove') {
                // Remove participant
                $termination->participants()->where('phone', $cleanPhone)->delete();

                Log::info('Removed participant from termination', [
                    'termination_id' => $termination->id,
                    'phone' => $cleanPhone,
                    'participant_jid' => $participantJid
                ]);
            }
        }
    }

    /**
     * Handle message upsert event
     *
     * @param array $data
     * @return void
     */
    protected function handleMessageUpsert(array $data): void
    {
        $key = $data['key'];
        $message = $data['message'] ?? [];
        $messageType = $data['messageType'] ?? null;
        $pushName = $data['pushName'] ?? null;
        $messageTimestamp = $data['messageTimestamp'] ?? null;

        // Only process messages from groups
        if (!str_ends_with($key['remoteJid'], '@g.us')) {
            return;
        }

        // Find termination by group ID
        $termination = Termination::where('group_id', $key['remoteJid'])->first();

        if (!$termination) {
            Log::warning('Termination not found for group', ['group_id' => $key['remoteJid']]);
            return;
        }

        // Get participant using the full JID
        $participantJid = $key['participant'] ?? null;
        if (!$participantJid) {
            Log::warning('No participant JID found in message', ['message_id' => $key['id']]);
            return;
        }

        $participant = TerminationParticipant::where('participant_jid', $participantJid)->first();
        
        if (!$participant) {
            Log::warning('Participant not found', [
                'participant_jid' => $participantJid,
                'termination_id' => $termination->id
            ]);
            return;
        }

        $messageContent = $message['conversation'] ?? '';

        // Check for "vamos comecar" message from group owner
        if (strtolower($messageContent) === 'vamos comecar' || 
            levenshtein(strtolower($messageContent), 'vamos comecar') <= 3) {
            
            // Check if the message is from the group owner
            if ($participant->phone === $termination->owner_phone) {
                Log::info('Updating termination status to collecting_answers', [
                    'termination_id' => $termination->id,
                    'participant_id' => $participant->id
                ]);

                // Update termination status
                $termination->update(['status' => 'collecting_answers']);

                // Send initial message
                $this->evolutionApi->sendTextMessage(
                    $termination->group_id,
                    "Me conte o que aconteceu"
                );
            }
        }

        // Process messages if we're in collecting_answers status
        if ($termination->status === 'collecting_answers' && !$key['fromMe']) {
            // Get AI response
            $aiResponse = $this->aiService->getResponse($messageContent, [
                'termination_id' => $termination->id,
                'participant_name' => $pushName,
                'message_history' => $this->getMessageHistory($termination)
            ]);

            if ($aiResponse) {
                // Send AI response
                Log::info('Sending AI response', [
                    'termination_id' => $termination->id,
                    'ai_response' => $aiResponse
                ]);
                //$this->evolutionApi->sendTextMessage(
                //    $termination->group_id,
                //    $aiResponse
                //);
            }
        }

        // Store the message
        $messageData = [
            'termination_id' => $termination->id,
            'message_id' => $key['id'],
            'participant_id' => $participant->id,
            'sender_phone' => $participant->phone,
            'sender_name' => $pushName,
            'message_type' => $messageType,
            'content' => $messageContent,
            'sent_at' => date('Y-m-d H:i:s', $messageTimestamp),
            'status' => $data['status'] ?? null,
            'from_me' => $key['fromMe'] ?? false,
        ];

        // Always create a new message record
        Message::create($messageData);

        Log::info('Created new message record', [
            'termination_id' => $termination->id,
            'message_id' => $key['id'],
            'participant_id' => $participant->id,
            'participant_jid' => $participantJid
        ]);
    }

    /**
     * Get message history for context
     *
     * @param Termination $termination
     * @return array
     */
    protected function getMessageHistory(Termination $termination): array
    {
        return $termination->messages()
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($message) {
                return [
                    'sender' => $message->sender_name,
                    'content' => $message->content,
                    'timestamp' => $message->sent_at
                ];
            })
            ->toArray();
    }
} 