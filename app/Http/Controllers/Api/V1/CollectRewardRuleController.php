<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\HandleRewardRuleCollection;
use App\Http\Responses\RewardRuleCollectionResponse;
use Illuminate\Http\JsonResponse;

final class CollectRewardRuleController
{
    public function __invoke(HandleRewardRuleCollection $handler): JsonResponse
    {
        $result = $handler->handle();

        $response = new RewardRuleCollectionResponse($result);

        return $response->make();
    }
}
