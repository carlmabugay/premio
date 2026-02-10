<?php

namespace App\Domain\Rewards\Contracts;

interface RewardRuleRepositoryInterface
{
    public function findActive(): array;
}
