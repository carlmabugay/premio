<?php

namespace App\Application\UseCases;

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleCollection
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(): array
    {
        $rules = $this->service->fetchAll();

        return RewardRuleReadDTO::fromEntityCollection($rules);
    }
}
