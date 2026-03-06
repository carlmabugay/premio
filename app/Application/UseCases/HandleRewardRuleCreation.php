<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Application\DTOs\Write\RewardRuleCreateDTO;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleCreation
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(RewardRuleCreateDTO $dto): RewardRuleReadDTO
    {
        $rule = new RewardRule(
            id: null,
            merchant_id: $dto->merchant_id,
            name: $dto->name,
            event_type: $dto->event_type,
            reward_type: $dto->reward_type,
            reward_value: $dto->reward_value,
            is_active: $dto->is_active,
            starts_at: $dto->starts_at,
            ends_at: $dto->ends_at,
            priority: $dto->priority,
        );

        $savedRule = $this->service->save($rule);

        return RewardRuleReadDTO::fromEntity($savedRule);
    }
}
