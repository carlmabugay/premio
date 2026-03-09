<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleSelection
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(int $id): RewardRuleReadDTO
    {
        $rule = $this->service->fetchById($id);

        return RewardRuleReadDTO::fromEntity($rule);
    }
}
