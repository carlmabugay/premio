<?php

namespace App\Domain\Rewards\Contracts;

use App\Domain\Rewards\Entities\RewardRule;

interface RewardRuleRepositoryInterface
{
    public function findActive(string $event_type): array;

    public function save(RewardRule $rewardRule): RewardRule;

    public function fetchAll(): array;
}
