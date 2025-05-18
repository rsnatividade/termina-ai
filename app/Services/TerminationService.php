<?php

namespace App\Services;

use App\Models\Termination;
use App\Models\TerminationParticipant;
use Illuminate\Support\Str;

class TerminationService
{
    protected $evolutionApi;

    public function __construct(EvolutionApiService $evolutionApi)
    {
        $this->evolutionApi = $evolutionApi;
    }

    /**
     * Create a new termination with the owner
     *
     * @param string $name
     * @param string $phone
     * @return Termination
     */
    public function createTermination(string $name, string $phone): Termination
    {
        // Clean phone number (remove non-digit characters)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Create termination
        $termination = Termination::create([
            'owner_phone' => $cleanPhone,
            'status' => 'waiting_friends',
            'scenario' => 'cemiterio_amor',
            'soundtrack' => 'fim_de_nos_dois'
        ]);

        // Create owner as participant
        $termination->participants()->create([
            'phone' => $cleanPhone,
            'name' => $name,
            'token' => uniqid(),
            'type' => 'terminator'
        ]);

        return $termination;
    }

    /**
     * Create WhatsApp group and send invitation
     *
     * @param Termination $termination
     * @return void
     */
    public function createAndInviteToGroup(Termination $termination): void
    {
        // Create group
        //dd($termination->participants()->first());
        $groupResponse = $this->evolutionApi->createGroup(
            participants: [$termination->owner_phone],
            subject: "Grupo de Termino de Relacionamento do {$termination->participants->first()->name}",
            description: "Grupo destinado a ajudar o {$termination->participants->first()->name} a terminar o relacionamento de forma segura"
        );

        //dd($groupResponse);
        if (isset($groupResponse['id'])) {
            // Get group invite link
            $inviteResponse = $this->evolutionApi->getGroupInviteLink(
                groupJid: $groupResponse['id']
            );

            $groupLink = $inviteResponse['inviteUrl'] ?? null;
            
            // Update termination with group information
            $termination->update([
                'group_id' => $groupResponse['id'],
                'group_link' => $groupLink
            ]);

            // Update group image
            try {
                $this->evolutionApi->updateGroupImage(
                    groupJid: $groupResponse['id']
                );
            } catch (\Exception $e) {
                // Log the error but continue with the process
                \Log::error('Failed to update group image: ' . $e->getMessage());
            }

            // Send group link to owner
            if ($groupLink) {
                $this->evolutionApi->sendTextMessage(
                    number: $termination->owner_phone,
                    text: "Aqui está o link, para convidar outras pessoas, do grupo de termino de relacionamento: {$groupLink}"
                );

                $this->evolutionApi->sendTextMessage(
                    number: $groupResponse['id'],
                    text: "{$termination->participants->first()->name}: Quando estiver pronto, mande um 'Vamos começar'"
                );
            }
        }
    }
} 