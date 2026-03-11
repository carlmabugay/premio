<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Application\DTOs\Write\RewardRuleCreateDTO;
use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleCreation
{
    public function __construct(
        private RewardRuleService $ruleService,
        private ApiKeyService $apiKeyService,
    ) {}

    public function handle(string $api_key, RewardRuleCreateDTO $dto): RewardRuleReadDTO
    {
        $key = $this->apiKeyService->fetchByApiKey($api_key);

        $rule = new RewardRule(
            id: null,
            merchant_id: $key->merchantId(),
            name: $dto->name,
            event_type: $dto->event_type,
            reward_type: $dto->reward_type,
            reward_value: $dto->reward_value,
            is_active: $dto->is_active,
            starts_at: $dto->starts_at,
            ends_at: $dto->ends_at,
            priority: $dto->priority,
        );

        $savedRule = $this->ruleService->save($rule);

        return RewardRuleReadDTO::fromEntity($savedRule);
    }
}
