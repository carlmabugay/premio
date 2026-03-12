<?php

namespace App\Http\Responses;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use Illuminate\Http\JsonResponse;

readonly class RewardRuleCreationResponse
{
    public function __construct(private RewardRuleReadDTO $dto) {}

    public function make(): JsonResponse
    {
        return response()->json([
            'status' => 'created',
            'id' => $this->dto->id,
        ], 201);
    }
}
