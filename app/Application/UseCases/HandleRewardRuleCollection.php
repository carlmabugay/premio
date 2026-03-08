<?php

namespace App\Application\UseCases;

use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleCollection
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(): array
    {
        return $this->service->fetchAll();
    }
}
