<?php

namespace App\Application\UseCases;

use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleSelection
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(int $id): RewardRule
    {
        return $this->service->fetchById($id);
    }
}
