<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\HandleRewardRuleCollection;
use Illuminate\Http\JsonResponse;

class CollectRewardRuleController
{
    public function __invoke(HandleRewardRuleCollection $handler): JsonResponse
    {
        $result = $handler->handle();

        return response()->json([
            'data' => $result,
        ]);
    }
}
