<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class RewardRuleCollectionResponse
{
    public function __construct(private array $data) {}

    public function make(): JsonResponse
    {
        return response()->json([
            'data' => $this->data,
        ]);
    }
}
