<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\Write\RewardRuleCreateDTO;
use App\Application\UseCases\HandleRewardRuleCreation;
use App\Http\Requests\CreateRewardRuleRequest;
use App\Http\Responses\RewardRuleCreationResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class RewardRuleController
{
    /**
     * @throws Exception
     */
    public function __invoke(CreateRewardRuleRequest $request, HandleRewardRuleCreation $handler): JsonResponse
    {
        $dto = RewardRuleCreateDTO::fromArray($request->validated());

        $result = $handler->handle($dto);

        $response = new RewardRuleCreationResponse($result);

        return $response->make();
    }
}
