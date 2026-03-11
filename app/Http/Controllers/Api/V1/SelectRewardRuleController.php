<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\HandleRewardRuleSelection;
use App\Http\Responses\RewardRuleSelectionResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectRewardRuleController
{
    public function __invoke(Request $request, string $id, HandleRewardRuleSelection $handler): JsonResponse
    {
        $api_key = $request->header('X-API-KEY');

        $result = $handler->handle($api_key, $id);

        $response = new RewardRuleSelectionResponse($result);

        return $response->make();
    }
}
