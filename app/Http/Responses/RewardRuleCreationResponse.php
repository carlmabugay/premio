<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class RewardRuleCreationResponse
{
    public function make(): JsonResponse
    {
        return response()->json([
            'status' => 'processed',
        ], 200);
    }
}
