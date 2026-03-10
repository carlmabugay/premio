<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\HandleRewardRuleCollection;
use App\Http\Responses\RewardRuleCollectionResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CollectRewardRuleController
{
    public function __invoke(Request $request, HandleRewardRuleCollection $handler): JsonResponse
    {
        $api_key = $request->header('X-API-KEY');

        $result = $handler->handle($api_key);

        $response = new RewardRuleCollectionResponse($result);

        return $response->make();
    }
}
