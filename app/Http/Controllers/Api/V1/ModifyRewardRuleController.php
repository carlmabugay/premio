<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\HandleRewardRuleModification;
use App\Http\Requests\ModifyRewardRuleRequest;
use App\Http\Responses\RewardRuleModificationResponse;
use Illuminate\Http\JsonResponse;

final class ModifyRewardRuleController
{
    public function __invoke(ModifyRewardRuleRequest $request, HandleRewardRuleModification $handler): JsonResponse
    {
        $api_key = $request->header('X-API-KEY');

        $result = $handler->handle($api_key, $request->validated());

        $response = new RewardRuleModificationResponse($result);

        return $response->make();
    }
}
