<?php

namespace App\Http\Responses;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use Illuminate\Http\JsonResponse;

readonly class RewardRuleSelectionResponse
{
    public function __construct(private RewardRuleReadDTO $dto) {}

    public function make(): JsonResponse
    {
        return response()->json([
            'data' => $this->dto,
        ]);
    }
}
