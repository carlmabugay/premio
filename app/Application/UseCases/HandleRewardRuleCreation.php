<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateRewardRuleDTO;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleCreation
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(CreateRewardRuleDTO $dto): void
    {
        $rule = new RewardRule(
            id: 1,
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

        $this->service->save($rule);

    }
}
