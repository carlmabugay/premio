<?php

namespace App\Application\UseCases;

use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleModification
{
    public function __construct(
        private RewardRuleService $ruleService,
        private ApiKeyService $apiKeyService
    ) {}

    public function handle(string $api_key, array $data): RewardRule
    {
        $key = $this->apiKeyService->fetchByApiKey($api_key);

        return $this->ruleService->update($key->merchantId(), $data);
    }
}
