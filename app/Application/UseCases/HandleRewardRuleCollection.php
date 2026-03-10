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
        $merchant_id = $this->apiKeyService->isKeyExists($api_key);

        $rules = $this->ruleService->fetchAll($merchant_id);

        return RewardRuleReadDTO::fromEntityCollection($rules);
    }
}
