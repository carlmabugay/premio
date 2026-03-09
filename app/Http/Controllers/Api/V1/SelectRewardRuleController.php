<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\HandleRewardRuleSelection;
use App\Http\Responses\RewardRuleSelectionResponse;
use Illuminate\Http\JsonResponse;

final class SelectRewardRuleController
{
    public function __invoke(string $id, HandleRewardRuleSelection $handler): JsonResponse
    {
        $result = $handler->handle($id);

        $response = new RewardRuleSelectionResponse($result);

        return $response->make();
    }
}
