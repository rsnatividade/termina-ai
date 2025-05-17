<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TerminationParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Get termination information by token
     *
     * @param string $token
     * @return JsonResponse
     */
    public function show(string $token): JsonResponse
    {
        $participant = TerminationParticipant::where('token', $token)->first();

        if (!$participant) {
            return response()->json([
                'error' => 'Invalid token'
            ], 404);
        }

        $termination = $participant->termination;

        return response()->json([
            'terminationId' => $termination->id,
            'chosenMessage' => $termination->chosen_message,
            'scenario' => $termination->scenario,
            'soundtrack' => $termination->soundtrack,
            'type' => $participant->type
        ]);
    }
} 