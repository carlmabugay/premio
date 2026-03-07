<?php

namespace App\Domain\Rewards\Contracts\Read;

use App\Domain\Rewards\Entities\RewardRule;

interface RewardRuleReadRepositoryInterface
{
    public function findActive(string $event_type): array;

    public function fetchAll(): array;

    public function fetchById(int $id): RewardRule;
}
