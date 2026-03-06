<?php

namespace App\Domain\Rewards\Services;

use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;

readonly class RewardRuleService
{
    public function __construct(
        private RewardRuleRepositoryInterface $repository
    ) {}

    public function save(RewardRule $rewardRule): RewardRule
    {
        return $this->repository->save($rewardRule);
    }

    public function fetchAll(): array
    {
        return $this->repository->fetchAll();
    }
}
