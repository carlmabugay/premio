<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\CreateRewardRuleDTO;
use App\Application\UseCases\HandleRewardRuleCreation;
use App\Http\Requests\CreateRewardRuleRequest;
use App\Http\Responses\RewardRuleCreationResponse;
use Illuminate\Http\JsonResponse;

class RewardRuleController
{
    public function __construct() {}

    public function __invoke(CreateRewardRuleRequest $request, HandleRewardRuleCreation $handler): JsonResponse
    {
        $dto = CreateRewardRuleDTO::fromArray($request->validated());

        $handler->handle($dto);

        $response = new RewardRuleCreationResponse;

        return $response->make();
    }
}
