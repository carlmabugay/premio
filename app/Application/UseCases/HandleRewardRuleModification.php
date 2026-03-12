<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleModification
{
    public function __construct(
        private RewardRuleService $ruleService,
        private ApiKeyService $apiKeyService
    ) {}

    public function handle(string $api_key, array $data): RewardRuleReadDTO
    {
        $key = $this->apiKeyService->fetchByApiKey($api_key);

        $rule = $this->ruleService->update($key->merchantId(), $data);

        return RewardRuleReadDTO::fromEntity($rule);
    }
}
