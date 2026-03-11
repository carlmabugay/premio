<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleCollection
{
    public function __construct(
        private RewardRuleService $ruleService,
        private ApiKeyService $apiKeyService,
    ) {}

    public function handle(string $api_key): array
    {
        $key = $this->apiKeyService->fetchByApiKey($api_key);

        $rules = $this->ruleService->fetchAll($key->merchantId());

        return RewardRuleReadDTO::fromEntityCollection($rules);
    }
}
