<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class RewardRuleModificationResponse
{
    public function make(): JsonResponse
    {
        return response()->json([
            'status' => 'updated',
        ], 204);
    }
}
