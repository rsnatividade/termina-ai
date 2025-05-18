<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TerminationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TerminationController extends Controller
{
    protected $terminationService;

    public function __construct(TerminationService $terminationService)
    {
        $this->terminationService = $terminationService;
    }

    /**
     * Start a new termination process
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function start(Request $request): JsonResponse
    {
        // Validate request
        //melhoar o validation remove caracteres especiais
        $validator = Validator::make($request->all(), [
            'nameTerminator' => 'required|string|max:255',
            'phoneTerminator' => 'required|string|max:20',
            'nameTerminated' => 'required|string|max:255',
            'phoneTerminated' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create termination
            $termination = $this->terminationService->createTermination(
                name: $request->input('nameTerminator'),
                phone: $request->input('phoneTerminator')
            );

            $cuid = uniqid();
            $termination->participants()->create([
                'name' => $request->input('nameTerminated'),
                'phone' => $request->input('phoneTerminated'),
                'token' => $cuid,
                'type' => 'terminated'
            ]);

            // Create group and send invitation
            //@todo descomentar antes de por pra rodar pra valer
            $this->terminationService->createAndInviteToGroup($termination);

            return response()->json([
                'success' => true,
                'data' => [
                    'termination_id' => $termination->id,
                    'status' => $termination->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create termination',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 