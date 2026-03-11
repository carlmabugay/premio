<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleSelection
{
    public function __construct(
        private RewardRuleService $ruleService,
        private ApiKeyService $apiKeyService,
    ) {}

    public function handle(string $api_key, int $id): RewardRuleReadDTO
    {

        $key = $this->apiKeyService->fetchByApiKey($api_key);

        $rule = $this->ruleService->fetchById($key->merchantId(), $id);

        return RewardRuleReadDTO::fromEntity($rule);
    }
}
